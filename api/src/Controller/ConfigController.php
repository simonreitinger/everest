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

use App\Entity\Installation;
use App\Manager\ConfigManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConfigController.
 *
 * @Route("/config/{hash}")
 */
class ConfigController extends ApiController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ConfigManager
     */
    private $configManager;

    /**
     * ConfigController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ConfigManager          $configManager
     */
    public function __construct(EntityManagerInterface $entityManager, ConfigManager $configManager)
    {
        $this->entityManager = $entityManager;
        $this->configManager = $configManager;
    }

    /**
     * update config for specific installation.
     *
     * @param $hash
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke($hash, Request $request)
    {
        $installation = $this->entityManager->getRepository(Installation::class)->findOneByHash($hash);

        if ($installation) {
            $this->configManager
                ->setInstallations([$installation])
                ->fetchConfig()
            ;

            return new JsonResponse($installation);
        }

        return $this->createApiProblemResponse();
    }
}
