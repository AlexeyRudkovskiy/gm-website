<?php

namespace App\Repository;

use App\Entity\PageMetaTags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PageMetaTags|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageMetaTags|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageMetaTags[]    findAll()
 * @method PageMetaTags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageMetaTagsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageMetaTags::class);
    }

    public function findByUrl(string $url)
    {
        return $this->createQueryBuilder('pmt')
            ->where('pmt.url = :url')
            ->setParameter('url', $url)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return PageMetaTags[] Returns an array of PageMetaTags objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PageMetaTags
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
