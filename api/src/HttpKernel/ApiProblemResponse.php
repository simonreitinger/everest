<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-17
 * Time: 11:44
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
     * @param array $headers
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
