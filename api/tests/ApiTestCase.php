<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-17
 * Time: 11:02
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\HttpFoundation\Request;

class ApiTestCase extends WebTestCase
{
    /** @var Client */
    protected static $client;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$client = static::createClient();
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $payload
     * @return Client
     */
    public function jsonRequest(string $method, string $url, array $payload = null)
    {
        static::$client->request($method, $url, [], [], [], json_encode($payload));

        return static::$client;
    }

    public function jsonRequestWithToken(string $token, string $method, string $url, array $payload = null)
    {
        static::$client->request($method, $url, [], [], ['Authorization' => $token], json_encode($payload));

        return static::$client;
    }
}
