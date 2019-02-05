<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-17
 * Time: 15:54
 */

namespace App\Controller;

use App\Client\ManagerClient;
use App\Entity\Website;
use App\HttpKernel\ApiProblemResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WebsiteController
 * @package App\Controller
 *
 * lists and adds website to the database
 *
 * @Route("/website")
 */
class WebsiteController extends ApiController
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
     * WebsiteController constructor.
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
        $websites = $this->entityManager->getRepository(Website::class)->findAll();

        return new JsonResponse($websites);
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

        $website = $this->entityManager->getRepository(Website::class)->findOneBy(['url' => $json['url']]);

        // only progress with valid data
        if ($this->jsonIsValid($json)) {
            if (!$website) {
                // set new urls
                $website = new Website();
                $website
                    ->setUrl($json['url'])
                    ->setManagerUrl($json['url'])
                    ->setCleanUrl(parse_url($json['url'], PHP_URL_HOST)); // without protocol
            }

            $website->setToken($json['token']);

            $this->entityManager->persist($website);
            $this->entityManager->flush();

            return new JsonResponse($website);
        }

        $this->createApiProblemResponse('Invalid data', Response::HTTP_BAD_REQUEST);
    }

    private function jsonIsValid(array $json)
    {
        return (
            $json['url'] &&
            $json['token']
        );
    }
}
