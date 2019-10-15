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

use App\Cache\InstallationCache;
use App\Client\ManagerClient;
use App\Entity\Installation;
use App\HttpKernel\ApiProblemResponse;
use App\Manager\ConfigManager;
use App\Repository\InstallationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class InstallationController.
 *
 * @Route("/installations")
 */
class InstallationController extends ApiController
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
     * @var ManagerClient
     */
    private $client;

    /**
     * @var InstallationCache
     */
    private $cache;

    /**
     * InstallationController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ManagerClient          $client
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ManagerClient $client,
        ConfigManager $configManager,
        InstallationCache $cache
    ) {
        $this->entityManager = $entityManager;
        $this->client = $client;
        $this->configManager = $configManager;
        $this->cache = $cache;
    }

    /**
     * @Route("/", methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getAll(Request $request)
    {
        /** @var InstallationRepository $repo */
        $repo = $this->entityManager->getRepository(Installation::class);

        if ($limit = (int) $request->get('limit')) {
            $installations = $repo->findByLimitAndOffset($limit, (int) $request->get('offset') ?: 0);
        } else {
            $installations = $repo->findAll();
        }

        return new JsonResponse($this->mergeWithCache($installations));
    }

    /**
     * @Route("/count", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getCount()
    {
        $installations = $this->entityManager->getRepository(Installation::class)->countAll();

        return new JsonResponse(['total' => $installations]);
    }

    /**
     * @Route("/add", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse|ApiProblemResponse
     */
    public function add(Request $request)
    {
        // parse payload
        $json = $this->getRequestContentAsJson($request);

        $installation = $this->entityManager
            ->getRepository(Installation::class)
            ->findOneBy(['url' => $json['url']])
        ;

        // only progress with valid data
        if ($this->jsonIsValid($json)) {
            if (!$installation) {
                // set new urls
                $installation = new Installation();
                $installation
                    ->setUrl($json['url'])
                    ->setManagerUrl($json['url'])
                    ->setCleanUrl(parse_url($json['url'], PHP_URL_HOST)) // without protocol
                ;
            }

            // new token can be set without deleting the installation
            $installation->setToken($json['token']);

            // set the configuration
            $this->configManager
                ->setInstallations($installation)
                ->fetchConfig()
            ;

            $this->entityManager->persist($installation);
            $this->entityManager->flush();

            return new JsonResponse(['success' => true]);
        }

        $this->createApiProblemResponse('Invalid data', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/delete/{hash}", methods={"DELETE"})
     *
     * @param $hash
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function delete($hash, Request $request)
    {
        // parse payload
        $json = $this->getRequestContentAsJson($request);

        /** @var Installation $installation */
        $installation = $this->entityManager
            ->getRepository(Installation::class)
            ->findOneByHash($hash)
        ;

        if ($installation) {
            $installation->removeChildren($this->entityManager);

            $this->entityManager->remove($installation);
            $this->entityManager->flush();

            return new JsonResponse(['success' => true], Response::HTTP_OK);
        }

        $this->createApiProblemResponse('Invalid data', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/{hash}", methods={"GET"})
     *
     * @param mixed $hash
     *
     * @return JsonResponse
     */
    public function getOneByHash($hash)
    {
        $installation = $this->entityManager->getRepository(Installation::class)->findOneByHash($hash);

        if ($installation) {
            return new JsonResponse($this->mergeWithCache($installation));
        }

        return $this->createApiProblemResponse('Not found', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param Installation|Installation[] $installation
     *
     * @return array
     */
    public function mergeWithCache($installation)
    {
        if (\is_array($installation)) {
            $result = [];
            foreach ($installation as $i) {
                $result[] = array_merge(
                    json_decode(json_encode($i), true),
                    json_decode($this->cache->findByInstallation($i) ?? json_encode([]), true)
               );
            }

            return $result;
        }

        return array_merge(
            json_decode(json_encode($installation), true),
            json_decode($this->cache->findByInstallation($installation), true)
        );
    }

    private function jsonIsValid(array $json)
    {
        return
            $json['url'] &&
            $json['token']
        ;
    }
}
