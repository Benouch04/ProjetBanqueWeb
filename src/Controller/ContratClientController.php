<?php

namespace App\Controller;

use App\Form\ContratClientType;
use App\Form\StatistiqueContratType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contrat;
use App\Entity\Motif;
use App\Entity\Calendar;
use App\Entity\Client;
use App\Entity\ContratClient;
use App\Form\CompteType;
use App\Form\CompteClientType;
use App\Repository\ContratClientRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Controller\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ContratClientController extends AbstractController
{
    #[Route('/contrat/client', name: 'app_contrat_client')]
    public function index(): Response
    {
        return $this->render('contrat_client/index.html.twig', [
            'controller_name' => 'ContratClientController',
        ]);
    }
    #[Route('/client/contrat/{id}', name: 'contratClient_edit')]
    public function editContrat($id, Request $request, EntityManagerInterface $entityManager)
    {
        // Trouvez le compte client par son ID
        $contratClient = $entityManager->getRepository(ContratClient::class)->find($id);

        // Gérer le cas où le compte client n'existe pas
        if (!$contratClient) {
            throw $this->createNotFoundException('Aucun contrat client trouvé pour cet ID.');
        }

        // Créez le formulaire en passant le compte client existant pour pré-remplir les données
        $formContrat = $this->createForm(ContratClientType::class, $contratClient);

        $formContrat->handleRequest($request);

        if ($formContrat->isSubmitted() && $formContrat->isValid()) {
            // Si le formulaire est soumis et valide, mettez à jour et enregistrez le compte client

            $contratClient->setTarifMensuel($formContrat->get('tarifMensuel')->getData());
            $entityManager->flush();

            // Ajoutez un message flash indiquant le succès de l'opération
            $this->addFlash('success', 'Le tarif mensuel a été mis à jour.');

            // Redirigez l'utilisateur vers la page appropriée
            return $this->redirectToRoute('contrat_edit', ['id' => $contratClient->getId()]);
        }

        return $this->render('contrat_client/edit.html.twig', [
            'formContrat' => $formContrat->createView(),
        ]);
    }

    #[Route("/client/contrat/delete/{id}", name: "contratClient_delete", methods: ["POST"])]
    public function deleteContratClient(int $id, EntityManagerInterface $entityManager): Response
    {
        $contratClient = $entityManager->getRepository(ContratClient::class)->find($id);

        $clientId = $contratClient->getClient()->getId();

        if (!$contratClient) {
            $this->addFlash('error', 'Contrat associé au client non trouvé');
            return $this->redirectToRoute('client_infos', ['id' => $clientId]);
        }

        $entityManager->remove($contratClient);
        $entityManager->flush();

        $this->addFlash('success', 'Contrat associé au client supprimé avec succès');

        return $this->redirectToRoute('client_infos', ['id' => $clientId]);
    }

    #[Route('/client/contrat/{id}', name: 'contratClient_list')]
    public function listContratClient(EntityManagerInterface $entityManager, $id): Response
    {
        return $this->redirectToRoute('client_infos');
    }
    #[Route('/contrat/ouverture/{id}', name: 'contrat_ouverture')]
    public function ouvertureContrat(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, int $id): Response
    {
        $calendar = $entityManager->getRepository(Calendar::class)->find($id);

        if (!$calendar) {
            throw new NotFoundHttpException('Rendez-vous non trouvé');
        }

        $client = $calendar->getClients(); // Assurez-vous que c'est bien la méthode appropriée pour obtenir le client

        $libelleMotif = $calendar->getMotif()->getLibelleMotif();
        $contratExistant = $entityManager->getRepository(Contrat::class)->findOneBy(['nomContrat' => $libelleMotif]);

        if (!$contratExistant) {
            throw new \Exception("Aucun contrat avec le nom spécifié n'a été trouvé.");
        }

        $contratClientExistant = $entityManager->getRepository(ContratClient::class)->findOneBy([
            'contrat' => $contratExistant,
            'client' => $client
        ]);

        if ($contratClientExistant) {
            $session->set('contratCreationStatus', 'existant');
            return $this->redirectToRoute('some_route', ['id' => $contratClientExistant->getId()]);
        }

        $contratClient = new ContratClient();
        $formContrat = $this->createForm(ContratClientType::class, $contratClient);
        $formContrat->handleRequest($request);

        if ($formContrat->isSubmitted() && $formContrat->isValid()) {
            // ... logique de création de ContratClient
            $contratClient->setDateOuvertureContrat(new \DateTime()); // Date actuelle
            $tarifMensuel = $formContrat->get('tarifMensuel')->getData();
            $contratClient->setTarifMensuel($tarifMensuel);
            $contratClient->setContrat($contratExistant); // Associez le contrat existant
            $client->addContratClient($contratClient); // Associez le contrat client au client

            $entityManager->persist($contratClient);
            $entityManager->flush();

            $session->set('contratCreationStatus', 'nouveau');
            return $this->redirectToRoute('calendar_show', ['id' => $calendar->getId()]);
        }

        // Si le formulaire n'est pas soumis ou n'est pas valide, retournez le formulaire
        return $this->render('calendar/show.html.twig', [
            'formContrat' => $formContrat->createView(),
            'calendar' => $calendar,
        ]);
    }

    #[Route('/contrat/statistiques', name: 'contrat_statistiques')]
    public function statistiques(Request $request, ContratClientRepository $contratClientRepository, SessionInterface $session): Response
    {

        $formStatContrat = $this->createForm(StatistiqueContratType::class);
        $formStatContrat->handleRequest($request);

        if ($formStatContrat->isSubmitted() && $formStatContrat->isValid()) {
            $data = $formStatContrat->getData();
            $nombreContrats = $contratClientRepository->countContratByDate($data['startDateContrat'], $data['endDateContrat']);

            $session->set('searchContrat', true);
            $session->set('nombreContrats', $nombreContrats);
            $session->set('startDateContrat', $data['startDateContrat']);
            $session->set('endDateContrat', $data['endDateContrat']);

            return $this->redirectToRoute('app_directeur');
        }

        return $this->render('contrat/statistiques.html.twig', [
            'formStatContrat' => $formStatContrat->createView(),
        ]);
    }
}
