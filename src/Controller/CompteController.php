<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Compte;
use App\Entity\Motif;
use App\Form\CompteType;
use App\Repository\CompteRepository;

#[Route('/directeur')]
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
    public function ajoutCompte(Request $request, EntityManagerInterface $entityManager, CompteRepository $compteRepository)
    {
        $compte = new Compte();
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $existingCompte = $compteRepository->findByNomCompte($compte->getNomCompte());
            if ($existingCompte) {
                $this->addFlash('danger', 'Un compte avec le même nom existe déjà.');
                return $this->redirectToRoute('compte_ajout');
            }
            $entityManager->persist($compte);

            $motif = new Motif();
            $motif->setLibelleMotif($compte->getNomCompte());
            $entityManager->persist($motif);
            $entityManager->flush();

            return $this->redirectToRoute('app_directeur');
        }

        return $this->render('compte/index.html.twig', [
            'form' => $form->createView(),
            'nomCompte' => $compte->getNomCompte(),
            'data_class' => Compte::class
        ]);
    }
    #[Route("/compte/edit/{id}", name: "compte_edit")]
    public function editCompte(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $compte = $entityManager->getRepository(Compte::class)->find($id);

        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            $this->addFlash('error', 'Compte non trouvé');
            return $this->redirectToRoute('app_directeur');
        }

        $entityManager->remove($compte);
        $entityManager->flush();

        $this->addFlash('success', 'Compte supprimé avec succès');

        return $this->redirectToRoute('app_directeur');
    }
    #[Route('/compte/list', name: 'compte_list')]
    public function listCompte(EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('ROLE_AGENT')){
            return $this->redirectToRoute('app_agent');
        } elseif ($this->isGranted('ROLE_CONSEILLER')){
            return $this->redirectToRoute('app_conseiller');
        } else {
            return $this->redirectToRoute('app_directeur');
        }     
    }
}
