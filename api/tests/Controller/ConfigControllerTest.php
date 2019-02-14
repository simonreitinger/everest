<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-02-14
 * Time: 08:35
 */

namespace App\Tests\Controller;

use App\Controller\ConfigController;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ConfigControllerTest
 * @package App\Tests\Controller
 */
class ConfigControllerTest extends ApiTestCase
{

    public function test__invoke()
    {
        $token = '';
        $client = $this->jsonRequestWithToken('GET', '/config/1120a44f2bfbb1cb20d6c61e4d81ee3de36df4aed2b6689541d8ef6b459f63ca', $token);

        /** @var Response $response */
        $response = $client->getResponse();

        $this->assertEquals($response->getStatusCode(), 200);
    }
}
