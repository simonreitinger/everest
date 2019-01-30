<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-24
 * Time: 10:30
 */

namespace App\Controller;

use App\Client\ManagerClient;
use App\Entity\Monitoring;
use App\Entity\Website;
use App\HttpKernel\ApiProblemResponse;
use App\Repository\MonitoringRepository;
use App\Repository\WebsiteRepository;
use Crell\ApiProblem\ApiProblem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MonitoringController
 * @package App\Controller
 *
 * adds and lists uptime datasets
 *
 * @Route("/monitoring")
 */
class MonitoringController extends AbstractController
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
     * MonitoringController constructor.
     * @param ManagerClient $client
     */
    public function __construct(EntityManagerInterface $entityManager, ManagerClient $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    /**
     * @Route("/{hash}", methods={"POST"});
     *
     * @param $hash
     * @return JsonResponse|ApiProblemResponse
     */
    public function add($hash)
    {
        /** @var WebsiteRepository $websiteRepo */
        $websiteRepo = $this->entityManager->getRepository(Website::class);
        $website = $websiteRepo->findOneByHash($hash);

        if ($website) {
            // perform the request
            $response = $this->client->homepageRequest($website, true);

            $monitoring = new Monitoring();
            $monitoring
                ->setWebsite($website)
                ->setStatus($response->getStatusCode())
                ->setRequestTime($this->client->getRequestTime());

            $this->entityManager->persist($monitoring);
            $this->entityManager->flush();

            return new JsonResponse(['success' => true]);
        }

        return new ApiProblemResponse((new ApiProblem())->setStatus(Response::HTTP_BAD_REQUEST));
    }

    /**
     * @Route("/{hash}/current", methods={"GET"})
     *
     * @param $hash
     * @return JsonResponse|ApiProblemResponse
     */
    public function currentStatusByHash($hash)
    {
        /** @var WebsiteRepository $websiteRepo */
        $websiteRepo = $this->entityManager->getRepository(Website::class);
        $website = $websiteRepo->findOneByHash($hash);

        if ($website) {
            /** @var MonitoringRepository $monitoringRepo */
            $monitoringRepo = $this->entityManager->getRepository(Monitoring::class);
            $monitoring = $monitoringRepo->findCurrentByWebsiteId($website->getId());

            return new JsonResponse($monitoring);
        }

        return new ApiProblemResponse((new ApiProblem())->setStatus(Response::HTTP_BAD_REQUEST));
    }

    /**
     * @Route("/{hash}", methods={"GET"});
     *
     * @param $hash
     * @return JsonResponse|ApiProblemResponse
     */
    public function listForOne($hash, Request $request)
    {
        /** @var WebsiteRepository $websiteRepo */
        $websiteRepo = $this->entityManager->getRepository(Website::class);
        $website = $websiteRepo->findOneByHash($hash);

        if ($website) {
            /** @var MonitoringRepository $monitoringRepo */
            $monitoringRepo = $this->entityManager->getRepository(Monitoring::class);
            $monitoring = $monitoringRepo->findByWebsiteId($website->getId()) ?? [];

            return new JsonResponse($monitoring);
        }

        return new ApiProblemResponse((new ApiProblem())->setStatus(Response::HTTP_BAD_REQUEST));
    }
}
