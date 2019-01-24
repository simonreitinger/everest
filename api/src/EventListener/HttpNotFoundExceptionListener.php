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
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class HttpNotFoundExceptionListener implements EventSubscriberInterface
{

    /**
     * @var LoggerInterface $logger
     */
    private $logger;

    /**
     * HttpNotFoundExceptionListener constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $this->logger->alert($event->getException()->getMessage());

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
