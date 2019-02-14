<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-02-14
 * Time: 08:37
 */

namespace App\Tests\Controller;

use App\Controller\WebsiteController;
use App\Entity\Website;
use App\Tests\ApiTestCase;

class WebsiteControllerTest extends ApiTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    public function testAdd()
    {
        $data = json_encode(['url' => 'contao.test', 'token' => '']);

        static::$client->request('POST', '/website/add');
    }

    public function testGetOneByHash()
    {

    }

    public function testGetAll()
    {

    }
}
