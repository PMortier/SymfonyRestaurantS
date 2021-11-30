<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Restaurant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du restaurant',
                'attr' => [
                'class' => 'w3-input w3-border w3-round w3-light-grey w3-margin-bottom w3-margin-top'
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du restaurant',
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey w3-margin-bottom w3-margin-top'
                ],
            ])
            ->add('streetNumber', IntegerType::class, [
                'label' => 'Numéro de rue',
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey w3-margin-bottom w3-margin-top'
                ],
            ])
            ->add('street', TextType::class, [
                'label' => 'Rue',
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey w3-margin-bottom w3-margin-top'
                ],
            ])
            ->add('cp', TextType::class, [
                'label' => 'Code postal',
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey w3-margin-bottom w3-margin-top'
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey w3-margin-bottom w3-margin-top'
                ],
            ])
            ->add('openingDays', ChoiceType::class, [
                'choices' => [
                    ' Lundi ' => 'Lundi',
                    ' Mardi ' => 'Mardi',
                    ' Mercredi ' => 'Mercredi',
                    ' Jeudi ' => 'Jeudi',
                    ' Vendredi ' => 'Vendredi',
                    ' Samedi ' => 'Samedi',
                    ' Dimanche ' => 'Dimanche',
                ],
                'label' => 'Jours d\'ouverture',
                'expanded' => true,
                'multiple' => true,
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey w3-margin-bottom w3-margin-top',
                ],
            ])
            ->add('openingTime', IntegerType::class, [
                'label' => 'Heure d\'ouverture',
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey w3-margin-bottom w3-margin-top'
                ],
            ])
            ->add('closingTime', IntegerType::class, [
                'label' => 'Heure de fermeture',
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey w3-margin-bottom w3-margin-top'
                ],
            ])
            ->add('category', EntityType::class, [
                'label' => 'Choisir une catégorie',
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey w3-margin-bottom w3-margin-top',
                ],
                'class' => Category::class,
                'choice_label' => 'name', //L'attribut de notre Entity que nous voulons utiliser comme label
                'expanded' => false, // expanded = boutons de choix
                'multiple' => false, // choix non multiple car 1 catégorie par produit (OneToMany)
            ])
            ->add('valider', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'w3-button w3-black w3-margin-bottom',
                    'style' => 'margin-top:5px;'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}
