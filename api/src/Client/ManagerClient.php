<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-22
 * Time: 21:30
 */

namespace App\Client;

use App\Entity\Website;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class ManagerClient makes requests to the Contao Manager API
 * @package App\Client
 */
class ManagerClient
{
    /**
     * @var ClientInterface $guzzle
     */
    private $guzzle;

    /**
     * ManagerClient constructor.
     * @param ClientInterface $guzzle
     */
    public function __construct(ClientInterface $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * @param Website $website
     * @param string $endpoint
     * @param string $method
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    private function apiRequest(Website $website, string $endpoint = '', $method = 'GET')
    {
        try {
            return $this->guzzle
                ->request($method, $website->getManagerUrl() . $endpoint, $this->authHeader($website));
        } catch (GuzzleException $e) {
            return null;
        }
    }

    /**
     * @param Website $website
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    private function homepageRequest(Website $website)
    {
        try {
            return $this->guzzle->request('GET', $website->getUrl());
        } catch (GuzzleException $e) {
            return null;
        }
    }

    /**
     * @param Website $website
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function fetchMetadata(Website $website)
    {
        return $this->homepageRequest($website);
    }

    /**
     * @param Website $website
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function serverContao(Website $website)
    {
        return $this->apiRequest($website, '/api/server/contao');
    }

    /**
     * @param Website $website
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function serverComposer(Website $website)
    {
        return $this->apiRequest($website, '/api/server/composer');
    }

    /**
     * @param Website $website
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function serverConfig(Website $website)
    {
        return $this->apiRequest($website, '/api/server/config');
    }

    /**
     * @param Website $website
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function serverPhpWeb(Website $website)
    {
        return $this->apiRequest($website, '/api/server/php-web');
    }

    /**
     * @param Website $website
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function serverPhpCli(Website $website)
    {
        return $this->apiRequest($website, '/api/server/php-cli');
    }

    /**
     * @param Website $website
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function configManager(Website $website)
    {
        return $this->apiRequest($website, '/api/config/manager');
    }

    /**
     * returns the composer.json file
     *
     * @param Website $website
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function packagesRoot(Website $website)
    {
        return $this->apiRequest($website, '/api/packages/root');
    }

    /**
     * @param Website $website
     * @return array
     */
    private function authHeader(Website $website)
    {
        return [
            'headers' => [
                'Contao-Manager-Auth' => $website->getToken()
            ]
        ];
    }
}
