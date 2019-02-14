<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-02-14
 * Time: 08:35
 */

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthControllerTest extends ApiTestCase
{
    /**
     * login
     */
    public function testLogin()
    {
        $credentials = ['username' => 'test', 'password' => 'test'];

        $client = $this->jsonRequest('POST', '/auth/login', $credentials);

        /** @var Response $response */
        $response = $client->getResponse();

        $this->assertEquals($response->getStatusCode(), 200);

       return json_decode($response->getContent(), true)['token'];
    }

    /**
     * @depends testLogin
     */
    public function testRefreshToken($token)
    {
        $client = $this->jsonRequestWithToken('GET', '/auth/token', $token);

        /** @var Response $response */
        $response = $client->getResponse();

        // token can only be refreshed after a certain time (see AuthController)
        // UNAUTHORIZED is expected
        $this->assertEquals($response->getStatusCode(), 401);

        // TODO save token global
    }
}
