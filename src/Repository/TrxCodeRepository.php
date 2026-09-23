<?php

namespace App\Repository;

use App\Entity\HotelGroup;
use App\Entity\TrxCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrxCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrxCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrxCode[]    findAll()
 * @method TrxCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrxCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrxCode::class);
    }

    // /**
    //  * @return TrxCode[] Returns an array of TrxCode objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrxCode
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param HotelGroup $hotelGroup
     * @param string $resort
     * @param string|null $trxCode
     * @return TrxCode|null
     */
    public function findOneByHotelGroupResortTrx(HotelGroup $hotelGroup, string $resort, ?string $trxCode): ?TrxCode
    {
        return $this->createQueryBuilder('t')
            ->where('t.hotelGroup = :hotelGroup')
            ->andWhere('t.resort = :resort')
            ->andWhere('t.trxCode = :trxCode')
            ->setParameter('hotelGroup', $hotelGroup)
            ->setParameter('resort', $resort)
            ->setParameter('trxCode', $trxCode)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
