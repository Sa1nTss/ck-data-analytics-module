<?php

namespace App\Repository;

use App\Entity\CKGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CKGroup>
 *
 * @method CKGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method CKGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method CKGroup[]    findAll()
 * @method CKGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CKGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CKGroup::class);
    }

//    /**
//     * @return CKGroup[] Returns an array of CKGroup objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CKGroup
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
