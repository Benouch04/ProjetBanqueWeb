<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Compte;
use App\Entity\Motif;
use App\Entity\Calendar;
use App\Entity\Client;
use App\Entity\CompteClient;
use App\Form\StatistiqueSoldeType;
use App\Form\CompteClientType;
use App\Controller\AccessDeniedException;
use App\Repository\CompteClientRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CompteClientController extends AbstractController
{
    #[Route('/compte/client', name: 'app_compte_client')]
    public function index(): Response
    {
        return $this->render('compte_client/index.html.twig', [
            'controller_name' => 'CompteClientController',
        ]);
    }

   /* #[Route("/client/compte/edit/{id}", name: "compteClient_edit")]
    public function editClientCompte(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $compteClient = $entityManager->getRepository(CompteClient::class)->find($id);

        $form = $this->createForm(CompteClientType::class, $compteClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $compteClient->setMontantDecouvert($form->get('montantDecouvert')->getData());
            $compteClient->setSolde($form->get('solde')->getData());

            $entityManager->flush();

            return $this->redirectToRoute('compteClient_list');
        }

        return $this->render('compte_client/edit.html.twig', [
            'compteClients' => $compteClient,
            'form' => $form->createView(),
        ]);
    }*/

    #[Route("/client/compte/delete/{id}", name: "compteClient_delete", methods: ["POST"])]
    public function deleteCompteClient(int $id, EntityManagerInterface $entityManager): Response
    {
        $compteClient = $entityManager->getRepository(CompteClient::class)->find($id);

        $clientId = $compteClient->getClient()->getId();

        if (!$compteClient) {
            $this->addFlash('error', 'Compte associé au client non trouvé');
            return $this->redirectToRoute('client_infos', ['id' => $clientId]);
        }

        $entityManager->remove($compteClient);
        $entityManager->flush();

        $this->addFlash('success', 'Compte associé au client supprimé avec succès');

        return $this->redirectToRoute('client_infos', ['id' => $clientId]);
    }

    #[Route('/client/compte/{id}', name: 'compteClient_list')]
    public function listCompteClient(EntityManagerInterface $entityManager, $id): Response
    {
        return $this->redirectToRoute('client_infos');
    }
    #[Route('/compte/ouverture/{id}', name: 'compte_ouverture')]
    public function ouvertureCompte(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, int $id): Response
    {
        $calendar = $entityManager->getRepository(Calendar::class)->find($id);

        if (!$calendar) {
            throw new NotFoundHttpException('Rendez-vous non trouvé');
        }

        $client = $calendar->getClients(); // Assurez-vous que c'est bien la méthode appropriée pour obtenir le client

        $libelleMotif = $calendar->getMotif()->getLibelleMotif();
        $compteExistant = $entityManager->getRepository(Compte::class)->findOneBy(['NomCompte' => $libelleMotif]);

        if (!$compteExistant) {
            throw new \Exception("Aucun compte avec le nom spécifié n'a été trouvé.");
        }

        $compteClientExistant = $entityManager->getRepository(CompteClient::class)->findOneBy([
            'compte' => $compteExistant,
            'client' => $client
        ]);

        if ($compteClientExistant) {
            $session->set('compteCreationStatus', 'existant');
            return $this->redirectToRoute('some_route', ['id' => $compteClientExistant->getId()]);
        }

        $compteClient = new CompteClient();
        $form = $this->createForm(CompteClientType::class, $compteClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ... logique de création de CompteClient
            $compteClient->setDateOuverture(new \DateTime()); // Date actuelle
            $compteClient->setSolde(0); // Solde initial
            $montantDecouvert = $form->get('montantDecouvert')->getData();
            $compteClient->setMontantDecouvert($montantDecouvert);
            $compteClient->setCompte($compteExistant); // Associez le compte existant
            $client->addCompteClient($compteClient); // Associez le compte client au client

            $entityManager->persist($compteClient);
            $entityManager->flush();

            $session->set('compteCreationStatus', 'nouveau');
            return $this->redirectToRoute('calendar_show', ['id' => $calendar->getId()]);
        }

        // Si le formulaire n'est pas soumis ou n'est pas valide, retournez le formulaire
        return $this->render('calendar/show.html.twig', [
            'form' => $form->createView(),
            'calendar' => $calendar,
        ]);
    }

    #[Route('/solde/statistiques', name: 'solde_statistiques')]
    public function statistiquesSolde(Request $request, CompteClientRepository $compteClientRepository, SessionInterface $session): Response
    {
        $formStatSolde = $this->createForm(StatistiqueSoldeType::class);
        $formStatSolde->handleRequest($request);

        if ($formStatSolde->isSubmitted() && $formStatSolde->isValid()) {
            $data = $formStatSolde->getData();
            $soldeClient = $compteClientRepository->findTotalSoldeAtDate($data['dateOuverture']);

            $session->set('searchSolde', true);
            $session->set('soldeClient', $soldeClient);
            $session->set('dateOuverture', $data['dateOuverture']);

            return $this->redirectToRoute('app_directeur');
        }

        return $this->render('compte_client/statistiques.html.twig', [
            'formStatSolde' => $formStatSolde->createView(),
        ]);
    }
}
