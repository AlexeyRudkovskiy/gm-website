<?php

namespace App\Repository;

use App\Entity\MetaTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MetaTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetaTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetaTag[]    findAll()
 * @method MetaTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetaTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MetaTag::class);
    }

    // /**
    //  * @return MetaTag[] Returns an array of MetaTag objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MetaTag
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
