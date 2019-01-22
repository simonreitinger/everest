<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-22
 * Time: 16:31
 */

namespace App\Controller\Config;

use App\Client\ManagerClient;
use App\Controller\ApiController;
use App\Entity\Website;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConfigController
 * @package App\Controller\Config
 *
 * @Route("/config/{id}", name="website_config", methods={"GET"})
 */
class ConfigController extends ApiController
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var ManagerClient $guzzle
     */
    private $client;

    /**
     * ConfigController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, ManagerClient $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __invoke($id, Request $request)
    {
        /** @var Website $website */
        $website = $this->entityManager->getRepository('App:Website')->findOneBy(['id' => $id]);

        $response = $this->client->serverContao($website);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            $config = json_decode($response->getBody()->getContents(), true);

            $website->setVersion($config['version']);
            $website->setApi($config['api']);
            $website->setSupported($config['supported']);

            $this->entityManager->persist($website);
            $this->entityManager->flush();
        }

        return new JsonResponse($response->getBody()->getContents());
    }
}
