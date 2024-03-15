<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Client;
use App\Entity\PieceJustif;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\UsersAuthenticator;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use App\Entity\Contrat;
use App\Entity\Compte;

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

        foreach($events as $event){
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
    public function conseiller(EntityManagerInterface $entityManager): Response
    {
        $clients = $entityManager->getRepository(Client::class)->findAll();
        $events = $entityManager->getRepository(Calendar::class)->findAll();

        $rdvs = [];

        foreach($events as $event){
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
        return $this->render('main/conseiller.html.twig', [
            'controller_name' => 'MainController',
            'clients' => $clients,
            'data' => $data,
        ]);
    }
}
