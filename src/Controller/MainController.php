<?php

// src/Controller/MainController.php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(PostRepository $postRepository)
    {
        // Récupérer les 3 derniers articles publiés
        $recentPosts = $postRepository->findBy([], ['publishedAt' => 'DESC'], 3);

        return $this->render('main/index.html.twig', [
            'recentPosts' => $recentPosts,
        ]);
    }
}
