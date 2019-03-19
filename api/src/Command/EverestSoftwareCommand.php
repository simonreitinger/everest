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

use App\Manager\SoftwareManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EverestSoftwareCommand extends Command
{
    protected static $defaultName = 'everest:software';

    /**
     * @var SoftwareManager
     */
    private $softwareManager;

    /**
     * EverestSoftwareCommand constructor.
     *
     * @param SoftwareManager $softwareManager
     */
    public function __construct(SoftwareManager $softwareManager)
    {
        parent::__construct();
        $this->softwareManager = $softwareManager;
    }

    protected function configure(): void
    {
        $this->setDescription('Update software versions (PHP etc.)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->softwareManager->update();
    }
}
