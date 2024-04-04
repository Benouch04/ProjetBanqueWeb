<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\ClientInfoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Client;
use App\Entity\CompteClient;
use App\Entity\Calendar;
use App\Form\ClientType;
use App\Form\CompteClientType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


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

            $this->addFlash('success', 'Le client a été ajouté avec succès');
            // Redirection ou affichage d'un message de succès
            return $this->redirectToRoute('client_list');
        }

        return $this->render('client/index.html.twig', [
            'form' => $form->createView(),
            'nomClient' => $client->getNomClient(),
            'prenomClient' => $client->getPrenomClient(),
            'adresseClient' => $client->getAdresseClient(),
            'numTel' => $client->getNumTel(),
            'situation' => $client->getSituation()
        ]);
    }
    #[Route("/client/edit/{id}", name: "client_edit")]
    public function editClient(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $client = $entityManager->getRepository(Client::class)->find($id);

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour le client
            $client->setNomClient($form->get('nomClient')->getData());
            $client->setPrenomClient($form->get('prenomClient')->getData());

            $client->setAdresseClient($form->get('adresseClient')->getData());
            $client->setNumTel($form->get('numTel')->getData());
            $client->setSituation($form->get('situation')->getData());
            $entityManager->flush();

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
    #[Route('/search', name: 'client_search')]
    public function searchClient(Request $request, EntityManagerInterface $entityManager)
    {
        $searchForm = $this->createFormBuilder(null)
            ->add('query', TextType::class)
            ->getForm();

        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $query = $searchForm->getData()['query'];
            $client = $entityManager->getRepository(Client::class)->findOneBy(['nomClient' => $query]);

            if ($client) {
                return $this->redirectToRoute('client_infos', ['id' => $client->getId()]);
            } else {
                // Si le client n'est pas trouvé, ajoutez un message flash
                $this->addFlash(
                    'danger', // Le type de message, peut être 'danger', 'warning', 'success', etc.
                    'Aucun client trouvé avec le nom ' . $query
                );

                // Redirigez l'utilisateur là où vous voulez, par exemple :
                return $this->redirectToRoute('app_conseiller');
            }
        }

        // Assurez-vous de retourner le formulaire de recherche pour qu'il soit rendu si aucune recherche n'a été faite ou si le formulaire n'est pas valide.
        return $this->render('main/conseiller.html.twig', [
            'searchForm' => $searchForm->createView(),
        ]);
    }
    #[Route('/client/list', name: 'client_list')]
    public function listClients(EntityManagerInterface $entityManager): Response
    {
        return $this->redirectToRoute('app_conseiller');
    }
    // Dans ClientController.php ou le contrôleur qui gère l'affichage des infos client

    #[Route('/client/infos/{id}', name: 'client_infos')]
    public function show($id, EntityManagerInterface $entityManager): Response
    {
        $client = $entityManager->getRepository(Client::class)->find($id);
        $user = $entityManager->getRepository(Users::class)->find($id);
        $form = $this->createForm(ClientInfoType::class, $client);
        $compte = $entityManager->getRepository(CompteClient::class)->find($id);
        $formCompte = $this->createForm(CompteClientType::class, $compte);
        if (!$client) {
            throw $this->createNotFoundException('Le client n\'a pas été trouvé.');
        }

        $conseiller = $client->getParent();


        return $this->render('client/info.html.twig', [
            'client' => $client,
            'user' => $user,
            'form' => $form->createView(),
            'formCompte' => $formCompte->createView(),
            'conseiller' => $conseiller,
        ]);
    }

}
