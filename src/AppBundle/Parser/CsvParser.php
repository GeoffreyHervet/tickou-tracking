<?php

namespace AppBundle\Parser;

use Symfony\Component\HttpFoundation\File\File;

class CsvParser
{
    public function parse(File $file): iterable
    {
        $reader = $file->openFile('r');
        $firstLine = $reader->getCurrentLine();
        $delimiter = $this->getDelimiter($firstLine);
        $header = iterator_to_array($this->getHeader($firstLine, $delimiter));

        while (!$reader->eof()) {
            $line = $reader->getCurrentLine();
            if (empty($line)) {
                continue;
            }

            yield iterator_to_array($this->parseLine($header, $line, $delimiter));
        }
    }

    private function getDelimiter(string $line): string
    {
        return substr_count($line, ',') > 1 ? ',' : ';';
    }

    private function parseLine(array $header, string $line, string $delimiter): iterable
    {
        $explodedLine = explode($delimiter, $line);
        foreach ($header as $i => $col) {
            yield $col => $explodedLine[$i] ?? '';
        }
    }

    public function getHeader(string $header, string $delimiter): iterable
    {
        $exploded = explode($delimiter, trim($header));

        foreach ($exploded as $column) {
            yield trim($column, "\t\n\r\0\x0B \"'");
        }
    }
}
