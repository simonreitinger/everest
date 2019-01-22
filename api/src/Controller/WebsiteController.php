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
use Cocur\Slugify\SlugifyInterface;
use Crell\ApiProblem\ApiProblem;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\ClientInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class WebsiteController
 * @package App\Controller
 *
 * @Route("/website")
 */
class WebsiteController extends ApiController
{
    private $entityManager;

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
     * @param Request $request
     * @return JsonResponse
     */
    public function getAll(Request $request)
    {
        $websites = $this->entityManager->getRepository('App:Website')->findAll();
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
        $json = $this->getJsonContent($request);

        $website = $this->entityManager->getRepository('App:Website')->findOneBy(['url' => $json['url']]);

        if ($this->jsonIsValid($json)) {
            if (!$website) {
                $website = new Website();
                $website->setUrl($json['url']);
            }

            $website->setToken($json['token']);

            if ($response = $this->client->serverContao($website)) {
                if ($response->getStatusCode() === Response::HTTP_OK) {
                    $config = json_decode($response->getBody()->getContents(), true);

                    $website->setVersion($config['version']);
                    $website->setApi($config['api']);
                    $website->setSupported($config['supported']);
                }
            }

            $this->entityManager->persist($website);
            $this->entityManager->flush();

            $json = ['success' => true];
            return new JsonResponse($json);
        }

        return new ApiProblemResponse((new ApiProblem())->setStatus(Response::HTTP_BAD_REQUEST));
    }

    private function jsonIsValid(array $json) {
        return (
            $json['url'] &&
            $json['token']
        );
    }
}
