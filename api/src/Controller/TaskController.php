<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-02-11
 * Time: 15:59
 */

namespace App\Controller;

use App\Client\ManagerClient;
use App\Entity\Task;
use App\Entity\Installation;
use App\HttpKernel\ApiProblemResponse;
use App\Manager\ConfigManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskController
 * @package App\Controller
 *
 * @Route("/task")
 */
class TaskController extends ApiController
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var ManagerClient $client
     */
    private $client;

    /**
     * @var ConfigManager $configManager
     */
    private $configManager;

    /**
     * TaskController constructor.
     * @param EntityManagerInterface $entityManager
     * @param ManagerClient $client
     */
    public function __construct(EntityManagerInterface $entityManager, ManagerClient $client, ConfigManager $configManager)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
        $this->configManager = $configManager;
    }

    /**
     * @Route(methods={"POST"})
     *
     * 200 for existing tasks, they are overwritten
     * 201 for new tasks
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function postTask(Request $request)
    {
        $json = $this->getRequestContentAsJson($request);

        $installation = $this->entityManager->getRepository(Installation::class)->findOneByUrl($json['installation']);

        /** @var Task $task */
        $task = $this->entityManager->getRepository(Task::class)->findOneByInstallation($installation->getId());

        // status code is relevant for deciding if its a new / old task
        $statusCode = Response::HTTP_OK;

        if (!$task) {
            $statusCode = Response::HTTP_CREATED;
            $task = new Task();
        }

        $task->setName($json['name']);
        $task->setConfig($json['config']);
        $task->setInstallation($installation);
        $task->setCreatedAt(new \DateTime());

        try {
            // active tasks should not be put again, this causes a 401
            if ($task->getOutput() && $task->getOutput()['status'] === 'active') {
                return new JsonResponse($task->getOutput(), Response::HTTP_OK);
            }

            $response = $this->client->putTask($installation, $task);

            if ($response) {

                switch ($response->getStatusCode()) {
                    case Response::HTTP_OK:
                        $this->entityManager->persist($task);
                        $this->entityManager->flush();
                        $json = $this->client->getJsonContent($response);

                        return new JsonResponse($json, $statusCode);

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
     * @return ApiProblemResponse|JsonResponse|Response
     */
    public function getTaskStatus($hash)
    {
        /** @var Installation $installation */
        $installation = $this->entityManager->getRepository(Installation::class)->findOneByHash($hash);

        /** @var Task $task */
        $task = $this->entityManager->getRepository(Task::class)->findOneByInstallation($installation);
        if ($installation) {
            $response = $this->client->getTask($installation);

            $json = $this->client->getJsonContent($response);

            // task has to exist to complete it
            if ($task) {
                if ($json['status'] === 'complete') {
                    $response = $this->client->removeTask($installation);
                    $this->entityManager->remove($task);
                    $this->entityManager->flush();

                    $process = new Process(['php bin/console everest:update-config ' . $installation->getCleanUrl()], __DIR__ . '/../..');
                    $process->start();
                } else {
                    $task->setOutput($json);
                    $this->entityManager->persist($task);
                    $this->entityManager->flush();
                }

            }

            return new JsonResponse($json);
        }

        return $this->createApiProblemResponse('Invalid hash', Response::HTTP_NO_CONTENT);
    }
}
