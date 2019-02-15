<?php

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

    public function findByInstallationId($id)
    {
        return $this->createQueryBuilder('m')
            ->where('m.installation = :id')
            ->setParameter('id', $id)
            ->orderBy('m.createdAt', 'desc')
            ->getQuery()
            ->getResult();
    }

    public function findCurrentByInstallationId($id)
    {
        return $this->findOneBy(['installation' => $id], ['createdAt' => 'desc']);
    }
}
