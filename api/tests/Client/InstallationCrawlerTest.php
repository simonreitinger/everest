<?php

declare(strict_types=1);

/*
 * This file is part of Everest Monitoring.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace App\Client;

use App\Entity\Installation;
use PHPUnit\Framework\TestCase;

class InstallationCrawlerTest extends TestCase
{
    /**
     * @var Installation
     */
    private $installation;

    /**
     * @var InstallationCrawler
     */
    private $crawler;

    protected function setUp(): void
    {
        parent::setUp();

        $html = file_get_contents(__DIR__.'/../Fixtures/Resources/crawler_test.html');

        $this->installation = new Installation();

        $this->crawler = new InstallationCrawler($html, $this->installation);
    }

    public function testAnalyzeMetadata(): void
    {
        $this->crawler->analyzeMetadata();

        $this->assertSame($this->installation->getThemeColor(), '#03a9f4');
        $this->assertSame($this->installation->getFavicon(), 'favicon.ico');
        $this->assertSame($this->installation->getTitle(), 'Crawler Test');
    }
}
