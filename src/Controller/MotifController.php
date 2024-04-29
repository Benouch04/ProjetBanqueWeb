<?php

namespace App\Controller;

use App\Entity\Motif;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\MotifType;

#[Route('/directeur')]
class MotifController extends AbstractController
{

    #[Route('/motif/list', name: 'motif_list')]
    public function listMotifs(EntityManagerInterface $entityManager): Response
    {
        $motif = $entityManager->getRepository(Motif::class)->findAll();
        return $this->render('motif/index.html.twig', [
            'motifs' => $motif
        ]);
    }
    #[Route('/motif/{id}/edit', name: 'motif_edit')]
    public function edit(Request $request, $id, EntityManagerInterface $entityManager)
    {
        $motifRepository = $entityManager->getRepository(Motif::class);
        $motif = $motifRepository->find($id);

        if (!$motif) {
            $this->addFlash('error', 'Aucun motif trouvé pour l\'ID');
        }

        $form = $this->createForm(MotifType::class, $motif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($motif);
            $entityManager->flush();

            return $this->redirectToRoute('motif_list');
        }

        return $this->render('motif/edit.html.twig', [
            'motif' => $motif,
            'form' => $form->createView(),
        ]);
    }
}
