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

use App\Entity\Software;
use App\Manager\SoftwareManager;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SoftwareController.
 *
 * @Route("/software")
 */
class SoftwareController extends ApiController
{
    /**
     * @var SoftwareManager
     */
    private $softwareManager;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SoftwareController constructor.
     *
     * @param ClientInterface $client
     */
    public function __construct(
        SoftwareManager $softwareManager,
        EntityManagerInterface $entityManager)
    {
        $this->softwareManager = $softwareManager;
        $this->entityManager = $entityManager;
    }

    /**
     * sets supported / maintained versions of softwares that can be defined in services.yaml.
     *
     * @Route("/update", methods={"GET"})
     *
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function updateSoftwares()
    {
        return new JsonResponse($this->softwareManager->update());
    }

    /**
     * @Route(methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getSoftwares()
    {
        $softwares = $this->entityManager->getRepository(Software::class)->findAll() ?? [];

        return new JsonResponse($softwares);
    }

    /**
     * @Route("/{name}/versions", methods={"GET"})
     *
     * @param mixed $name
     *
     * @return JsonResponse
     */
    public function getVersionsByName($name)
    {
        $software = $this->entityManager->getRepository(Software::class)->findOneByName($name);

        return new JsonResponse($software->getVersions());
    }
}
