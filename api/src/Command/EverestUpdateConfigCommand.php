<?php

namespace App\Command;

use App\Client\ManagerClient;
use App\Client\WebsiteCrawler;
use App\Entity\Website;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
     * map properties to client methods
     *
     * @var array $responses
     */
    private $responses = [
        'setContao' => 'serverContao',
        'setComposer' => 'serverComposer',
        'setConfig' => 'serverConfig',
        'setPhpWeb' => 'serverPhpWeb',
        'setPhpCli' => 'serverPhpCli',
        'setManager' => 'configManager',
        'setPackages' => 'packagesRoot',
        'setLock' => 'composerLock' // has to be after packagesRoot
    ];

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
            ->addArgument('url', InputArgument::OPTIONAL, 'URL of the website to be updated')
            ->addOption('all', 'a', null, 'Update config data for all websites');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $url = $input->getArgument('url');
        $all = $input->getOption('all');

        if ($url) {
            $site = $this->entityManager->getRepository(Website::class)->findOneByUrl($url);
            $websites = [$site];

        } else {
            if (!$all) {
                $io->error('Please specify the URL or set the --all option.');
                exit(1);
            }
            $websites = $this->entityManager->getRepository(Website::class)->findAll();
        }

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
        // create a progress bar for every website request set
        $progress = $io->createProgressBar(count($this->responses));
        $io->writeln('');
        $io->writeln($website->getCleanUrl());
        $progress->display();

        foreach ($this->responses as $set => $method) {
            /** @var Psr7Response $response */
            $response = $this->client->{$method}($website);

            //$io->writeln(sprintf('Performing %s request for %s...', $set, $website->getCleanUrl()));
            if ($response->getStatusCode() === Response::HTTP_OK) {
                // decode into array for database
                $json = $this->client->getJsonContent($response);

                // skip hosting providers, they are not needed for now
                if ($set === 'setConfig') {
                    unset($json['configs']);
                }

                if ($set === 'setLock') {
                    $json = $this->buildLockData($json, $website->getPackages());
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

    private function buildLockData(array $json, array $packages)
    {
        $filtered = [];

        // root repository should include a "/" to filter php and ext versions
        $rootRepositories = array_filter(array_keys($packages['require']), function($name) {
            return stripos($name, '/');
        });

        $privateRepositories = array_map(function($repo) {
            return $repo['url'];
        }, $packages['repositories']);

        // sort packages in alphabetical order
        ksort($json);

        foreach ($json as $package => $data) {
            $name = $json[$package]['name'];

            $filtered[] = [
                'name' => $name,
                'version' => $json[$package]['version'],
                'versionNormalized' => $json[$package]['version_normalized'],
                'description' => $json[$package]['description'] ?? '',
                'inRoot' => $this->packageFoundInArray($name, $rootRepositories),
                'rootVersion' => $packages['require'][$name] ?? '',
                'isPrivate' => $this->packageFoundInArray($name, $privateRepositories),
            ];
        }

        usort($filtered, function($a, $b) {
            return ($a['name'] < $b['name']) ? -1 : 1;
        });

        return $filtered;
    }

    /**
     * searches $repositories for occurrences of $name
     *
     * @param $name
     * @param array $repositories
     * @return bool
     */
    private function packageFoundInArray($name, array $repositories = [])
    {
        foreach ($repositories as $repository) {
            if (stripos($repository, $name)) {
                return true;
            }
        }

        return false;
    }
}
