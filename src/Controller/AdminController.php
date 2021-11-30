<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Entity\User;
use App\Form\PlatType;
use App\Entity\Category;
use App\Entity\Commande;
use App\Entity\Restaurant;
use App\Form\RestaurantType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin-backoffice-restaurant", name="admin_backoffice_restaurant")
     */
    public function indexBackofficeRestaurant()
    {
        $entityManager = $this->getDoctrine()->getManager();
        // Nous récupérons la liste des catégories à transmettre au sidebar
        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $restaurantRepository = $entityManager->getRepository(Restaurant::class);
        $restaurants = $restaurantRepository->findAll();
        
        return $this->render('admin/admin-backoffice-restaurant.html.twig', [
            'categories' => $categories,
            'restaurants' => $restaurants,
        ]);
    }

    /**
     * @Route("/admin-backoffice-user", name="admin_backoffice_user")
     */
    public function indexBackofficeUser()
    {
        $entityManager = $this->getDoctrine()->getManager();
        // Nous récupérons la liste des catégories à transmettre au sidebar
        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $userRepository = $entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        return $this->render('admin/admin-backoffice-user.html.twig', [
            'categories' => $categories,
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin-backoffice-commande", name="admin_backoffice_commande")
     */
    public function indexBackofficeCommande()
    {
        $entityManager = $this->getDoctrine()->getManager();
        // Nous récupérons la liste des catégories à transmettre au sidebar
        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $commandeRepository = $entityManager->getRepository(Commande::class);
        $commandes = $commandeRepository->findAll();

        return $this->render('admin/admin-backoffice-commande.html.twig', [
            'categories' => $categories,
            'commandes' => $commandes,
        ]);
    }

    /**
     * @Route("/restaurant/create", name="admin_restaurant_create")
     */
    public function createRestaurant(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        // Nous récupérons la liste des catégories à transmettre au sidebar
        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $restaurant = new Restaurant;
        $restaurantForm = $this->createForm(RestaurantType::class, $restaurant);
        // Nous appliquons la Request à notre formulaire et si valide, nous persistons
        $restaurantForm->handleRequest($request);
        if ($request->isMethod('post') && $restaurantForm->isValid()) {
            $entityManager->persist($restaurant);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('admin_backoffice_restaurant'));
        }

        return $this->render('index/dataform.html.twig', [
            'categories' => $categories,
            'formName' => 'Création d\'un nouveau Restaurant',
            'dataForm' => $restaurantForm->createview(),
        ]);
    }

    /**
     * @Route("/restaurant/delete/{restaurantId}", name="admin_restaurant_delete")
     */
    public function deleteRestaurant($restaurantId = false)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $restaurantRepository = $entityManager->getRepository(Restaurant::class);
        $restaurant = $restaurantRepository->find($restaurantId);

        if (!$restaurant) {
            return $this->redirect($this->generateUrl('admin_backoffice_restaurant'));
        }

        $entityManager->remove($restaurant);
        $entityManager->flush();
        return $this->redirect($this->generateUrl('admin_backoffice_restaurant'));
    }

    /**
     * @Route("/restaurant/update/{restaurantId}", name="admin_restaurant_update")
     */
    public function updateRestaurant(Request $request, $restaurantId = false)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $restaurantRepository = $entityManager->getRepository(Restaurant::class);
        $restaurant = $restaurantRepository->find($restaurantId);

        if (!$restaurant) {
            return $this->redirect($this->generateUrl('admin_backoffice_restaurant'));
        }

        $restaurantForm = $this->createForm(RestaurantType::class, $restaurant);

        $restaurantForm->handlerequest($request);

        if ($request->isMethod('post') && $restaurantForm->isValid()) {
            $entityManager->persist($restaurant);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('admin_backoffice_restaurant'));
        }

        return $this->render('index/dataform.html.twig', [
            'categories' => $categories, 
            'formName' => 'Modification du Restaurant',
            'dataForm' => $restaurantForm->createview(),
        ]);
    }

    /**
     * @Route("/plat/create/{restaurantId}", name="admin_plat_create")
     */
    public function createPlat(Request $request, $restaurantId=false)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $restaurantRepository = $entityManager->getRepository(Restaurant::class);

        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $restaurant = $restaurantRepository->find($restaurantId);

        if (!$restaurant) {
            return $this->redirect($this->generateUrl('index'));
        }

        $plat = new Plat;
        $platForm = $this->createForm(PlatType::class, $plat);

        $platForm->handleRequest($request);
        if ($request->isMethod('post') && $platForm->isValid()) {
            $plat->setRestaurant($restaurant);
            $entityManager->persist($plat);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('admin_backoffice_restaurant'));
        }

        return $this->render('index/dataform.html.twig', [
            'categories' => $categories,
            'formName' => 'Création du Plat',
            'dataForm' => $platForm->createview(),
        ]);

    }

    /**
     * @Route("/plat/delete/{platId}", name="admin_plat_delete")
     */
    public function deletePlat($platId=false)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $platRepository = $entityManager->getRepository(Plat::class);
        $plat = $platRepository->find($platId);

        if (!$plat) {
            return $this->redirect($this->generateUrl('admin_backoffice_restaurant'));
        }

        $entityManager->remove($plat);
        $entityManager->flush();
        return $this->redirect($this->generateUrl('admin_backoffice_restaurant'));
    }

    /**
     * @Route("/plat/update/{platId}", name="admin_plat_update")
     */
    public function updatePlat(Request $request, $platId=false)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $platRepository = $entityManager->getRepository(Plat::class);
        $plat = $platRepository->find($platId);

        if (!$plat) {
            return $this->redirect($this->generateUrl('admin_backoffice_restaurant'));
        }

        $platForm = $this->createForm(PlatType::class, $plat);

        $platForm->handlerequest($request);

        if ($request->isMethod('post') && $platForm->isValid()) {
            $entityManager->persist($plat);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('admin_backoffice_restaurant'));
        }

        return $this->render('index/dataform.html.twig', [
            'categories' => $categories,
            'formName' => 'Modification du Plat',
            'dataForm' => $platForm->createview(),
        ]);
    }
}
