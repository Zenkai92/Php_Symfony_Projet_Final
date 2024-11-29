<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]
    public function index(): Response
    {
        return $this->render('post/postList.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    #[Route('/posts', name: 'app_posts')] // Fonction pour afficher la liste des articles
    public function list(PostRepository $postRepository): Response
    {
        $posts = $postRepository->getAllposts();

        return $this->render('post/postList.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post/{id}', name: 'app_post')] // Fonction pour afficher un article en particulier grâce à son id
    public function show(
        Request $request,
        EntityManagerInterface $entityManager,
        ?Post $post,
        CommentRepository $commentRepository
    ): Response {
        if (!$post) {
            throw $this->createNotFoundException('Article introuvable.');
        }

        $comments = $commentRepository->findBy(['post' => $post]);

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setPost($post);
            $comment->setUser($this->getUser());
            $comment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a été ajouté.');
            return $this->redirectToRoute('app_post', ['id' => $post->getId()]);
        }

        return $this->render('post/postDetails.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    #[Route('/post_add', name: 'app_post_add')] // Fonction pour ajouter un article
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        $categories = $entityManager->getRepository(Category::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if ($user) {
                $post->setUser($user);
            }
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Article ajouté avec succès.');
            return $this->redirectToRoute('app_posts');
        }

        return $this->render('post/postForm.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
        ]);
    }

    #[Route('/post/delete/{id}', name: 'app_post_delete')] // Fonction pour supprimer un article
    public function delete(Post $post, EntityManagerInterface $entityManager, CommentRepository $commentRepository): Response
    {
        // Supprimer tous les commentaires associés à ce post
        $comments = $commentRepository->findBy(['post' => $post]);
        foreach ($comments as $comment) {
            $entityManager->remove($comment);
        }

        // Supprimer le post
        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', 'L\'article et ses commentaires ont été supprimés avec succès.');
        return $this->redirectToRoute('app_posts');
    }

    #[Route('/post/edit/{id}', name: 'app_post_edit')] // Fonction pour modifier un article
    public function edit(
        $id,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $post = $entityManager->getRepository(Post::class)->find($id);
        if (!$post) {
            throw $this->createNotFoundException('L\'article n\'existe pas.');
        }

        $categories = $entityManager->getRepository(Category::class)->findAll();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Article modifié avec succès.');
            return $this->redirectToRoute('app_posts');
        }

        return $this->render('post/postEdit.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
            'post' => $post,
        ]);
    }
}
