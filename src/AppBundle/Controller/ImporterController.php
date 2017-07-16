<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Fulfillment;
use AppBundle\Entity\User;
use AppBundle\Factory\FulfillmentFactory;
use AppBundle\Parser\CsvParser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class ImporterController extends Controller
{
    /**
     * @param Request $request
     * @param User $user
     *
     * @return RedirectResponse
     *
     * @Route("/application", name="app_index")
     * @Method("GET")
     */
    public function indexAction(Request $request, UserInterface $user): Response
    {
        return $this->render('AppBundle:Importer:index.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/upload", name="upload")
     */
    public function uploadCsvAction(Request $request, UserInterface $user, CsvParser $parser)
    {
        $user = $this->getDoctrine()->getManager()->find(User::class, $user->getId());
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        if (!$file) {
            return $this->renderWithError($user, 'No file uploaded');
        }

        $em = $this->getDoctrine()->getManager();
        $lineNumber = 1;
        $fulfillments = [];
        try {
            foreach ($parser->parse($file) as $line) {
                $lineNumber++;
                if (!isset($line['tracking'], $line['order'])) {
                    throw new \Exception('Tracking column or order column is missing.');
                }
                $fulfillment = FulfillmentFactory::createFromArray($line, $user);
                $fulfillments[] = $fulfillment;
                $em->persist($fulfillment);
            }
        } catch (\Exception $exception) {
            return $this->renderWithError($user, $exception);
        }

        $em->flush();
        $producer = $this->get('old_sound_rabbit_mq.fulfillment_producer');
        /** @var Fulfillment $fulfillment */
        foreach ($fulfillments as $fulfillment) {
            $producer->publish($fulfillment->getId());
        }

        return $this->render('AppBundle:Importer:index.html.twig', [
            'user' => $user,
            'success' => 'Imported '. count($fulfillments) . ' lines!',
        ]);
    }

    private function renderWithError(User $user, string $errorMessage)
    {
        return $this->render('AppBundle:Importer:index.html.twig', [
            'user' => $user,
            'error' => $errorMessage,
        ]);
    }
}
