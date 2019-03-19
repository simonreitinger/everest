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

use App\Client\ManagerClient;
use App\Entity\Installation;
use App\Entity\Task;
use App\HttpKernel\ApiProblemResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskController.
 *
 * @Route("/task")
 */
class TaskController extends ApiController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ManagerClient
     */
    private $client;

    /**
     * TaskController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ManagerClient          $client
     */
    public function __construct(EntityManagerInterface $entityManager, ManagerClient $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    /**
     * @Route(methods={"POST"})
     *
     * 200 for existing tasks, they are overwritten
     * 201 for new tasks
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function postTask(Request $request)
    {
        $json = $this->getRequestContentAsJson($request);

        $installation = $this->entityManager->getRepository(Installation::class)->findOneByUrl($json['installation']);

        $task = new Task();
        $task
            ->setName($json['name'])
            ->setConfig($json['config'])
            ->setCreatedAt(new \DateTime())
        ;

        try {
            // active tasks should not be put again, this causes a 401
            if ($task->getOutput() && $task->getOutput()['status'] === 'active') {
                return new JsonResponse($task->getOutput(), Response::HTTP_OK);
            }

            $response = $this->client->putTask($installation, $task);

            if ($response) {
                switch ($response->getStatusCode()) {
                    case Response::HTTP_OK:
                        $json = $this->client->getJsonContent($response);

                        return new JsonResponse($json, Response::HTTP_OK);

                    case Response::HTTP_BAD_REQUEST:
                        return $this->createApiProblemResponse('Task already running', Response::HTTP_BAD_REQUEST);
                }
            }
        } catch (\Exception $e) {
        }

        return $this->createApiProblemResponse('Invalid data supplied', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/{hash}", methods={"GET"})
     *
     * @param $hash
     *
     * @return ApiProblemResponse|JsonResponse|Response
     */
    public function getTaskStatus($hash)
    {
        /** @var Installation $installation */
        $installation = $this->entityManager->getRepository(Installation::class)->findOneByHash($hash);

        if ($installation) {
            $response = $this->client->getTask($installation);

            $json = $this->client->getJsonContent($response);

            if ($json['status'] === 'complete') {
                $response = $this->client->removeTask($installation);

                $process = new Process(['php bin/console everest:update-config '.$installation->getCleanUrl()], __DIR__.'/../..');
                $process->start();
                while ($process->isRunning()) {
                    sleep(1);
                }
            }

            return new JsonResponse($json);
        }

        return $this->createApiProblemResponse('Invalid hash', Response::HTTP_NO_CONTENT);
    }
}
