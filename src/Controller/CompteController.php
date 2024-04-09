<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Compte;
use App\Entity\Motif;
use App\Entity\Users;
use App\Entity\CompteClient;
use App\Form\CompteType;
use App\Form\CompteClientType;
use App\Controller\AccessDeniedException;

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
            // Créez et configurez l'entité Motif avec le nomCompte.
            $motif = new Motif();
            $motif->setLibelleMotif($compte->getNomCompte());
            // Vous pouvez également ajouter d'autres propriétés nécessaires à l'entité Motif ici.

            // Persistez l'entité Motif.
            $entityManager->persist($motif);
            $entityManager->flush();
            // Redirection ou affichage d'un message de succès
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
            return $this->redirectToRoute('app_directeur');
        }

        $entityManager->remove($compte);
        $entityManager->flush();

        // Add a flash message or some kind of notification to let the compte know it was successful
        $this->addFlash('success', 'Compte supprimé avec succès');

        return $this->redirectToRoute('app_directeur');
    }
    #[Route('/compte/list', name: 'compte_list')]
    public function listCompte(EntityManagerInterface $entityManager): Response
    {
        return $this->redirectToRoute('app_main');
    }
    #[Route('/compte/ouverture', name: 'compte_ouverture')]
    public function ouvrirCompte(Request $request, EntityManagerInterface $entityManager)
    {
        $compteClient = new CompteClient();
        $form = $this->createForm(CompteClientType::class, $compteClient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'employé (conseiller) actuellement connecté
            // Cette partie dépend de la manière dont vous gérez l'authentification
            $conseiller = $this->getUser();

            // Vérifier si l'utilisateur actuel est bien un conseiller
            if ($conseiller->getRoles() !== 'Conseiller') {
                // throw new AccessDeniedException('Seuls les conseillers peuvent ouvrir des comptes.');
            }

            // Assigner le conseiller au compte client (si votre modèle de données le permet)
            $compteClient->setClient($conseiller);

            // Assigner le client et le compte récupérés du formulaire à l'objet CompteClient
            // Ces données devraient être soumises via le formulaire CompteClientType
            $client = $form->get('client')->getData();
            $compte = $form->get('compte')->getData();
            $compteClient->setClient($client);
            $compteClient->setCompte($compte);

            // Gérer les autres propriétés comme dateOuverture, solde initial, etc.
            $compteClient->setDateOuverture(new \DateTime());
            $compteClient->setSolde(0);

            $entityManager->persist($compteClient);
            $entityManager->flush();

            $this->addFlash('success', 'Le compte a été ouvert avec succès.');

            // Rediriger vers une page appropriée
            return $this->redirectToRoute('quelque_route');
        }

        return $this->render('main/conseiller.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
