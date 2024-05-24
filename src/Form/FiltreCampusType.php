<?php

namespace App\Form;

use App\Entity\FiltreCampus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreCampusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomCampus', TextType::class, [
                'label' => 'Le nom contient :'
            ])
            ->add('searchButton' , SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => ['class' => 'btn btn-sm btn-success']
            ])
            ->add('resetButton' , SubmitType::class, [
                'label' => 'Réinitialiser',
                'attr' => ['class' => 'btn btn-sm btn-secondary']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FiltreCampus::class,
        ]);
    }
}
