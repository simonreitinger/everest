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

use App\Entity\Installation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Installation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Installation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Installation[]    findAll()
 * @method Installation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstallationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Installation::class);
    }

    /**
     * @param $hash
     *
     * @return Installation|null
     */
    public function findOneByHash($hash): ?Installation
    {
        try {
            return $this->createQueryBuilder('i')
                ->where('i.hash= :hash')
                ->setParameter('hash', $hash)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param string $url
     *
     * @return Installation|null
     */
    public function findOneByUrl(string $url): ?Installation
    {
        try {
            return $this->createQueryBuilder('i')
                ->orWhere('i.url = :url')
                ->orWhere('i.cleanUrl LIKE :url')
                ->orWhere('i.managerUrl LIKE :url')
                ->setParameter('url', $url)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function countAll()
    {
        try {
            return $this->createQueryBuilder('i')
                ->select('count(i.id)')
                ->getQuery()
                ->getSingleScalarResult()
            ;
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    public function findByLimitAndOffset($limit = 10, $offset = 0)
    {
        try {
            return $this->createQueryBuilder('i')
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ->getQuery()
                ->getResult()
            ;
        } catch (\Exception $e) {
        }
    }
}
