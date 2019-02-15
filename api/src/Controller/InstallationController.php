<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-17
 * Time: 15:54
 */

namespace App\Controller;

use App\Client\ManagerClient;
use App\Entity\Installation;
use App\HttpKernel\ApiProblemResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class InstallationController
 * @package App\Controller
 *
 * lists and adds installation to the database
 *
 * @Route("/installation")
 */
class InstallationController extends ApiController
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
     * InstallationController constructor.
     * @param EntityManagerInterface $entityManager
     * @param ManagerClient $client
     */
    public function __construct(EntityManagerInterface $entityManager, ManagerClient $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    /**
     * @Route("/all", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getAll()
    {
        $installations = $this->entityManager->getRepository(Installation::class)->findAll();

        return new JsonResponse($installations);
    }

    /**
     * @Route("/add", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse|ApiProblemResponse
     */
    public function add(Request $request)
    {
        $json = $this->getRequestContentAsJson($request);

        $installation = $this->entityManager->getRepository(Installation::class)->findOneBy(['url' => $json['url']]);

        // only progress with valid data
        if ($this->jsonIsValid($json)) {
            if (!$installation) {
                // set new urls
                $installation = new Installation();
                $installation
                    ->setUrl($json['url'])
                    ->setManagerUrl($json['url'])
                    ->setCleanUrl(parse_url($json['url'], PHP_URL_HOST)); // without protocol
            }

            $installation->setToken($json['token']);

            $this->entityManager->persist($installation);
            $this->entityManager->flush();

            return new JsonResponse($installation, Response::HTTP_CREATED);
        }

        $this->createApiProblemResponse('Invalid data', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/{hash}", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getOneByHash($hash)
    {
        $installation = $this->entityManager->getRepository(Installation::class)->findOneByHash($hash);

        if ($installation) {
            return new JsonResponse($installation);
        }

        return $this->createApiProblemResponse('Not found', Response::HTTP_BAD_REQUEST);
    }

    private function jsonIsValid(array $json)
    {
        return (
            $json['url'] &&
            $json['token']
        );
    }
}
