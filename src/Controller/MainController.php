<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Client;
use App\Entity\PieceJustif;
use App\Form\StatistiqueContratType;
use App\Form\StatistiqueRdvType;
use App\Form\StatistiqueClientType;
use App\Form\StatistiqueSoldeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use App\Entity\Contrat;
use App\Entity\Compte;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'MainController'
        ]);
    }
    #[Route('/directeur', name: 'app_directeur')]
    public function directeur(EntityManagerInterface $entityManager, SessionInterface $session, Request $request): Response
    {
        $users = $entityManager->getRepository(Users::class)->findAll();
        $clients = $entityManager->getRepository(Client::class)->findAll();
        $contrats = $entityManager->getRepository(Contrat::class)->findAll();
        $pieces = $entityManager->getRepository(PieceJustif::class)->findAll();
        $comptes = $entityManager->getRepository(Compte::class)->findAll();
        $events = $entityManager->getRepository(Calendar::class)->findAll();

        $rdvs = [];

        foreach ($events as $event) {
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'backgroundColor' => $event->getBackgroundColor(),
                'allDay' => $event->isAllDay(),
            ];
        }

        $data = json_encode($rdvs);

        //Pagination pour les employés
        $pageUsers = $request->query->getInt('pageUsers', 1);
        $maxResultsUsers = 3;
        $firstResultUsers = ($pageUsers - 1) * $maxResultsUsers;
        $users = $entityManager->getRepository(Users::class)
            ->findBy([], null, $maxResultsUsers, $firstResultUsers);
        $totalUsers = count($entityManager->getRepository(Users::class)->findAll());
        $totalPagesUsers = ceil($totalUsers / $maxResultsUsers);

        //Pagination pour les pièces justificatives
        $pagePJ = $request->query->getInt('pagePJ', 1);
        $maxResults = 3;
        $firstResult = ($pagePJ - 1) * $maxResults;
        $pieces = $entityManager->getRepository(PieceJustif::class)
            ->findBy([], null, $maxResults, $firstResult);
        $totalPieces = count($entityManager->getRepository(PieceJustif::class)->findAll());
        $totalPagesPJ = ceil($totalPieces / $maxResults);

        //Pagination pour les contrats
        $pageContrats = $request->query->getInt('pageContrats', 1);
        $maxResults = 3;
        $firstResult = ($pageContrats - 1) * $maxResults;
        $contrats = $entityManager->getRepository(Contrat::class)
            ->findBy([], null, $maxResults, $firstResult);
        $totalContrats = count($entityManager->getRepository(Contrat::class)->findAll());
        $totalPagesContrats = ceil($totalContrats / $maxResults);

        //Pagination pour les comptes
        $pageComptes = $request->query->getInt('pageComptes', 1);
        $maxResults = 3;
        $firstResult = ($pageComptes - 1) * $maxResults;
        $comptes = $entityManager->getRepository(Compte::class)
            ->findBy([], null, $maxResults, $firstResult);
        $totalComptes = count($entityManager->getRepository(Compte::class)->findAll());
        $totalPagesComptes = ceil($totalComptes / $maxResults);

        //Partie statistiques
        $formStatContrat = $this->createForm(StatistiqueContratType::class);
        $nombreContrats = $session->get('nombreContrats', null);

        $formStatRdv = $this->createForm(StatistiqueRdvType::class);
        $nombreRdv = $session->get('nombreRdv', null);

        $formStatClient = $this->createForm(StatistiqueClientType::class);
        $nombreClient = $session->get('nombreClient', null);

        $formStatSolde = $this->createForm(StatistiqueSoldeType::class);
        $soldeClient = $session->get('soldeClient', null);

        return $this->render('main/directeur.html.twig', [
            'formStatContrat' => $formStatContrat->createView(),
            'formStatRdv' => $formStatRdv->createView(),
            'formStatClient' => $formStatClient->createView(),
            'formStatSolde' => $formStatSolde->createView(),
            'soldeClient' => $soldeClient,
            'nombreRdv' => $nombreRdv,
            'nombreClient' => $nombreClient,
            'nombreContrats' => $nombreContrats,
            'users' => $users,
            'clients' => $clients,
            'contrats' => $contrats,
            'comptes' => $comptes,
            'pieces' => $pieces,
            'data' => $data,
            'totalPagesUsers' => $totalPagesUsers,
            'currentPageUsers' => $pageUsers,
            'totalPagesPJ' => $totalPagesPJ,
            'currentPagePJ' => $pagePJ,
            'totalPagesContrats' => $totalPagesContrats,
            'currentPageContrats' => $pageContrats,
            'totalPagesComptes' => $totalPagesComptes,
            'currentPageComptes' => $pageComptes,
        ]);
    }
    #[Route('/agent', name: 'app_agent')]
    public function agent(EntityManagerInterface $entityManager, Request $request): Response
    {
        $clients = $entityManager->getRepository(Client::class)->findAll();

        $form = $this->createFormBuilder(null)
            ->setAction($this->generateUrl('client_search')) 
            ->add('query', TextType::class, [
                'label' => false, 
                'attr' => [
                    'placeholder' => 'Search for...',
                    'aria-label' => 'Search for...',
                    'aria-describedby' => 'btnNavbarSearch',
                    'class' => 'form-control'
                ],
            ])

            ->getForm();

        //Pagination de la liste des clients
        $page = $request->query->getInt('page', 1); 
        $maxResults = 5; 
        $firstResult = ($page - 1) * $maxResults;
        $clients = $entityManager->getRepository(Client::class)
            ->findBy([], null, $maxResults, $firstResult);
        $totalClients = count($entityManager->getRepository(Client::class)->findAll());
        $totalPages = ceil($totalClients / $maxResults);

        return $this->render('main/agent.html.twig', [
            'clients' => $clients,
            'search_form' => $form->createView(),
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }
    #[Route('/conseiller', name: 'app_conseiller')]
    public function conseiller(EntityManagerInterface $entityManager, Request $request): Response
    {
        $clients = $entityManager->getRepository(Client::class)->findAll();

        $conseillers = $entityManager->getRepository(Users::class)->findBy(['type' => 'conseiller']);

        $form = $this->createFormBuilder(null)
            ->setAction($this->generateUrl('client_search')) 
            ->add('query', TextType::class, [
                'label' => false, 
                'attr' => [
                    'placeholder' => 'Search for...',
                    'aria-label' => 'Search for...',
                    'aria-describedby' => 'btnNavbarSearch',
                    'class' => 'form-control'
                ],
            ])

            ->getForm();

        //Pagination dde la liste des clients
        $page = $request->query->getInt('page', 1); 
        $maxResults = 5;
        $firstResult = ($page - 1) * $maxResults;
        $clients = $entityManager->getRepository(Client::class)
            ->findBy([], null, $maxResults, $firstResult);
        $totalClients = count($entityManager->getRepository(Client::class)->findAll());
        $totalPages = ceil($totalClients / $maxResults);

        return $this->render('main/conseiller.html.twig', [
            'controller_name' => 'MainController',
            'clients' => $clients,
            'conseillers' => $conseillers,
            'search_form' => $form->createView(),
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }

    #[Route('/conseiller/{id}/planning', name: 'conseiller_planning')]
    public function planningConseiller($id, EntityManagerInterface $entityManager): Response
    {
        $conseiller = $entityManager->getRepository(Users::class)->find($id);
        $calendarRepository = $entityManager->getRepository(Calendar::class);
        $events = $calendarRepository->findBy(['users' => $conseiller->getId()]);

        $rdvs = [];

        foreach ($events as $event) {
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'backgroundColor' => $event->getBackgroundColor(),
                'allDay' => $event->isAllDay(),
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('calendar/calendarConseiller.html.twig', [
            'events' => $events,
            'conseiller' => $conseiller,
            'data' => $data,
        ]);
    }

    #[Route('/conseiller/planning', name: 'choix_conseiller_planning')]
    public function choixPlanningConseiller(Request $request, EntityManagerInterface $entityManager): Response
    {
        $id = $request->query->get('id');

        if (!$id) {
            $this->addFlash('error', 'Pas de conseiller avec cet id');
        }

        $conseiller = $entityManager->getRepository(Users::class)->find($id);

        if (!$conseiller) {
            $this->addFlash('error', 'Conseiller non trouvé');
        }
        $calendarRepository = $entityManager->getRepository(Calendar::class);
        $events = $calendarRepository->findBy(['users' => $conseiller->getId()]);

        $rdvs = [];

        foreach ($events as $event) {
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'backgroundColor' => $event->getBackgroundColor(),
                'allDay' => $event->isAllDay(),
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('calendar/calendarConseiller.html.twig', [
            'events' => $events,
            'conseiller' => $conseiller,
            'data' => $data,
        ]);
    }

    #[Route('/directeur/reinit', name: 'app_directeur_reinit')]
    public function nouvelleRecherche(SessionInterface $session): Response
    {
        $session->remove('nombreContrats');
        $session->remove('searchContrat');
        $session->remove('searchRdv');
        $session->remove('searchClient');
        $session->remove('searchSolde');
        $session->remove('soldeClient');

        return $this->redirectToRoute('app_directeur');
    }
}
