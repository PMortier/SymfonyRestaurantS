<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Plat;
use App\Entity\Category;
use App\Entity\Restaurant;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //Création des catégories
        $categoryNames = ['Français', 'Italien', 'Indien', 'Japonais', 'Africain'];
        $categoryDescription = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        
        //Création des restaurants
        $restaurantPrefix = ['Chez', 'Oh!', 'Super', 'Délices de', 'Les gourmandises de', 'Les spécialités de', 'Au gourmet ', 'Resto de', 'Renaud &'];
        $restaurantNames = ['Jules', 'Cindy', 'Michel', 'Nathan', 'Pierre', 'Michou', 'Sarah', 'Paul', 'Richard', 'Julie','Emile', 'Emilie'];
        $restaurantDescription = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        $restaurantCity = 'Paris';
        $restaurantStreet = ["rue de l'église", 'rue nationale', 'rue Victor Hugo', 'rue des marthyrs', 'rue du goût', 'avenue du Siècle'];
        $restaurantOpeningDays = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'];
        $restaurantOpeningTime = 9;
        $restaurantClosingTime = 22;

        //Création des plats
        $platNames = ['Delice', 'Gourmand', 'Gourmet', 'Voyage', 'Tradition', 'Original', 'Sensation', 'Finesse', 'Tonic', 'Saveur', 'Surprise', 'Rafiné', 'Spécialité', 'Douceur', 'Simplicité'];
        $platDescription = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $nbCategory = count($categoryNames); // ici 5
        $nbRestaurantsParCategory = 4;
        $nbPlatsParRestaurant = 8;

        for ($i = 0; $i < $nbCategory; ++$i) 
        {
            $category = new Category;
            
            $category->setName($categoryNames[$i]);
            $category->setDescription($categoryDescription);

            $manager->persist($category);

            for ($j = 0; $j < $nbRestaurantsParCategory; ++$j) 
            {
                $restaurant = new Restaurant();
                
                $restaurant->setName($restaurantPrefix[rand(0, (count($restaurantPrefix) - 1))] . ' ' . $restaurantNames[rand(0, (count($restaurantNames) - 1))]);
                $restaurant->setDescription($restaurantDescription);
                $restaurant->setStreetNumber(rand(1, 99));
                $restaurant->setStreet($restaurantStreet[rand(0, (count($restaurantStreet) - 1))]);
                $restaurant->setCp('750' . rand(1, 9));
                $restaurant->setCity($restaurantCity);
                $restaurant->setOpeningDays($restaurantOpeningDays);
                $restaurant->setOpeningTime($restaurantOpeningTime);
                $restaurant->setClosingTime($restaurantClosingTime);

                $manager->persist($restaurant);
                $category->addRestaurant($restaurant);

                for ($k = 0; $k < $nbPlatsParRestaurant; ++$k) 
                {
                    $plat = new Plat();
                    
                    $plat->setName($platNames[rand(0, (count($platNames) - 1))] . ' #' . rand(1, 99));
                    $plat->setDescription($platDescription);
                    $plat->setPrice(rand(9, 24) + 0.50);
                    $plat->setPhoto('plat_' . rand(1, 5) . '.jpg');
                    
                    $manager->persist($plat);
                    $restaurant->addPlat($plat);
                } 
            }
        }
        $manager->flush();
    }
}
