<?php

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
     * @return Installation|null
     */
    public function findOneByHash($hash): ?Installation
    {
        try {
            return $this->createQueryBuilder('w')
                ->where('w.hash= :hash')
                ->setParameter('hash', $hash)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param string $url
     * @return Installation|null
     */
    public function findOneByUrl(string $url): ?Installation
    {
        try {
            return $this->createQueryBuilder('w')
                ->orWhere('w.url = :url')
                ->orWhere('w.cleanUrl LIKE :url')
                ->orWhere('w.managerUrl LIKE :url')
                ->setParameter('url', $url)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}