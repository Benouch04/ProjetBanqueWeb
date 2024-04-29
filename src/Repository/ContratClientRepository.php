<?php

namespace App\Repository;

use App\Entity\ContratClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContratClient>
 *
 * @method ContratClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContratClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContratClient[]    findAll()
 * @method ContratClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContratClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContratClient::class);
    }

    public function countContratByDate(\DateTimeInterface $startDateContrat, \DateTimeInterface $endDateContrat): int
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.dateOuvertureContrat >= :start')
            ->andWhere('c.dateOuvertureContrat <= :end')
            ->setParameter('start', $startDateContrat)
            ->setParameter('end', $endDateContrat)
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return ContratClient[] Returns an array of ContratClient objects
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

//    public function findOneBySomeField($value): ?ContratClient
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
