<?php

namespace App\Repository;

use App\Entity\FutureReservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FutureReservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method FutureReservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method FutureReservation[]    findAll()
 * @method FutureReservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FutureReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FutureReservation::class);
    }

    // /**
    //  * @return FutureReservations[] Returns an array of FutureReservations objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FutureReservations
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param $reservationId
     * @return FutureReservation|null
     * @throws NonUniqueResultException
     */
    public function findLastByReservationId($reservationId): ?FutureReservation
    {
        return $this->createQueryBuilder('r')
            ->where('r.reservationId = :reservationId')
            ->setParameter('reservationId', $reservationId)
            ->orderBy('r.created', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
