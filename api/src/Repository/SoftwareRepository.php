<?php

namespace App\Repository;

use App\Entity\Software;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Software|null find($id, $lockMode = null, $lockVersion = null)
 * @method Software|null findOneBy(array $criteria, array $orderBy = null)
 * @method Software[]    findAll()
 * @method Software[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SoftwareRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Software::class);
    }

    /**
     * returns the record with the specific name, e.g. 'php'
     *
     * @param $name
     * @return Software|null
     */
    public function findOneByName($name)
    {
        return $this->findOneBy(['name' => $name]);
    }
}
