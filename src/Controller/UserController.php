<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/users', name: 'app_users')]//Fontion pour afficher la liste des utilisateurs
    public function list(UserRepository $userRepository): Response
    {
        $users = $userRepository->getAllUsers();
        //dd($users);
        
        return $this->render('user/admin.html.twig', [
            'controller_name' => 'userController',
            'users' => $users,
        ]);
    }
    #[Route('/user/{id}', name: 'app_user_show', requirements: ['id' => '\d+'])]//Fonction pour afficher un utilisateur en particulier grâce à son id
    public function show(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->getUserById($id);
        //dd($user);
        return $this->render('user/userDetails.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
        ]);
    }



    #[Route('/user/delete/{id}', name: 'app_user_delete')]//Fonction pour supprimer un utilisateur
    public function delete(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.');
        return $this->redirectToRoute('app_users');
    }
}
