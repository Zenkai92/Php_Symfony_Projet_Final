<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\HttpFoundation\RedirectToRouteException;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/users', name: 'app_users')]
    public function list(UserRepository $userRepository): Response
    {
        // Vérifie si l'utilisateur connecté est un administrateur
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.');
            return $this->redirectToRoute('app_main'); // Remplacez 'app_home' par votre route d'accueil
        }

        $users = $userRepository->findAll(); // Méthode par défaut pour récupérer les utilisateurs
        return $this->render('user/admin.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/{id}', name: 'app_user_show', requirements: ['id' => '\d+'])]
    public function show(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id); // Utilisez `find()` pour une récupération standard

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        return $this->render('user/userDetails.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/delete/{id}', name: 'app_user_delete')]
    public function delete(User $user, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur connecté est un administrateur
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas les autorisations nécessaires pour effectuer cette action.');
            return $this->redirectToRoute('app_main'); // Remplacez 'app_home' par votre route d'accueil
        }

        // Supprimer les commentaires associés à cet utilisateur avant de le supprimer
        foreach ($user->getComment() as $comment) {
            $entityManager->remove($comment);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'L\'utilisateur et ses commentaires associés ont été supprimés avec succès.');
        return $this->redirectToRoute('app_users');
    }
}
