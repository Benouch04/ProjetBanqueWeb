<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Client;
use App\Entity\Compte;
use App\Entity\Contrat;
use App\Entity\Motif;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route("/calendar")]
class CalendarController extends AbstractController
{
    #[Route("/", name: "calendar_index", methods: ["GET"])]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $page = $request->query->getInt('page', 1); // Get the current page from the URL, default to 1
        $maxResults = 9; // The number of calendar per page

        // Calculating the offset
        $firstResult = ($page - 1) * $maxResults;

        // Get the client repository and find the calendar with the offset and the limit
        $calendar = $entityManager->getRepository(Calendar::class)
            ->findBy([], null, $maxResults, $firstResult);

        // Calculate the total number of pages
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
        $calendar = new Calendar();

        // Récupérez les noms de contrats et de comptes ici. Exemple fictif :
        $nomContrats = $entityManager->getRepository(Contrat::class)->findAll();
        $nomComptes = $entityManager->getRepository(Compte::class)->findAll();

        // Récupérez le client et le conseiller en utilisant les paramètres passés
        $client = $clientId ? $entityManager->getRepository(Client::class)->find($clientId) : null;

        // Récupérez le conseiller associé au client (remplacez getParent par la méthode appropriée)
        $conseiller = $client ? $client->getParent() : null;

        if ($client && $conseiller) {
            $calendar->setClients($client);
            $calendar->setUsers($conseiller);
        }

        // Créez le formulaire avec les données du client et du conseiller
        $form = $this->createForm(CalendarType::class, $calendar, [
            'client_name' => $client ? $client->getFullName() : '',
            'conseiller_name' => $conseiller ? $conseiller->getFullName() : '',
            'nomContrat' => $nomContrats, // Assurez-vous que ce nom correspond à celui attendu dans le formulaire
            'nomCompte' => $nomComptes, // Assurez-vous que ce nom correspond à celui attendu dans le formulaire

        ]);

        //Affiche la valeur Autre dans la liste déoulante des motifs
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
            // Vérifiez s'il existe déjà un événement qui se chevauche
            $start = $calendar->getStart();
            $end = $calendar->getEnd();

            $overlappingEvent = $entityManager->getRepository(Calendar::class)->findOneByOverlap($start, $end, $conseiller->getId());

            if ($overlappingEvent) {
                // Ajoutez un message d'erreur et retournez au formulaire
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
        ]);
    }

    #[Route("/{id}", name: "calendar_show", methods: ["GET"])]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        $calendar = $entityManager->getRepository(Calendar::class)->find($id);

        if (!$calendar) {
            throw new NotFoundHttpException('No calendar found for id ' . $id);
        }

        $motif = $calendar->getMotif();
        if ($motif) {
            $piecesJustifs = $motif->getMotifPj();
        } else {
            $piecesJustifs = []; // ou bien une ArrayCollection si vous préférez
        }

        return $this->render('calendar/show.html.twig', [
            'calendar' => $calendar,
            'piecesJustifs' => $piecesJustifs,
        ]);
    }

    #[Route("/{id}/edit", name: "calendar_edit", methods: ["GET", "POST"])]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $calendar = $entityManager->getRepository(Calendar::class)->find($id);

        if (!$calendar) {
            throw new NotFoundHttpException('No calendar found for id ' . $id);
        }

        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // This line saves the modifications

            return $this->redirectToRoute('calendar_index');
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
            throw new NotFoundHttpException('Aucun événement trouvé pour cet id ' . $id);
        }

        // Ici, nous supposons que 'description' contient le motif.
        // Vous devez ajuster ce code si le motif est stocké différemment.
        $description = $calendar->getMotif();

        if ($description === 'Ouverture compte') {
            // Si le motif est l'ouverture d'un compte, personnalisez le message
            $this->addFlash('success', 'L\'ouverture du compte a été acceptée avec succès.');
        } else if ($description === 'Ouverture contrat') {
            // Pour tout autre motif
            $this->addFlash('success', 'L\'ouverture du contrat a été acceptée avec succès.');
        }

        // ... (ici la logique pour effectivement accepter l'événement)

        return $this->redirectToRoute('app_conseiller');
    }

    #[Route("/calendar/reject/{id}", name: "calendar_reject")]
    public function rejectAction(Request $request, $id)
    {
        $this->addFlash('error', 'La création  a été rejeté avec succès.');
        return $this->redirectToRoute('calendar_index');
    }
}

