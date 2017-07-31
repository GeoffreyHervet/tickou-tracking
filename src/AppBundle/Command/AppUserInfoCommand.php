<?php

namespace AppBundle\Command;

use AppBundle\Bridge\ShopifyBridge;
use AppBundle\Entity\User;
use AppBundle\Notifier\SlackNotifier;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppUserInfoCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:user:info')
            ->setDescription('Get user details')
            ->addArgument('shop-name', InputArgument::REQUIRED, 'Shop name (eg: shopizy-demo.myshopify.com)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $shop = $input->getArgument('shop-name');
        $repo =$this->getContainer()->get('doctrine')->getManager()->getRepository(User::class);
        $user = $repo->findOneBy(['shop' => $shop]);

        if (!$user) {
            $output->writeln('<error>No user found.</error>');
        }

        $this->getContainer()->get(SlackNotifier::class)->notifyNewClient($user);
    }

}
