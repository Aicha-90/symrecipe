<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'app_ingredient', methods:['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findAll(), 
            $request->query->getInt('page', 1),
            10 
        );
       
        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    #[Route('/ingredient/nouveau', 'ingredient.new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {
        $ingredient = new Ingredient();
        
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été crée avec succès !'
            );

            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('pages/ingredient/new.html.twig', ['form' => $form]);
    }

    #[Route('/ingredient/edition/{id}', 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $manager, Ingredient $ingredient) : Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès !'
            );

            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('pages/ingredient/edit.html.twig', ['form' => $form]);
    }

    #[Route('/ingredient/supression/{id}', 'ingredient.delete', methods: ['GET'])]
    public function delete(Request $request, EntityManagerInterface $manager, Ingredient $ingredient) : Response
    {
        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre ingrédient a été supprimé avec succès !'
        );

        return $this->redirectToRoute('app_ingredient');
        
    }
}
