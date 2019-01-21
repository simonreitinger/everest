<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-17
 * Time: 17:24
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiController
 * @package App\Controller
 */
class ApiController extends AbstractController
{
    public function getJsonContent(Request $request)
    {
        return json_decode($request->getContent(), true) ?? [];
    }
}
