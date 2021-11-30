<?php

namespace App\Form;

use App\Entity\Plat;
use App\Entity\Restaurant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PlatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du plat',
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey w3-margin-bottom w3-margin-top'
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du plat',
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey w3-margin-bottom w3-margin-top'
                ],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey w3-margin-bottom w3-margin-top'
                ],
            ])
            ->add('photo', TextType::class, [
                'label' => 'Photo',
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey w3-margin-bottom w3-margin-top'
                ],
            ])
            // Si on passe obligatoirement par l'id du restaurant on peut forcer ce champ
            /* ->add('restaurant', EntityType::class, [
                'label' => 'Choisir un restaurant',
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey w3-margin-bottom w3-margin-top',
                ],
                'class' => Restaurant::class,
                'choice_label' => 'name',
                'expanded' => false,
                'multiple' => false,
            ]) */
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
            'data_class' => Plat::class,
        ]);
    }
}
