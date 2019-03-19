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
use App\Entity\Monitoring;
use App\HttpKernel\ApiProblemResponse;
use App\Repository\InstallationRepository;
use App\Repository\MonitoringRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MonitoringController.
 *
 * @Route("/monitoring")
 */
class MonitoringController extends ApiController
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
     * MonitoringController constructor.
     *
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
     *
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
     *
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
