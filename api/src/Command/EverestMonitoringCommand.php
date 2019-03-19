<?php

declare(strict_types=1);

/*
 * This file is part of Everest Monitoring.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace App\Command;

use App\Client\ManagerClient;
use App\Entity\Installation;
use App\Entity\Monitoring;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EverestMonitoringCommand extends Command
{
    protected static $defaultName = 'everest:monitoring';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ManagerClient
     */
    private $client;

    /**
     * EverestMonitoringCommand constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, ManagerClient $client)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Performs a request to every configured website')
            ->addArgument('url', InputArgument::OPTIONAL, 'URL of the website to be monitored')
            ->addOption('all', 'a', null, 'Perform requests to all websites')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $url = $input->getArgument('url');
        $all = $input->getOption('all');

        if ($url) {
            $site = $this->entityManager->getRepository(Installation::class)->findOneByUrl($url);
            $websites = [$site];
        } else {
            if (!$all) {
                $io->error('Please specify the URL or set the --all option.');
                exit(1);
            }
            $websites = $this->entityManager->getRepository(Installation::class)->findAll();
        }

        $progress = $io->createProgressBar(\count($websites));
        $progress->display();

        /** @var Installation $website */
        foreach ($websites as $website) {
            $response = $this->client->homepageRequest($website, true);

            $monitoring = new Monitoring();
            $monitoring
                ->setInstallation($website)
                ->setStatus($response->getStatusCode())
                ->setRequestTime($this->client->getRequestTime())
            ;

            $this->entityManager->persist($monitoring);
            $progress->setProgress($progress->getProgress() + 1);
        }
        $this->entityManager->flush();

        $io->writeln('');
        $io->writeln('');
        $io->success('All done!');
    }
}
