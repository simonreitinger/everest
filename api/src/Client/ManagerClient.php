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
use App\Entity\Task;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ManagerClient makes requests to the Contao Manager API.
 */
class ManagerClient
{
    /**
     * @var ClientInterface
     */
    private $guzzle;

    /**
     * @var int
     */
    private $startTime;

    /**
     * @var int
     */
    private $endTime;

    /**
     * ManagerClient constructor.
     *
     * @param ClientInterface $guzzle
     */
    public function __construct(ClientInterface $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * @param Installation $website
     * @param bool         $logTime
     *
     * @return ResponseInterface|null
     */
    public function homepageRequest(Installation $website, $logTime = false)
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
     * @param Installation $website
     *
     * @return ResponseInterface|null
     */
    public function serverContao(Installation $website)
    {
        return $this->apiRequest($website, '/api/server/contao');
    }

    /**
     * @param Installation $website
     *
     * @return ResponseInterface|null
     */
    public function serverComposer(Installation $website)
    {
        return $this->apiRequest($website, '/api/server/composer');
    }

    /**
     * @param Installation $website
     *
     * @return ResponseInterface|null
     */
    public function serverConfig(Installation $website)
    {
        return $this->apiRequest($website, '/api/server/config');
    }

    /**
     * @param Installation $website
     *
     * @return ResponseInterface|null
     */
    public function serverPhpWeb(Installation $website)
    {
        return $this->apiRequest($website, '/api/server/php-web');
    }

    /**
     * @param Installation $website
     *
     * @return ResponseInterface|null
     */
    public function serverPhpCli(Installation $website)
    {
        return $this->apiRequest($website, '/api/server/php-cli');
    }

    /**
     * @param Installation $website
     *
     * @return ResponseInterface|null
     */
    public function configManager(Installation $website)
    {
        return $this->apiRequest($website, '/api/config/manager');
    }

    /**
     * returns the composer.json file.
     *
     * @param Installation $website
     *
     * @return ResponseInterface|null
     */
    public function packagesRoot(Installation $website)
    {
        return $this->apiRequest($website, '/api/packages/root');
    }

    /**
     * returns the composer.lock file.
     *
     * @param Installation $website
     *
     * @return ResponseInterface|null
     */
    public function composerLock(Installation $website)
    {
        return $this->apiRequest($website, '/api/packages/local');
    }

    /**
     * @param Installation $website
     * @param Task         $task
     *
     * @return ResponseInterface|null
     */
    public function putTask(Installation $website, Task $task)
    {
        return $this->apiRequest($website, '/api/task', 'PUT', $task);
    }

    /**
     * @param Installation $website
     *
     * @return ResponseInterface|null
     */
    public function getTask(Installation $website)
    {
        return $this->apiRequest($website, '/api/task');
    }

    /**
     * @param Installation $website
     *
     * @return ResponseInterface|null
     */
    public function removeTask(Installation $website)
    {
        return $this->apiRequest($website, '/api/task', 'DELETE');
    }

    /**
     * @param ResponseInterface $response
     *
     * @return mixed
     */
    public function getJsonContent(ResponseInterface $response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param Installation $website
     * @param string       $endpoint
     * @param string       $method
     * @param mixed|null   $data
     *
     * @return ResponseInterface|null
     */
    private function apiRequest(Installation $website, string $endpoint = '', $method = 'GET', $data = null)
    {
        try {
            return $this->guzzle
                ->request($method, $website->getManagerUrl().$endpoint, $this->buildOptions($website, $data))
            ;
        } catch (GuzzleException $e) {
            echo $e->getMessage();

            return null;
        }
    }

    /**
     * @param Installation $website
     * @param null         $data
     *
     * @return array
     */
    private function buildOptions(Installation $website, $data = null)
    {
        $options = [
            'headers' => [
                'Contao-Manager-Auth' => $website->getToken(),
            ],
        ];

        if ($data) {
            $options['body'] = json_encode($data);
            $options['headers']['Content-Type'] = 'application/json';
        }

        if ($_ENV['APP_ENV'] === 'dev') {
            $options['verify'] = false;
        }

        return $options;
    }
}
