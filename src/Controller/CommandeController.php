<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Entity\Commande;
use App\Entity\Restaurant;
use App\Entity\Reservation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/commande")
 * @Security("is_granted('ROLE_USER')")
 */
class CommandeController extends AbstractController
{

    /**
     * @Route("/commande/reservation/{platId}", name="reservation")
     */
    public function platReservation($platId=false)
    {
        $user = $this->getUser();
        
        $entityManager = $this->getDoctrine()->getManager();
        $platRepository = $entityManager->getRepository(Plat::class);
        $plat = $platRepository->find($platId);

        if(!$plat){
            return $this->redirect($this->generateUrl('index'));
        }

        $restaurant = $plat->getRestaurant();
        $restaurantId = $restaurant->getId();

        if (!$user || !in_array('ROLE_CLIENT', $user->getRoles(), true)) {
            return $this->redirect($this->generateUrl('restaurant', ['restaurantId' => $restaurantId]));
        }

        if(!$restaurant){
            return $this->redirect($this->gerenarteUrl('index'));
        }
        
        $commandes = $restaurant->getCommandes();
        $activeCommande = null;
        foreach ($commandes as $commandeUnit) {
            if ($commandeUnit->getStatus() == "panier" && $commandeUnit->getClient() == $user) {
                $activeCommande = $commandeUnit;
            }
        }

        //si pas déjà de commande dans le resto -> creation d'une nouvelle commande avec la nouvelle réservation
        //si déjà une commande dans le resto -> récupération de la commande active pour faire la réservations de plat
            //voir si déjà une réservation du plat choisi
                //si oui, ajouter 1 à la quantité de la réservation existante
                //si non, créer une nouvelle réservation et ajouter 1 à la quantité de la réservation

        if($user && !$activeCommande)
        {
            $commande = new Commande;
            $commande->setStatus("panier");
            $commande->setRestaurant($restaurant);
            $commande->setClient($user);
            $reservation = new Reservation;
            $commande->addReservation($reservation);
        } 
        else if($user && $activeCommande) 
        {
            $commande = $activeCommande;
            $reservations = $commande->getReservations();
            
            $reservation = null;
            foreach ($reservations as $reservationUnit){
                if($reservationUnit->getPlat()->getId() == $platId){
                    $reservation = $reservationUnit;
                } else {
                    $reservation = new Reservation;
                    $commande->addReservation($reservation);
                }
            }
        }

        $reservation->setPlat($plat);
        $reservation->setquantity($reservation->getQuantity()+1); 

        $entityManager->persist($commande);
        $entityManager->persist($reservation);

        $entityManager->flush();


        return $this->redirect($this->generateUrl('restaurant', [ 'restaurantId' => $restaurantId ]));

    }

    /**
     * @Route("/commande/reservation/delete/{platId}", name="reservation_delete")
     */
    public function deleteReservation($platId=false)
    {
        $user = $this->getUser();
        
        $entityManager = $this->getDoctrine()->getManager();
        $platRepository = $entityManager->getRepository(Plat::class);
        $plat = $platRepository->find($platId);

        if (!$plat) {
            return $this->redirect($this->generateUrl('index'));
        }

        $restaurant = $plat->getRestaurant();
        $restaurantId = $restaurant->getId();

        if (!$user || !in_array('ROLE_CLIENT', $user->getRoles(), true)) {
            return $this->redirect($this->generateUrl('restaurant', ['restaurantId' => $restaurantId]));
        }

        if (!$restaurant) {
            return $this->redirect($this->gerenarteUrl('index'));
        }

        $commandes = $restaurant->getCommandes();
        $activeCommande = null;
        foreach ($commandes as $commandeUnit) {
            if ($commandeUnit->getStatus() == "panier" && $commandeUnit->getClient() == $user) {
                $activeCommande = $commandeUnit;
            }
        }

        $reservations = $activeCommande->getReservations();

        $reservation = null;
        foreach ($reservations as $reservationUnit) {
            if ($reservationUnit->getPlat()->getId() == $platId) {
                $reservation = $reservationUnit;
            } 
        }
        
        $reservation->setquantity($reservation->getQuantity() - 1);

        //si la quantité = 0 -> supprimer totalement la réservation
        //sinon persister la réservation avec la nouvelle quantité

        if($reservation->getQuantity() == 0){
            $activeCommande->removeReservation($reservation);
            $entityManager->remove($reservation);
        } else {
            $entityManager->persist($reservation);
        }
        
        $entityManager->persist($activeCommande);

        if ($activeCommande->getReservations()->isEmpty() && $activeCommande->getStatus() == 'panier' && $activeCommande->getClient() == $user) {
            $entityManager->remove($activeCommande);
        }

        $entityManager->flush();

        return $this->redirect($this->generateUrl('restaurant', ['restaurantId' => $restaurantId]));
    }

    /**
     * @Route("/commande/validate/{restaurantId}", name="commande_validate")
     * @Security("is_granted('ROLE_CLIENT')")
     */
    public function validateCommande(Request $request, $restaurantId=false)
    {
        $user = $this->getUser();
        if (!$user || !in_array('ROLE_CLIENT', $user->getRoles(), true)) {
            return $this->redirect($this->generateUrl('restaurant', ['restaurantId' => $restaurantId]));
        }
        
        //validation de la commande du resto' (bouton placé sur la page du resto)
        $entityManager = $this->getDoctrine()->getManager();
        $restaurantRepository = $entityManager->getRepository(Restaurant::class);
        $restaurant = $restaurantRepository->find($restaurantId);

        if(!$restaurant){
            return $this->redirect($this->generateUrl('index'));
        }

        $commandes = $restaurant->getCommandes();
        //Je chercher à récupérer la commande avec le statut 'panier'
        $activeCommande = null;
        foreach ($commandes as $commandeUnit) {
            if ($commandeUnit->getStatus() == "panier" && $commandeUnit->getClient() == $user) {
                $activeCommande = $commandeUnit;
            }
        }

        if(!$activeCommande || $activeCommande->getStatus() != "panier" || $activeCommande->getClient() != $user){
            return $this->redirect($this->generateUrl('restaurant', ['restaurantId' => $restaurantId]));
        }

        $activeCommande->setStatus('validee');
        $entityManager->persist($activeCommande);

        $entityManager->flush();

        //Nous créons un flash bag notifiant la validation de la commande
        $request->getSession()->getFlashBag()->add('message', 'Votre commande a bien été validée');

        return $this->redirect($this->generateUrl('restaurant', ['restaurantId' => $restaurantId]));

    }

}
