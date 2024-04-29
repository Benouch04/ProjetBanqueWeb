<?php

namespace App\Repository;

use App\Entity\PieceJustif;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PieceJustif>
 *
 * @method PieceJustif|null find($id, $lockMode = null, $lockVersion = null)
 * @method PieceJustif|null findOneBy(array $criteria, array $orderBy = null)
 * @method PieceJustif[]    findAll()
 * @method PieceJustif[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PieceJustifRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PieceJustif::class);
    }

    public function findByNomPieceJustif($nomPieceJustif)
    {
        return $this->createQueryBuilder('p')
            ->where('p.nomPieceJustif = :nom')
            ->setParameter('nom', $nomPieceJustif)
            ->getQuery()
            ->getOneOrNullResult();
    }
    //    /**
//     * @return PieceJustif[] Returns an array of PieceJustif objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    //    public function findOneBySomeField($value): ?PieceJustif
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
