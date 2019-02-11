<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-22
 * Time: 21:30
 */

namespace App\Client;

use App\Entity\Task;
use App\Entity\Website;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

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
     * @var int $startTime
     */
    private $startTime;

    /**
     * @var int $endTime
     */
    private $endTime;

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
    private function apiRequest(Website $website, string $endpoint = '', $method = 'GET', $data = null)
    {
        try {
            return $this->guzzle
                ->request($method, $website->getManagerUrl() . $endpoint, $this->buildOptions($website, $data));
        } catch (GuzzleException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    /**
     * @param Website $website
     * @param bool $logTime
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function homepageRequest(Website $website, $logTime = false)
    {
        try {
            if ($logTime) {
                $this->startTime = microtime(true);
            }

            $response = $this->guzzle->request('GET', $website->getUrl());

            if ($logTime) {
                $this->endTime = microtime(true);
            }

            return $response;

        } catch (GuzzleException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    /**
     * @return int
     */
    public function getRequestTime()
    {
        if ($this->startTime && $this->endTime) {
            return round(($this->endTime - $this->startTime) * 1000);
        }

        return 0;
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
     * returns the composer.lock file
     *
     * @param Website $website
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function composerLock(Website $website)
    {
        return $this->apiRequest($website, '/api/packages/local');
    }

    /**
     * @param Website $website
     * @param Task $task
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function putTask(Website $website, Task $task)
    {
        return $this->apiRequest($website, '/api/task', 'PUT', $task);
    }

    /**
     * @param Website $website
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function getTask(Website $website)
    {
        return $this->apiRequest($website, '/api/task');
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public function getJsonContent(ResponseInterface $response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param Website $website
     * @return array
     */
    private function buildOptions(Website $website, $data = null)
    {
        $options = [
            'headers' => [
                'Contao-Manager-Auth' => $website->getToken()
            ]
        ];

        if ($data) {
            $options['body'] = json_encode($data);
            $options['headers']['Content-Type'] = 'application/json';
        }

        return $options;
    }
}
