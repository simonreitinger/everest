<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-23
 * Time: 14:20
 */

namespace App\Client;

use App\Entity\Website;
use Symfony\Component\DomCrawler\Crawler;

class WebsiteCrawler extends Crawler
{

    /**
     * @var Website $website
     */
    private $website;

    /**
     * WebsiteCrawler constructor.
     * @param $html string (given to parent constructor)
     * @param $website Website (for storing data)
     */
    public function __construct($html, $website)
    {
        parent::__construct($html);
        $this->website = $website;
    }

    public function analyzeMetadata()
    {
        // fetch favicon path when different
        $favicon = $this->filter('link[rel="icon"]')->first()->attr('href') ?? '';
        $this->website->setFavicon($this->getBaseHref() . $favicon);

        // fetch title when different
        $title = $this->filter('title')->text();
        $this->website->setTitle($title);

        // fetch theme color
        $themeColor = $this->filter('meta[name="theme-color"]')->attr('content') ?? '';
        $this->website->setThemeColor($themeColor);
    }
}
