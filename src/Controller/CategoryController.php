<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategoryRepository;
use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        return $this->render('category/categoryList.html.twig', [
            'controller_name' => 'categoryController',
        ]);

    }

    #[Route('/categorys', name: 'app_categorys')]//Fonction pour lister les categories
    public function list(categoryRepository $categoryRepository): Response
    {
        $categorys = $categoryRepository->getAllcategorys();
 
        
        return $this->render('category/categoryList.html.twig', [
            'controller_name' => 'categoryController',
            'categorys' => $categorys,
        ]);
    }
    #[Route('/category/{id}', name: 'app_category', requirements: ['id' => '\d+'])]//Fonction pour avoir les categories en fonction de leur ID
    public function show(int $id, categoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->getcategoryById($id);

        return $this->render('category/categoryDetails.html.twig', [
            'controller_name' => 'categoryController',
            'category' => $category,
        ]);
    }

    #[Route('/category_add', name: 'app_category_add')]//Fonction pour ajouter une categorie
public function add(Request $request, EntityManagerInterface $entityManager): Response
{
    $category = new category();
    $form = $this->createForm(categoryType::class, $category);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($category);
        $entityManager->flush();
        return $this-> redirectToRoute('app_categorys', ['id' => $category->getId()]);    
        }
   

    return $this->render('category/categoryForm.html.twig', [
        'controller_name' => 'categoryController',
        'form' => $form->createView(),  
    ]);
    }
    
    #[Route('/category/delete/{id}', name: 'app_category_delete')]//Fonction pour supprimer une categorie
    public function delete(category $category, EntityManagerInterface $entityManager, Request $request): Response
    {
        
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();

            $this->addFlash('success', 'Le category a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_categorys');
    }
   #[Route('/category/edit/{id}', name: 'app_category_edit', requirements: ['id' => '\d+'])]//fonction pour modifier une categorie
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, categoryRepository $categoryRepository): Response
    {
        // Récupérer le category à modifier
        $category = $categoryRepository->getcategoryById($id);

        if (!$category) {
            throw $this->createNotFoundException('Le category n\'existe pas.');
        }

        // Créer le formulaire
        $form = $this->createForm(categoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'category modifié avec succès.');

            // Rediriger vers la page de détail du category ou la liste des categorys
            return $this->redirectToRoute('app_category', ['id' => $category->getId()]);
        }

        return $this->render('category/categoryForm.html.twig', [
            'controller_name' => 'categoryController',
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }
}
