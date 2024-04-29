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
        // Récupérer tous les motifs existants
        $motifs = $this->findAll();

        // Vérifier si 'Autre' est déjà dans la base de données
        $autreExists = false;
        foreach ($motifs as $motif) {
            if ($motif->getLibelleMotif() === 'Autre') {
                $autreExists = true;
                break;
            }
        }

        // Ajouter 'Autre' si nécessaire
        if (!$autreExists) {
            $autre = new Motif();
            $autre->setId(-1);
            // Assurez-vous d'assigner les propriétés nécessaires à l'entité 'Autre'
            $autre->setLibelleMotif('Autre');
            // Assurez-vous que l'ID que vous assignez ici ne se heurte pas aux IDs existants
            // Vous pourriez vouloir définir cet ID à une valeur qui ne serait jamais un ID valide autrement
            // Par exemple, en utilisant un nombre négatif
            $motifs[] = $autre;
        }

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
