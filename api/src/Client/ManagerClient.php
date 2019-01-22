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
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function serverContao(Website $website)
    {
        try {
            return $this->guzzle
                ->request('GET', $website->getUrl() . '/api/server/contao', $this->authHeader($website));
        } catch (GuzzleException $e) {
            return null;
        }
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
