<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Restaurant;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $user = $this->getUser();
        
        $entityManager = $this->getDoctrine()->getManager();
        // Nous récupérons la liste des catégories à transmettre au sidebar
        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        //récupérer les différents restaurants pour chaque catégory
        $restaurantRepository = $entityManager->getRepository(Restaurant::class);
        $restaurants = $restaurantRepository->findAll();
        shuffle($restaurants);

        return $this->render('index/index.html.twig', [
            'categories' => $categories,
            'restaurants' => $restaurants,
            'user' => $user,
        ]);
    }

}
