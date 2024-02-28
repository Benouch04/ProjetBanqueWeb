<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Client;
use App\Form\ClientType;


class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client')]
    public function index(): Response
    {
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }

    #[Route('/client/ajout', name: 'client_ajout')]
    public function ajoutClient(Request $request, EntityManagerInterface $entityManager)
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();

            // Redirection ou affichage d'un message de succès
            return $this->redirectToRoute('main');
        }

        return $this->render('client/index.html.twig', [
            'form' => $form->createView(),
            'nomClient' => $client->getNomClient(),
            'prenomClient' => $client->getPrenomClient(),
            'adresseClient' => $client->getAdresseClient(),
            'numTel' => $client->getNumTel(),
            'situation' => $client->getSituation(),
        ]);
    }
    #[Route("/client/edit/{id}", name: "client_edit")]
    public function editUsers(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $client = $entityManager->getRepository(Client::class)->find($id);

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour le client
            $client->setNomClient($form->get('nomClient')->getData());
            $client->setPrenomClient($form->get('prenomClient')->getData());
            $client->setNumTel($form->get('numTel')->getData());
            $client->setAdresseClient($form->get('adresseClient')->getData());
            $client->setSituation($form->get('situation')->getData());
            $entityManager->flush();

            $this->addFlash('success', 'Les informations du client ont été modifiées avec succès.');

            return $this->redirectToRoute('client_list');
        }

        return $this->render('client/edit.html.twig', [
            'clients' => $client,
            'form' => $form->createView(),
        ]);
    }
    #[Route("/client/delete/{id}", name: "client_delete", methods: ["POST"])]
    public function deleteContrat(int $id, EntityManagerInterface $entityManager): Response
    {
        $client = $entityManager->getRepository(Client::class)->find($id);

        if (!$client) {
            // Handle the case where the contrat does not exist
            $this->addFlash('error', 'Client non trouvé');
            return $this->redirectToRoute('client_list');
        }

        $entityManager->remove($client);
        $entityManager->flush();

        // Add a flash message or some kind of notification to let the contrat know it was successful
        $this->addFlash('success', 'Client supprimé avec succès');

        return $this->redirectToRoute('client_list');
    }
    #[Route('/client/list', name: 'client_list')]
    public function listClients(EntityManagerInterface $entityManager): Response
    {
        return $this->redirectToRoute('app_main');
    }
}
