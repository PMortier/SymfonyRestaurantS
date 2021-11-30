<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Form\PlatType;
use App\Entity\Category;
use App\Entity\Restaurant;
use App\Entity\Reservation;
use App\Form\RestaurantType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class RestaurantController extends AbstractController
{
    /**
     * @Route("/restaurant/{restaurantId}", name="restaurant")
     */
    public function indexRestaurant(Request $request, $restaurantId = false): Response
    {
        $user=$this->getUser();

        $entityManager = $this->getDoctrine()->getManager();

        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $restaurantRepository = $entityManager->getRepository(Restaurant::class);
        $restaurant =  $restaurantRepository->find($restaurantId);

        if(!$restaurant){
            return $this->redirect($this->generateUrl('index'));
        }

        $plats = $restaurant->getPlats();
        $category = $restaurant->getCategory();

        $commandes = $restaurant->getCommandes();
        $activeCommande = null;
        foreach ($commandes as $commandeUnit) {
            if ($commandeUnit->getStatus() == "panier" && $commandeUnit->getClient() == $user) {
                $activeCommande = $commandeUnit;
            }
        }

        $totalPrice = 0;

        if($activeCommande){
            $reservations = $activeCommande->getReservations();
            foreach ($reservations as $reservation) {
                $platPrice = $reservation->getPlat()->getPrice();
                $platQuantity = $reservation->getQuantity();
                $totalPrice += $platPrice * $platQuantity;
            }
        }
        
        return $this->render('index/restaurant.html.twig', [
            'categories' => $categories,
            'category' => $category,
            'restaurant' => $restaurant,
            'plats' => $plats,
            'activeCommande' => $activeCommande,
            'totalPrice' => $totalPrice,
        ]);
    }

    /**
     * @Route("/restaurant/delete/{restaurantId}", name="restaurant_delete")
     */
    public function deleteRestaurant($restaurantId = false)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $restaurantRepository = $entityManager->getRepository(Restaurant::class);
        $restaurant = $restaurantRepository->find($restaurantId);

        if (!$restaurant) {
            return $this->redirect($this->generateUrl('index'));
        }

        $plats = $restaurant->getPlats();
        $entityManager->remove($plats);
        $entityManager->remove($restaurant);
        $entityManager->flush();
        return $this->redirect($this->generateUrl('index'));
    }

    /**
     * @Route("/restaurant/update/{restaurantId}", name="restaurant_update")
     */
    public function updateRestaurant(Request $request, $restaurantId = false)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $restaurantRepository = $entityManager->getRepository(Restaurant::class);
        $restaurant = $restaurantRepository->find($restaurantId);

        if (!$restaurant) {
            return $this->redirect($this->generateUrl('index'));
        }

        $restaurantForm = $this->createForm(RestaurantType::class, $restaurant);

        $restaurantForm->handlerequest($request);

        if ($request->isMethod('post') && $restaurantForm->isValid()) {
            $entityManager->persist($restaurant);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('restaurant', ['restaurantId' => $restaurantId]));
        }

        return $this->render('index/dataform.html.twig', [
            'categories' => $categories,
            'formName' => 'Modification du Restaurant',
            'dataForm' => $restaurantForm->createview(),
        ]);
    }

    /**
     * @Route("/plat/create/{restaurantId}", name="plat_create")
     */
    public function createPlat(Request $request, $restaurantId=false)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $restaurantRepository = $entityManager->getRepository(Restaurant::class);

        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $restaurant = $restaurantRepository->find($restaurantId);

        if(!$restaurant){
            return $this->redirect($this->generateUrl('index'));
        }

        $plat = new Plat;
        $platForm = $this->createForm(PlatType::class, $plat);

        $platForm->handleRequest($request);
        if($request->isMethod('post') && $platForm->isValid()){
            $plat->setRestaurant($restaurant);
            $entityManager->persist($plat);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('restaurant', ['restaurantId'=>$restaurantId]));
        }

        return $this->render('index/dataform.html.twig', [
            'categories' => $categories,
            'formName' => 'Création du Plat',
            'dataForm' => $platForm->createview(),
        ]);

    }

    /**
     * @Route("/plat/delete/{platId}", name="plat_delete")
     */
    public function deletePlat($platId = false)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $platRepository = $entityManager->getRepository(Plat::class);
        $plat = $platRepository->find($platId);

        $restaurantId = $plat->getRestaurant()->getId();

        if (!$plat) {
            return $this->redirect($this->generateUrl('index'));
        }

        $entityManager->remove($plat);
        $entityManager->flush();
        return $this->redirect($this->generateUrl('restaurant', ['restaurantId' => $restaurantId ]));
    }

    /**
     * @Route("/plat/update/{platId}", name="plat_update")
     */
    public function updatePlat(Request $request, $platId = false)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $platRepository = $entityManager->getRepository(Plat::class);
        $plat = $platRepository->find($platId);

        $restaurantId = $plat->getRestaurant()->getId();

        if (!$plat) {
            return $this->redirect($this->generateUrl('index'));
        }

        $platForm = $this->createForm(PlatType::class, $plat);

        $platForm->handlerequest($request);

        if ($request->isMethod('post') && $platForm->isValid()) {
            $entityManager->persist($plat);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('restaurant', ['restaurantId' => $restaurantId]));
        }

        return $this->render('index/dataform.html.twig', [
            'categories' => $categories,
            'formName' => 'Modification du Plat',
            'dataForm' => $platForm->createview(),
        ]);
    }

}
