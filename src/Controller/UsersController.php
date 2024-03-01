<?php
namespace App\Controller;

use App\Entity\Directeur;
use App\Entity\Conseiller;
use App\Entity\Agent;
use App\Entity\Users;
use App\Entity\Contrat;
use App\Form\RegistrationFormType;
use App\Form\UsersType;
use App\Security\UsersAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;


class UsersController extends AbstractController
{
    #[Route('/employe/inscription', name: 'user_ajout')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $type = $form->get('type')->getData(); 
            switch ($type) {
                case 'Conseiller':
                    $user->setRoles(['ROLE_CONSEILLER']);
                    break;
                case 'Agent':
                    $user->setRoles(['ROLE_AGENT']);
                    break;
                case 'Directeur':
                    $user->setRoles(['ROLE_DIRECTEUR']);
                    break;
                // Ajoutez d'autres cas si nécessaire
                default:
                    $user->setRoles(['ROLE_USER']); // Un rôle par défaut
                    break;
            }
    
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
    
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }
    
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    
    #[Route("/employe/edit/{id}", name: "user_edit")]
    public function editUsers(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, int $id): Response
    {
        $user = $entityManager->getRepository(Users::class)->find($id);

        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour l'username
            $user->setUsername($form->get('username')->getData());

            // Hash and set the new password
            if (!empty($form->get('plainPassword')->getData())) {
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }
            $entityManager->flush();

            $this->addFlash('success', 'Les informations de l\'utilisateur ont été modifiées avec succès.');

            return $this->redirectToRoute('users_list');
        }

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    #[Route("/employe/delete/{id}", name: "user_delete", methods: ["POST"])]
    public function deleteUser(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(Users::class)->find($id);
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé');
            return $this->redirectToRoute('users_list');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès');
        return $this->redirectToRoute('users_list');
    }
    #[Route('/users/list', name: 'users_list')]
    public function listUsers(EntityManagerInterface $entityManager): Response
    {
        return $this->redirectToRoute('app_main');
    }
    /*
        #[Route("/user/create", name: "user_create", methods: "POST")]
        public function create(Request $request, EntityManagerInterface $entityManager): Response
        {
            $type = $request->request->get('type'); 
            error_log(print_r($type, true));
            dd($type);// Ici, nous lisons le type du formulaire
            if ($type === 'Directeur') {
                $users = new Directeur();
            } elseif ($type === 'Conseiller') {
                $users = new Conseiller();
            } elseif ($type === 'Agent') {
                $users = new Agent();
            } else {
                
                // Gérer le cas où aucun type valide n'est sélectionné
                throw new \Exception('Type d\'employé invalide.');
            }
        
            // Maintenant, définissez les propriétés sur l'objet du type correct.
            $users->setLastname($request->request->get('username'));
            $users->setFirstname($request->request->get('prenom'));
            $users->setUsername($request->request->get('login'));
            $users->setPassword($request->request->get('motDePasse')); // Pensez à hasher le mot de passe
        
            // Enregistrer l'entité dans la base de données
            $entityManager->persist($users);
            $entityManager->flush();
        
            // Rediriger l'utilisateur ou afficher un message de succès
            return new Response('Employé ajouté avec succès');
        }*/
}
