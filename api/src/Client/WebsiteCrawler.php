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

    public function fetchMetadata()
    {
        // fetch favicon path when different
        $this->filter('link[rel="icon"]')->reduce(function(Crawler $node) {
            $favicon = $this->getBaseHref() . $node->attr('href');
            if (
                strpos($node->attr('rel'), 'icon') !== false &&
                strpos($node->attr('href'), 'favicon') !== false &&
                $favicon !== $this->website->getFavicon()
            ) {
                $this->website->setFavicon($favicon);
                return;
            }
        });

        // fetch title when different
        $this->filter('title')->reduce(function(Crawler $node) {
            if (
                $this->website->getTitle() !== $node->text() &&
                $node->text() !== $this->website->getTitle()
            ) {
                $this->website->setTitle($node->text());
                return;
            }
        });
    }
}
