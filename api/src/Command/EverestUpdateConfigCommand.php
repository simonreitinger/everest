<?php

namespace App\Command;

use App\Entity\Website;
use App\Manager\ConfigManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EverestUpdateConfigCommand extends Command
{

    protected static $defaultName = 'everest:update-config';

    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var ConfigManager $configManager
     */
    private $configManager;

    /**
     * EverestUpdateConfigCommand constructor.
     * @param EntityManagerInterface $entityManager
     * @param ConfigManager $configManager
     */
    public function __construct(EntityManagerInterface $entityManager, ConfigManager $configManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->configManager = $configManager;
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

        $this->configManager
            ->setWebsites($websites)
            ->fetchConfig()
        ;

        $io->success('All done!');
    }
}
