<?php

namespace App\Repository;

use App\Entity\Website;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Website|null find($id, $lockMode = null, $lockVersion = null)
 * @method Website|null findOneBy(array $criteria, array $orderBy = null)
 * @method Website[]    findAll()
 * @method Website[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WebsiteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Website::class);
    }

    /**
     * @param $hash
     * @return Website|null
     */
    public function findOneByHash($hash): ?Website
    {
        try
        {
            return $this->createQueryBuilder('w')
                ->where('w.hash= :hash')
                ->setParameter('hash', $hash)
                ->getQuery()
                ->getOneOrNullResult();
        }
        catch (NonUniqueResultException $e)
        {
            return null;
        }
    }

    /**
     * @param string $url
     * @return Website|null
     */
    public function findOneByUrl(string $url): ?Website
    {
        try
        {
            return $this->createQueryBuilder('w')
                ->orWhere('w.url = :url')
                ->orWhere('w.cleanUrl LIKE :url')
                ->orWhere('w.managerUrl LIKE :url')
                ->setParameter('url', $url)
                ->getQuery()
                ->getOneOrNullResult();
        }
        catch (NonUniqueResultException $e)
        {
            return null;
        }
    }
}
