<?php

namespace App\Controller;

use App\Entity\Contrat;
use App\Entity\Users;
use App\Form\ContratType;
use App\Repository\ContratRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ContratController extends AbstractController
{
    #[Route('/contrat/ajout', name: 'ajout_contrat')]
    public function ajoutContrat(Request $request, EntityManagerInterface $entityManager)
    {
        $contrat = new Contrat();
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contrat);
            $entityManager->flush();

            // Redirection ou affichage d'un message de succès
            return $this->redirectToRoute('main');
        }

        return $this->render('contrat/index.html.twig', [
            'form' => $form->createView(),
            'nomContrat' => $contrat->getNomContrat()
        ]);
    }
    #[Route("/contrat/edit/{id}", name: "contrat_edit")]
    public function editUsers(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $contrat = $entityManager->getRepository(Contrat::class)->find($id);

        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour le contrat
            $contrat->setNomContrat($form->get('nomContrat')->getData());
            $entityManager->flush();

            $this->addFlash('success', 'Les informations du nom de contrat ont été modifiées avec succès.');

            return $this->redirectToRoute('contrat_list');
        }

        return $this->render('contrat/edit.html.twig', [
            'contrats' => $contrat,
            'form' => $form->createView(),
        ]);
    }
    #[Route("/contrat/delete/{id}", name: "contrat_delete", methods: ["POST"])]
    public function deleteContrat(int $id, EntityManagerInterface $entityManager): Response
    {
        $contrat = $entityManager->getRepository(Contrat::class)->find($id);

        if (!$contrat) {
            // Handle the case where the contrat does not exist
            $this->addFlash('error', 'Contrat non trouvé');
            return $this->redirectToRoute('contrat_list');
        }

        $entityManager->remove($contrat);
        $entityManager->flush();

        // Add a flash message or some kind of notification to let the contrat know it was successful
        $this->addFlash('success', 'Contrat supprimé avec succès');

        return $this->redirectToRoute('contrat_list');
    }
    #[Route('/contrat/list', name: 'contrat_list')]
    public function listContrats(EntityManagerInterface $entityManager): Response
    {
        return $this->redirectToRoute('app_main');
    }
}
