<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @param $website
     * @return mixed
     */
    public function findOneByWebsite($website)
    {
        try {
            return $this->createQueryBuilder('t')
                ->andWhere('t.website = :val')
                ->setParameter('val', $website)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        } catch (\Exception $e) {
            return null;
        }
    }
}
