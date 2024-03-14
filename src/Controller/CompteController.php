<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Compte;
use App\Form\CompteType;

class CompteController extends AbstractController
{
    #[Route('/compte', name: 'app_compte')]
    public function index(): Response
    {
        return $this->render('compte/index.html.twig', [
            'controller_name' => 'CompteController',
        ]);
    }
    #[Route('/compte/ajout', name: 'compte_ajout')]
    public function ajoutCompte(Request $request, EntityManagerInterface $entityManager)
    {
        $compte = new Compte();
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($compte);
            $entityManager->flush();

            // Redirection ou affichage d'un message de succès
            return $this->redirectToRoute('main');
        }

        return $this->render('compte/index.html.twig', [
            'form' => $form->createView(),
            'nomCompte' => $compte->getNomCompte()
        ]);
    }
    #[Route("/compte/edit/{id}", name: "compte_edit")]
    public function editCompte(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $compte = $entityManager->getRepository(Compte::class)->find($id);

        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour le compte
            $compte->setNomCompte($form->get('nomCompte')->getData());
            $entityManager->flush();

            $this->addFlash('success', 'Les informations du nom de compte ont été modifiées avec succès.');

            return $this->redirectToRoute('compte_list');
        }

        return $this->render('compte/edit.html.twig', [
            'comptes' => $compte,
            'form' => $form->createView(),
        ]);
    }
    #[Route("/compte/delete/{id}", name: "compte_delete", methods: ["POST"])]
    public function deleteCompte(int $id, EntityManagerInterface $entityManager): Response
    {
        $compte = $entityManager->getRepository(Compte::class)->find($id);

        if (!$compte) {
            // Handle the case where the compte does not exist
            $this->addFlash('error', 'Compte non trouvé');
            return $this->redirectToRoute('compte_list');
        }

        $entityManager->remove($compte);
        $entityManager->flush();

        // Add a flash message or some kind of notification to let the compte know it was successful
        $this->addFlash('success', 'Compte supprimé avec succès');

        return $this->redirectToRoute('compte_list');
    }
    #[Route('/compte/list', name: 'compte_list')]
    public function listCompte(EntityManagerInterface $entityManager): Response
    {
        return $this->redirectToRoute('app_main');
    }
}
