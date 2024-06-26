<?php

namespace App\Repository;

use App\Entity\Motif;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Motif>
 *
 * @method Motif|null find($id, $lockMode = null, $lockVersion = null)
 * @method Motif|null findOneBy(array $criteria, array $orderBy = null)
 * @method Motif[]    findAll()
 * @method Motif[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MotifRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Motif::class);
    }

    public function findAllIncludingAutre()
    {
        $motifs = $this->findAll();

        $autreExists = false;
        foreach ($motifs as $motif) {
            if ($motif->getLibelleMotif() === 'Autre') {
                $autreExists = true;
                break;
            }
        }
        if (!$autreExists) {
            $autre = new Motif();
            $autre->setId(-1);
            $autre->setLibelleMotif('Autre');

            $motifs[] = $autre;
        }

        // Tri des motifs par ordre alphabétique du libellé
        usort($motifs, function ($a, $b) {
            return strcmp($a->getLibelleMotif(), $b->getLibelleMotif());
        });

        return $motifs;
    }
    //    /**
//     * @return Motif[] Returns an array of Motif objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    //    public function findOneBySomeField($value): ?Motif
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
