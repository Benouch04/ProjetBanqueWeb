<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Client;
use App\Entity\PieceJustif;
use App\Form\CompteType;
use App\Form\ClientType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\UsersAuthenticator;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use App\Entity\Contrat;
use App\Entity\Compte;
use App\Controller\ClientController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController'
        ]);
    }
    #[Route('/directeur', name: 'app_directeur')]
    public function directeur(EntityManagerInterface $entityManager): Response
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
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
                'allDay' => $event->isAllDay(),
            ];
        }

        $data = json_encode($rdvs);
        /*dd($data);*/

        return $this->render('main/directeur.html.twig', [
            'controller_name' => 'MainController',
            'users' => $users,
            'clients' => $clients,
            'contrats' => $contrats,
            'comptes' => $comptes,
            'pieces' => $pieces,
            'data' => $data,
        ]);
    }
    #[Route('/agent', name: 'app_agent')]
    public function agent(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(Users::class)->findAll();
        return $this->render('main/agent.html.twig', [
            'controller_name' => 'MainController',
            'users' => $users
        ]);
    }
    #[Route('/conseiller', name: 'app_conseiller')]
    public function conseiller(EntityManagerInterface $entityManager, Request $request): Response
    {
        $clients = $entityManager->getRepository(Client::class)->findAll();
        $events = $entityManager->getRepository(Calendar::class)->findAll();

        // Récupérer les utilisateurs dont le type est 'conseiller'
        $conseillers = $entityManager->getRepository(Users::class)->findBy(['type' => 'conseiller']);


        $rdvs = [];

        foreach ($events as $event) {
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
                'allDay' => $event->isAllDay(),
            ];
        }

        $form = $this->createFormBuilder(null)
            ->setAction($this->generateUrl('client_search')) // Assurez-vous que la route 'client_search' est configurée
            ->add('query', TextType::class, [
                'label' => false, // On ne veut pas de label
                'attr' => [
                    'placeholder' => 'Search for...',
                    'aria-label' => 'Search for...',
                    'aria-describedby' => 'btnNavbarSearch',
                    'class' => 'form-control'
                ],
            ])

            ->getForm();

        $data = json_encode($rdvs);

        //Pagination

        $page = $request->query->getInt('page', 1); // Get the current page from the URL, default to 1
        $maxResults = 5; // The number of clients per page

        // Calculating the offset
        $firstResult = ($page - 1) * $maxResults;

        // Get the client repository and find the clients with the offset and the limit
        $clients = $entityManager->getRepository(Client::class)
            ->findBy([], null, $maxResults, $firstResult);

        // Calculate the total number of pages
        $totalClients = count($entityManager->getRepository(Client::class)->findAll());
        $totalPages = ceil($totalClients / $maxResults);

        return $this->render('main/conseiller.html.twig', [
            'controller_name' => 'MainController',
            'clients' => $clients,
            'conseillers' => $conseillers,
            'data' => $data,
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
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
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
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
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
}
