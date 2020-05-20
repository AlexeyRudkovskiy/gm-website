<?php

namespace App\Repository;

use App\Entity\ContactRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method ContactRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactRequest[]    findAll()
 * @method ContactRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactRequest::class);
    }

    public function getNewRequestsCount()
    {
        return $this->createQueryBuilder('contact_request')
            ->select('count(contact_request.id)')
            ->where('contact_request.status = :status')
            ->setParameter('status', 'new')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function paginate(PaginatorInterface $paginator, Request $request)
    {
        $query = $this->createQueryBuilder('contact_request')
            ->orderBy('contact_request.id', 'DESC');

        return $paginator->paginate($query, $request->query->getInt('page', 1), 20);
    }

    // /**
    //  * @return ContactRequest[] Returns an array of ContactRequest objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ContactRequest
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
