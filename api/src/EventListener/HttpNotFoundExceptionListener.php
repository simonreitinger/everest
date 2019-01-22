<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-22
 * Time: 13:12
 */

namespace App\EventListener;

use App\HttpKernel\ApiProblemResponse;
use Crell\ApiProblem\ApiProblem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class HttpNotFoundExceptionListener implements EventSubscriberInterface
{

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $response = new ApiProblemResponse(
            (new ApiProblem())
                ->setStatus(Response::HTTP_UNAUTHORIZED)
                ->setTitle('Unauthorized')
        );

        $event->setResponse($response);
    }

    /*
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
//            'kernel.exception' => 'onKernelException'
        ];
    }
}
