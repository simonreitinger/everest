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
use App\Entity\Website;
use App\HttpKernel\ApiProblemResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
     * TaskController constructor.
     * @param EntityManagerInterface $entityManager
     * @param ManagerClient $client
     */
    public function __construct(EntityManagerInterface $entityManager, ManagerClient $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    /**
     * @Route(methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getTasks()
    {
        $tasks = $this->entityManager->getRepository(Task::class)->findAll();

        return new JsonResponse($tasks);
    }

    /**
     * @Route(methods={"POST"})
     *
     * 200 for existing tasks, they are overwritten
     * 201 for new tasks
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function postTask(Request $request)
    {
        $json = $this->getRequestContentAsJson($request);

        $website = $this->entityManager->getRepository(Website::class)->findOneByUrl($json['website']);

        $task = $this->entityManager->getRepository(Task::class)->findOneByWebsite($website->getId());

        // status code is relevant for deciding if its a new / old task
        $statusCode = Response::HTTP_OK;

        if (!$task) {
            $statusCode = Response::HTTP_CREATED;
            $task = new Task();
        }

        $task->setName($json['name']);
        $task->setConfig($json['config']);
        $task->setWebsite($website);
        try {
            $task->setCreatedAt(new \DateTime());
        } catch (\Exception $e) {
        }

        try {
            // active tasks should not be put again, this causes a 401
            if ($task->getOutput() && $task->getOutput()['status'] === 'active') {
                return new JsonResponse($task->getOutput(), Response::HTTP_OK);
            }

            $response = $this->client->putTask($website, $task);

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
     * @return ApiProblemResponse|JsonResponse
     */
    public function getTaskStatus($hash)
    {
        /** @var Website $website */
        $website = $this->entityManager->getRepository(Website::class)->findOneByHash($hash);

        /** @var Task $task */
        $task = $this->entityManager->getRepository(Task::class)->findOneByWebsite($website);
        if ($website && $task) {
            $response = $this->client->getTask($website);

            $json = $this->client->getJsonContent($response);

            if ($json['status'] === 'complete') {
                $response = $this->client->removeTask($website);
                $this->entityManager->remove($task);

                $this->forward(ConfigController::class, ['hash', $hash]);

            } else {
                $task->setOutput($json);
                $this->entityManager->persist($task);
            }

            $this->entityManager->flush();

            return new JsonResponse($json);
        }

        if (!$website) {
            return $this->createApiProblemResponse('Invalid hash');
        }

        return $this->createApiProblemResponse('', Response::HTTP_NO_CONTENT);
    }
}
