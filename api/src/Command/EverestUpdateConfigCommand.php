<?php

namespace App\Command;

use App\Client\ManagerClient;
use App\Client\WebsiteCrawler;
use App\Entity\Website;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Response;

class EverestUpdateConfigCommand extends Command
{
    protected static $defaultName = 'everest:update-config';

    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var ManagerClient $client
     */
    private $client;

    /**
     * EverestUpdateConfigCommand constructor.
     * @param EntityManagerInterface $entityManager
     * @param ManagerClient $client
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ManagerClient $client
    )
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    protected function configure()
    {
        $this
            ->setDescription('Update script for all registered websites')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $websites = $this->entityManager->getRepository(Website::class)->findAll();
        /** @var Website $website */
        foreach ($websites as $website) {
            $this->updateConfig($website, $io);
        }

        // flush everything at once
        $this->entityManager->flush();

        $io->writeln('');
        $io->writeln('');
        $io->success('All done!');
    }

    private function updateConfig(Website $website, SymfonyStyle $io): void
    {
        // set the configuration requests
        $responses = [
            'setContao' => 'serverContao',
            'setComposer' => 'serverComposer',
            'setConfig' => 'serverConfig',
            'setPhpWeb' => 'serverPhpWeb',
            'setPhpCli' => 'serverPhpCli',
            'setManager' => 'configManager',
            'setPackages' => 'packagesRoot'
        ];

        // create a progress bar for every website request set
        $progress = $io->createProgressBar(7);
        $io->writeln('');
        $io->writeln($website->getCleanUrl());
        $progress->display();

        foreach ($responses as $set => $method) {
            /** @var Psr7Response $response */
            $response = $this->client->{$method}($website);

            //$io->writeln(sprintf('Performing %s request for %s...', $set, $website->getCleanUrl()));
            if ($response->getStatusCode() === Response::HTTP_OK) {
                // decode into array for database
                $json = json_decode($response->getBody()->getContents(), true);

                // skip hosting providers, they are not needed for now
                if ($set === 'setConfig') {
                    unset($json['configs']);
                }

                // use the keys from $responses for setting the received json
                $website->{$set}($json);

                // update progress bar
                $progress->setProgress($progress->getProgress() + 1);

                continue;
            }

            $io->error(sprintf('Request %s for %s failed!', $set, $website->getCleanUrl()));
        }

        // metadata
        $metadataResponse = $this->client->homepageRequest($website);
        (new WebsiteCrawler($metadataResponse->getBody()->getContents(), $website))->analyzeMetadata();

        $website->setLastUpdate();
        $this->entityManager->persist($website);
    }
}
