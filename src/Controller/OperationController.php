<?php

namespace App\Controller;

use App\Entity\ContratClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\OperationType;
use App\Form\CompteClientType;
use App\Form\ContratClientType;
use App\Entity\CompteClient;
use App\Entity\Client;
use App\Entity\Operation;
use Symfony\Component\HttpFoundation\Request;

class OperationController extends AbstractController
{
    #[Route('/operation', name: 'app_operation')]
    public function index(): Response
    {
        return $this->render('operation/index.html.twig', [
            'controller_name' => 'OperationController',
        ]);
    }
    #[Route('/compte/ope/{id}', name: 'compte_ope')]
    public function editCompte($id, Request $request, EntityManagerInterface $entityManager)
    {
        // Trouvez le compte client par son ID
        $compteClient = $entityManager->getRepository(CompteClient::class)->find($id);
        $formOpe = $this->createForm(OperationType::class);

        // Gérer le cas où le compte client n'existe pas
        if (!$compteClient) {
            throw $this->createNotFoundException('Aucun compte client trouvé pour cet ID.');
        }

        // Créez le formulaire en passant le compte client existant pour pré-remplir les données
        $formCompte = $this->createForm(CompteClientType::class, $compteClient);

        $formCompte->handleRequest($request);

        if ($formCompte->isSubmitted() && $formCompte->isValid()) {
            // Si le formulaire est soumis et valide, mettez à jour et enregistrez le compte client

            $compteClient->setMontantDecouvert($formCompte->get('montantDecouvert')->getData());
            $entityManager->flush();

            // Ajoutez un message flash indiquant le succès de l'opération
            $this->addFlash('success', 'Le montant découvert a été mis à jour.');

            // Redirigez l'utilisateur vers la page appropriée
            return $this->redirectToRoute('compte_ope', ['id' => $compteClient->getId()]);
        }

        return $this->render('operation/index.html.twig', [
            'formOpe' => $formOpe->createView(),
            'formCompte' => $formCompte->createView(),
            'compteClient' => $compteClient
        ]);
    }
    #[Route('/operation/create/{compteClientId}', name: 'operation_create')]
    public function createOperation(Request $request, EntityManagerInterface $entityManager, int $compteClientId): Response
    {
        $compteClient = $entityManager->getRepository(CompteClient::class)->find($compteClientId);

        if (!$compteClient) {
            $this->addFlash('error', "Compte client non trouvé.");
            return $this->redirectToRoute('compte_client_index'); // Rediriger vers une liste de comptes clients ou autre
        }

        $operation = new Operation();
        $formOpe = $this->createForm(OperationType::class, $operation);
        $formCompte = $this->createForm(CompteClientType::class);
        $formOpe->handleRequest($request);

        if ($formOpe->isSubmitted() && $formOpe->isValid()) {
            // Traitement de l'opération...
            $typeOpe = $operation->getTypeOperation();
            $montant = $operation->getMontant();

            $operation->setCompte($compteClient->getCompte());
            $operation->setClient($compteClient->getClient());

            $entityManager->persist($operation);

            // Mise à jour du solde du compte client selon le type d'opération
            if ($typeOpe === 'dépôt') {
                if ($montant >= 0) {
                    $compteClient->setSolde($compteClient->getSolde() + $montant);
                } else {
                    $this->addFlash('error', 'Le montant du dépôt ne peut pas être négatif.');
                    return $this->redirectToRoute('compte_ope', ['id' => $compteClient->getId()]);
                }
            } elseif ($typeOpe === 'retrait' && $compteClient->getSolde() - $montant >= -$compteClient->getMontantDecouvert()) {
                $compteClient->setSolde($compteClient->getSolde() - $montant);
            } else {
                $this->addFlash('error', "Le retrait dépasse le montant de découvert autorisé.");
                return $this->redirectToRoute('compte_ope', ['id' => $compteClient->getId()]);
            }

            $entityManager->flush();

            $this->addFlash('success', 'L\'opération a été enregistrée avec succès.');
            return $this->redirectToRoute('compte_ope', ['id' => $compteClient->getId()]); // Assurez-vous que cette route existe
        }

        // Afficher le formulaire en cas de requête GET ou de formulaire non valide
        return $this->render('operation/index.html.twig', [
            'formOpe' => $formOpe->createView(),
            'formCompte' => $formCompte->createView(),
            'compteClientId' => $compteClientId,
            'compteClient' => $compteClient
        ]);
    }

}
