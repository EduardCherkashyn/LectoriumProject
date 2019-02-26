<?php

namespace App\Repository;

use App\Entity\HomeTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HomeTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method HomeTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method HomeTask[]    findAll()
 * @method HomeTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HomeTaskRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HomeTask::class);
    }

    // /**
    //  * @return HomeTask[] Returns an array of HomeTask objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HomeTask
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
