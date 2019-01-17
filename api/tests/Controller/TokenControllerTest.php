<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-17
 * Time: 10:23
 */

namespace App\Controller;

use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class TokenControllerTest extends ApiTestCase
{
    public function testPostInvalid()
    {
        $payload = ['username' => 'test', 'password' => '87654321'];

        /** @var Response $response */
        $response = $this->jsonRequest('POST', '/api/token', $payload)->getResponse();

        $this->assertEquals(400, $response->getStatusCode());

        $this->assertArrayHasKey('title', json_decode($response->getContent(), true));
    }

    public function testPost()
    {
        $payload = ['username' => 'simon', 'password' => '12345678'];

        /** @var Response $response */
        $response = $this->jsonRequest('POST', '/api/token', $payload)->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $payload = json_decode($response->getContent(), true);

        $token = $response->headers->get('Authorization');

        $this->assertArrayHasKey('username', $payload);
        $this->assertNotEmpty($token);

        return $token;
    }

    /**
     * @depends testPost
     */
    public function testGet(string $token)
    {
        $this->jsonRequestWithToken($token, 'GET', '/api/token');
        echo "<pre>";
        print_r(static::$client->getResponse());
        exit;
    }
}
