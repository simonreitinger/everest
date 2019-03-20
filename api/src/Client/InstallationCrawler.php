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
use Symfony\Component\DomCrawler\Crawler;

class InstallationCrawler extends Crawler
{
    /**
     * @var Installation
     */
    private $installation;

    /**
     * InstallationCrawler constructor.
     *
     * @param $html string (given to parent constructor)
     * @param $installation Installation (for storing data)
     */
    public function __construct($html, $installation)
    {
        parent::__construct($html);
        $this->installation = $installation;
    }

    public function analyzeMetadata(): void
    {
        // fetch favicon path when different
        try {
            $favicon = $this->filter('link[rel="icon"]')->first()->attr('href');
        } catch (\InvalidArgumentException $e) {
            $favicon = '';
        }
        $this->installation->setFavicon($this->getBaseHref().$favicon);

        // fetch title when different
        $title = $this->filter('title')->text();
        $this->installation->setTitle($title);

        // fetch theme color
        try {
            $themeColor = $this->filter('meta[name="theme-color"]')->attr('content') ?? '';
        } catch (\InvalidArgumentException $e) {
            $themeColor = '';
        }
        $this->installation->setThemeColor($themeColor);
    }
}
