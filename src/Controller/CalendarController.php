<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Client;
use App\Entity\Compte;
use App\Entity\Contrat;
use App\Entity\Users;
use App\Entity\ContratClient;
use App\Entity\Motif;
use App\Entity\CompteClient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\CalendarType;
use App\Form\CompteClientType;
use App\Form\ContratClientType;
use App\Form\StatistiqueRdvType;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route("/calendar")]
class CalendarController extends AbstractController
{
    #[Route("/", name: "calendar_index", methods: ["GET"])]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $maxResults = 9;

        $firstResult = ($page - 1) * $maxResults;

        $calendar = $entityManager->getRepository(Calendar::class)
            ->findBy([], null, $maxResults, $firstResult);

        $totalCalendar = count($entityManager->getRepository(Calendar::class)->findAll());
        $totalPages = ceil($totalCalendar / $maxResults);


        return $this->render('calendar/index.html.twig', [
            'calendars' => $calendar,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }

    #[Route("/new/{clientId}", name: "calendar_new", methods: ["GET", "POST"])]
    public function new(Request $request, EntityManagerInterface $entityManager, $clientId = null): Response
    {
        $nomContrats = $entityManager->getRepository(Contrat::class)->findAll();
        $nomComptes = $entityManager->getRepository(Compte::class)->findAll();
        $client = $entityManager->getRepository(Client::class)->findAll();

        $client = $clientId ? $entityManager->getRepository(Client::class)->find($clientId) : null;

        $conseiller = $client ? $client->getParent() : null;

        $calendar = new Calendar();
        if ($client && $conseiller) {
            $calendar->setClients($client);
            $calendar->setUsers($conseiller);
        }

        $form = $this->createForm(CalendarType::class, $calendar, [
            'client_name' => $client ? $client->getFullName() : '',
            'conseiller_name' => $conseiller ? $conseiller->getFullName() : '',
            'nomContrat' => $nomContrats,
            'nomCompte' => $nomComptes,

        ]);

        $motifRepository = $entityManager->getRepository(Motif::class);
        $autreMotif = $motifRepository->findOneBy(['libelleMotif' => 'Autre']);

        if (!$autreMotif) {
            $autreMotif = new Motif();
            $autreMotif->setLibelleMotif('Autre');
            $entityManager->persist($autreMotif);
            $entityManager->flush();
        }
        $form->add('motif', EntityType::class, [
            'class' => Motif::class,
            'choices' => $entityManager->getRepository(Motif::class)->findAllIncludingAutre(),
            'choice_label' => 'libelleMotif',
            'label' => 'Motif :',
            'attr' => ['class' => 'form-select'],
            'label_attr' => ['class' => 'form-label mt-2'],
            'placeholder' => 'Sélectionnez un motif',
            'required' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $start = $calendar->getStart();
            $end = $calendar->getEnd();

            $overlappingEvent = $entityManager->getRepository(Calendar::class)->findOneByOverlap($start, $end, $conseiller->getId());

            if ($overlappingEvent) {
                $this->addFlash('error', 'Il existe déjà un événement durant cette période.');
                return $this->render('calendar/new.html.twig', [
                    'calendar' => $calendar,
                    'form' => $form->createView(),
                ]);
            }

            $entityManager->persist($calendar);
            $entityManager->flush();

            $this->addFlash('success', 'Le rendez-vous a été ajouté avec succès.');
            return $this->redirectToRoute('calendar_show', ['id' => $calendar->getId()]);
        }


        return $this->render('calendar/new.html.twig', [
            'calendar' => $calendar,
            'form' => $form->createView(),
            'client' => $client,
        ]);
    }

    #[Route("/{id}", name: "calendar_show", methods: ["GET"])]
    public function show(int $id, EntityManagerInterface $entityManager, Request $request, SessionInterface $session): Response
    {
        $calendar = $entityManager->getRepository(Calendar::class)->find($id);
        if (!$calendar) {
            $this->addFlash('error', 'Le calendrier avec cet id existe pas :' . $id);
        }
        $client = $calendar->getClients();
        $libelleMotif = $calendar->getMotif() ? $calendar->getMotif()->getLibelleMotif() : null;

        //Partie compte
        $compteExistant = $entityManager->getRepository(Compte::class)->findOneBy(['NomCompte' => $libelleMotif]);

        $compteClientExistant = null;
        if ($compteExistant) {
            $compteClientExistant = $entityManager->getRepository(CompteClient::class)->findOneBy([
                'compte' => $compteExistant,
                'client' => $client
            ]);
        }

        $compteCreationStatus = $compteClientExistant ? 'existant' : 'inconnu';

        $piecesJustifs = $calendar->getMotif() ? $calendar->getMotif()->getMotifPj() : [];

        $compteClient = new CompteClient();

        $formCompte = $this->createForm(CompteClientType::class, $compteClient);

        //Partie Contrat
        $contratExistant = $entityManager->getRepository(Contrat::class)->findOneBy(['nomContrat' => $libelleMotif]);

        $contratClientExistant = null;
        if ($contratExistant) {
            $contratClientExistant = $entityManager->getRepository(ContratClient::class)->findOneBy([
                'contrat' => $contratExistant,
                'client' => $client
            ]);
        }

        $contratCreationStatus = $contratClientExistant ? 'existant' : 'inconnu';

        $piecesJustifs = $calendar->getMotif() ? $calendar->getMotif()->getMotifPj() : [];

        $contratClient = new ContratClient();

        $formContrat = $this->createForm(ContratClientType::class, $contratClient);

        return $this->render('calendar/show.html.twig', [
            'formCompte' => $formCompte->createView(),
            'formContrat' => $formContrat->createView(),
            'calendar' => $calendar,
            'piecesJustifs' => $piecesJustifs,
            'compteCreationStatus' => $compteCreationStatus,
            'contratCreationStatus' => $contratCreationStatus, 
            
        ]);
    }


    #[Route("/{id}/edit", name: "calendar_edit", methods: ["GET", "POST"])]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $calendar = $entityManager->getRepository(Calendar::class)->find($id);

        if (!$calendar) {
            $this->addFlash('error', 'Le calendrier avec cet id existe pas :' . $id);
        }

        $form = $this->createForm(CalendarType::class, $calendar);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();
            $this->addFlash('success', 'Le rendez vous a été mis à jour');
            return $this->redirectToRoute('calendar_show', ['id' => $id]);
        }

        return $this->render('calendar/edit.html.twig', [
            'calendar' => $calendar,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/{id}", name: "calendar_delete", methods: ["DELETE"])]
    public function delete(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $calendar = $entityManager->getRepository(Calendar::class)->find($id);

        if (!$calendar) {
            throw new NotFoundHttpException('No calendar found for id ' . $id);
        }

        if ($this->isCsrfTokenValid('delete' . $calendar->getId(), $request->request->get('_token'))) {
            $entityManager->remove($calendar);
            $entityManager->flush();
        }

        return $this->redirectToRoute('calendar_index');
    }
    #[Route("/accept/{id}", name: "calendar_accept")]
    public function acceptAction(EntityManagerInterface $entityManager, $id): Response
    {
        $calendar = $entityManager->getRepository(Calendar::class)->find($id);

        if (!$calendar) {
            $this->addFlash('error', 'Aucun événement trouvé pour cet id ' . $id);
        }
        $description = $calendar->getMotif();

        if ($description === 'Ouverture compte') {
            $this->addFlash('success', 'L\'ouverture du compte a été acceptée avec succès.');
        } else if ($description === 'Ouverture contrat') {
            $this->addFlash('success', 'L\'ouverture du contrat a été acceptée avec succès.');
        }
        return $this->redirectToRoute('app_conseiller');
    }

    #[Route("/calendar/reject/{id}", name: "calendar_reject")]
    public function rejectAction(Request $request, $id)
    {
        $this->addFlash('error', 'La création  a été rejeté avec succès.');
        return $this->redirectToRoute('calendar_index');
    }

    #[Route('/rdv/statistiques', name: 'rdv_statistiques')]
    public function statistiquesRDV(Request $request, CalendarRepository $calendarRepository, SessionInterface $session): Response
    {

        $formStatRdv = $this->createForm(StatistiqueRdvType::class);
        $formStatRdv->handleRequest($request);

        if ($formStatRdv->isSubmitted() && $formStatRdv->isValid()) {
            $data = $formStatRdv->getData();

            if ($data['endDateRdv'] <= $data['startDateRdv']) {
                $request->getSession()->getFlashBag()->add('error', 'La date de fin doit être supérieure à la date de début.');
                return $this->redirectToRoute('app_directeur');
            }
            $nombreRdv = $calendarRepository->countRdvByDate($data['startDateRdv'], $data['endDateRdv']);

            $session->set('searchRdv', true);
            $session->set('nombreRdv', $nombreRdv);
            $session->set('startDateRdv', $data['startDateRdv']);
            $session->set('endDateRdv', $data['endDateRdv']);

            return $this->redirectToRoute('app_directeur');
        }

        return $this->render('calendar/statistiques.html.twig', [
            'formStatRdv' => $formStatRdv->createView(),
        ]);
    }
}

