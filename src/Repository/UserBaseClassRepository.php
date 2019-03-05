<?php

namespace App\Repository;

use App\Entity\UserBaseClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserBaseClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserBaseClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserBaseClass[]    findAll()
 * @method UserBaseClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserBaseClassRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserBaseClass::class);
    }

    public function findByRole($role)
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%')
            ->getQuery()
            ->getResult()
            ;
    }
    // /**
    //  * @return UserBaseClass[] Returns an array of UserBaseClass objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserBaseClass
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
