<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-23
 * Time: 14:20
 */

namespace App\Client;

use App\Entity\Installation;
use Symfony\Component\DomCrawler\Crawler;

class InstallationCrawler extends Crawler
{

    /**
     * @var Installation $installation
     */
    private $installation;

    /**
     * InstallationCrawler constructor.
     * @param $html string (given to parent constructor)
     * @param $installation Installation (for storing data)
     */
    public function __construct($html, $installation)
    {
        parent::__construct($html);
        $this->installation = $installation;
    }

    public function analyzeMetadata()
    {
        // fetch favicon path when different
        $favicon = $this->filter('link[rel="icon"]')->first()->attr('href') ?? '';
        $this->installation->setFavicon($this->getBaseHref() . $favicon);

        // fetch title when different
        $title = $this->filter('title')->text();
        $this->installation->setTitle($title);

        // fetch theme color
        $themeColor = $this->filter('meta[name="theme-color"]')->attr('content') ?? '';
        $this->installation->setThemeColor($themeColor);
    }
}
