<?php

declare(strict_types=1);

/*
 * This file is part of Everest Monitoring.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace App\HttpKernel;

use Crell\ApiProblem\ApiProblem;
use Symfony\Component\HttpFoundation\Response;

class ApiProblemResponse extends Response
{
    /**
     * ApiProblemResponse constructor.
     *
     * @param ApiProblem $apiProblem
     * @param array      $headers
     */
    public function __construct(ApiProblem $problem, array $headers = [])
    {
        if (!$problem->getStatus()) {
            $problem->setStatus(500);
        }
        if (!$problem->getTitle()) {
            $code = $problem->getStatus();
            $problem->setTitle(isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : 'unknown status');
        }
        parent::__construct(
            $problem->asJson(),
            $problem->getStatus(),
            array_merge($headers, ['Content-Type' => 'application/problem+json'])
        );
    }
}
