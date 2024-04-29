<?php

namespace App\Controller;

use App\Entity\Contrat;
use App\Entity\Motif;
use App\Entity\PieceJustif;
use App\Entity\Users;
use App\Entity\Calendar;
use App\Form\PieceJustifType;
use App\Repository\PieceJustifRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/directeur')]
class PieceJustifController extends AbstractController
{
    #[Route('/piece/ajout', name: 'ajout_piece')]
    public function ajoutPiece(Request $request, EntityManagerInterface $entityManager, PieceJustifRepository $pieceJustifRepository)
    {
        $piece = new PieceJustif();
        $form = $this->createForm(PieceJustifType::class, $piece);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $existingPiece = $pieceJustifRepository->findByNomPieceJustif($piece->getNomPieceJustif());
            if ($existingPiece) {
                $this->addFlash('danger', 'Une pièce justificative avec le même nom existe déjà.');
                return $this->redirectToRoute('ajout_piece');
            }
            $entityManager->persist($piece);
            $entityManager->flush();

            return $this->redirectToRoute('app_directeur');
        }

        return $this->render('piece_justif/index.html.twig', [
            'form' => $form->createView(),
            'nomPieceJustif' => $piece->getNomPieceJustif()
        ]);
    }
    #[Route("/piece/edit/{id}", name: "piece_edit")]
    public function editPiece(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $piece = $entityManager->getRepository(PieceJustif::class)->find($id);

        $form = $this->createForm(PieceJustifType::class, $piece);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour la piece
            $piece->setNomPieceJustif($form->get('nomPieceJustif')->getData());
            $entityManager->flush();

            $this->addFlash('success', 'Les informations du nom de la pièce ont été modifiées avec succès.');

            return $this->redirectToRoute('piece_list');
        }

        return $this->render('piece_justif/edit.html.twig', [
            'pieces' => $piece,
            'form' => $form->createView(),
        ]);
    }
    #[Route("/piece/delete/{id}", name: "piece_delete", methods: ["POST"])]
    public function deletePiece(int $id, EntityManagerInterface $entityManager): Response
    {
        $piece = $entityManager->getRepository(PieceJustif::class)->find($id);

        if (!$piece) {
            // Handle the case where the piece does not exist
            $this->addFlash('error', 'Piece non trouvé');
            return $this->redirectToRoute('piece_list');
        }

        $entityManager->remove($piece);
        $entityManager->flush();

        // Add a flash message or some kind of notification to let the piece know it was successful
        $this->addFlash('success', 'Pièce supprimé avec succès');

        return $this->redirectToRoute('piece_list');
    }
    #[Route('/piece/list', name: 'piece_list')]
    public function listPieces(EntityManagerInterface $entityManager): Response
    {
        return $this->redirectToRoute('app_directeur');
    }
    #[Route("/choix-pj/{id}", name: "choixPJ")]
    public function choixPJ(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        //$piece = $entityManager->getRepository(PieceJustif::class)->find($id);
        $piece = $entityManager->getRepository(PieceJustif::class)->findAll();
        $motif = $entityManager->getRepository(Motif::class)->find($id);
        

        return $this->render('piece_justif/choixPJ.html.twig', [
            'id' => $id,
            'pieces' => $piece,
            'motif' => $motif

        ]);
    }
    /*#[Route('/traitement-choix-pj/{id}', name: 'traitement_choix_pj')]
    public function traitementChoixPJ(Request $request, $id): Response
    {
        // Récupérez les données soumises avec $request->request->get('pieces')
        $selectedPieces = $request->request->get('pieces');

        // Traitez les données sélectionnées ici...

        // Ensuite, redirigez ou affichez une confirmation comme nécessaire
        return $this->redirectToRoute('une_autre_route');
    }*/
}
