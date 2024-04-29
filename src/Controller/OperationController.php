<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\OperationType;
use App\Form\CompteClientType;
use App\Entity\CompteClient;
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
        $compteClient = $entityManager->getRepository(CompteClient::class)->find($id);
        $formOpe = $this->createForm(OperationType::class);

        if (!$compteClient) {
            $this->addFlash('error', 'Aucun compte client trouvé pour cet ID.');
        }
        $formCompte = $this->createForm(CompteClientType::class, $compteClient);

        $formCompte->handleRequest($request);

        if ($formCompte->isSubmitted() && $formCompte->isValid()) {

            $compteClient->setMontantDecouvert($formCompte->get('montantDecouvert')->getData());
            $entityManager->flush();

            $this->addFlash('success', 'Le montant découvert a été mis à jour.');

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
            return $this->redirectToRoute('compte_client_index'); 
        }

        $operation = new Operation();
        $formOpe = $this->createForm(OperationType::class, $operation);
        $formCompte = $this->createForm(CompteClientType::class);
        $formOpe->handleRequest($request);

        if ($formOpe->isSubmitted() && $formOpe->isValid()) {
            $typeOpe = $operation->getTypeOperation();
            $montant = $operation->getMontant();

            $operation->setCompte($compteClient->getCompte());
            $operation->setClient($compteClient->getClient());

            $entityManager->persist($operation);

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
            return $this->redirectToRoute('compte_ope', ['id' => $compteClient->getId()]); 
        }

        return $this->render('operation/index.html.twig', [
            'formOpe' => $formOpe->createView(),
            'formCompte' => $formCompte->createView(),
            'compteClientId' => $compteClientId,
            'compteClient' => $compteClient
        ]);
    }

}
