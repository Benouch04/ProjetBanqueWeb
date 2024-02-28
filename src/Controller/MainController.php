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

class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(Users::class)->findAll();
        $clients = $entityManager->getRepository(Client::class)->findAll();
        $contrats = $entityManager->getRepository(Contrat::class)->findAll();
        $pieces = $entityManager->getRepository(PieceJustif::class)->findAll();
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

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'users' => $users,
            'clients' => $clients,
            'contrats' => $contrats,
            'pieces' => $pieces,
            'data' => $data,
        ]);
    }
}
