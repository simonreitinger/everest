<?php

declare(strict_types=1);

/*
 * This file is part of Everest Monitoring.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace App\Controller;

use App\HttpKernel\ApiProblemResponse;
use Crell\ApiProblem\ApiProblem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiController provides often used methods for all controllers.
 */
class ApiController extends AbstractController
{
    /**
     * returns the authorization header without "Bearer".
     *
     * @param Request $request
     *
     * @return string
     */
    public function getJwtFromRequest(Request $request)
    {
        $auth = $request->headers->get('Authorization');

        if (\is_string($auth) && stripos($auth, 'bearer ') === 0) {
            // remove 'bearer ' -> 7 characters
            return substr($auth, 7);
        }

        return $auth ?? '';
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function getRequestContentAsJson(Request $request)
    {
        return json_decode($request->getContent(), true);
    }

    public function createApiProblemResponse($title = '', int $status = 401)
    {
        return new ApiProblemResponse((new ApiProblem($title))->setStatus($status));
    }
}
