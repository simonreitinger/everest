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
     * @param $url
     * @return Website|null
     */
    public function findOneByUrl($url): ?Website
    {
        try
        {
            return $this->createQueryBuilder('w')
                ->where('w.url = :url')
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
