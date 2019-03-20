<?php

declare(strict_types=1);

/*
 * This file is part of Everest Monitoring.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace App\Repository;

use App\Entity\Monitoring;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Monitoring|null find($id, $lockMode = null, $lockVersion = null)
 * @method Monitoring|null findOneBy(array $criteria, array $orderBy = null)
 * @method Monitoring[]    findAll()
 * @method Monitoring[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MonitoringRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Monitoring::class);
    }

    public function findByInstallationId($id, $limit = 100)
    {
        return $this->createQueryBuilder('m')
            ->where('m.installation = :id')
            ->setParameter('id', $id)
            ->orderBy('m.createdAt', 'desc')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findCurrentByInstallationId($id)
    {
        return $this->findOneBy(['installation' => $id], ['createdAt' => 'desc']);
    }
}
