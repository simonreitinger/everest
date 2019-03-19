<?php

declare(strict_types=1);

/*
 * This file is part of Everest Monitoring.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace App\Manager;

use App\Entity\Software;
use App\Factory\VersionManagerFactory;
use App\Repository\SoftwareRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SoftwareManager
{
    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * @var array
     */
    private $software;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * SoftwareManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ParameterBagInterface  $params
     * @param ClientInterface        $client
     */
    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $params, ClientInterface $client)
    {
        $this->entityManager = $entityManager;
        $this->params = $params;
        $this->client = $client;
        $this->software = $params->get('softwares');
    }

    /**
     * @return array|null
     */
    public function update(): ?array
    {
        /** @var SoftwareRepository $softwareRepo */
        $softwareRepo = $this->entityManager->getRepository(Software::class);

        try {
            foreach ($this->software as $name => $endpoints) {
                // if software does not exist, create it
                $software = $softwareRepo->findOneByName($name);
                $manager = VersionManagerFactory::create($name);

                if (!$software) {
                    $software = (new Software())->setName($name);
                }

                foreach ($endpoints as $url) {
                    try {
                        $response = $this->client->request('GET', $url);
                        $versions = $manager->extractVersions($response);
                    } catch (GuzzleException $e) {
                    }
                }

                // duplicates of versions are possible at this point -> unique items in array
                $software->setVersions(array_unique($versions));
                $this->entityManager->persist($software);
            }

            // save all
            $this->entityManager->flush();
        } catch (\Exception $e) {
        }

        return $softwareRepo->findAll();
    }
}
