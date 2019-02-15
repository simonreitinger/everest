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
use App\Entity\Installation;
use App\HttpKernel\ApiProblemResponse;
use App\Repository\MonitoringRepository;
use App\Repository\InstallationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
class MonitoringController extends ApiController
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
     * @Route("/{hash}/current", methods={"GET"})
     *
     * @param $hash
     * @return JsonResponse|ApiProblemResponse
     */
    public function currentStatusByHash($hash)
    {
        /** @var InstallationRepository $installationRepo */
        $installationRepo = $this->entityManager->getRepository(Installation::class);
        $installation = $installationRepo->findOneByHash($hash);
        if ($installation) {
            /** @var MonitoringRepository $monitoringRepo */
            $monitoringRepo = $this->entityManager->getRepository(Monitoring::class);
            $monitoring = $monitoringRepo->findCurrentByInstallationId($installation->getId());

            return new JsonResponse($monitoring);
        }

        $this->createApiProblemResponse('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{hash}", methods={"GET"});
     *
     * @param $hash
     * @return JsonResponse|ApiProblemResponse
     */
    public function listForOne($hash)
    {
        /** @var InstallationRepository $installationRepo */
        $installationRepo = $this->entityManager->getRepository(Installation::class);
        $installation = $installationRepo->findOneByHash($hash);
        if ($installation) {
            /** @var MonitoringRepository $monitoringRepo */
            $monitoringRepo = $this->entityManager->getRepository(Monitoring::class);
            $monitoring = $monitoringRepo->findByInstallationId($installation->getId()) ?? [];

            return new JsonResponse($monitoring);
        }

        $this->createApiProblemResponse('Invalid hash', Response::HTTP_BAD_REQUEST);
    }
}
