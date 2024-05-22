<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModificationSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', null, [
                'widget' => 'single_text'
            ])
            ->add('duree')
            ->add('dateLimiteInscription', null, [
                'widget' => 'single_text'
            ])
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
            ->add('organisateur', EntityType::class, [
                'class' => Participant::class,
'choice_label' => 'id',
            ])
            ->add('participants', EntityType::class, [
                'class' => Participant::class,
'choice_label' => 'id',
'multiple' => true,
            ])
            ->add('siteOrganisateur', EntityType::class, [
                'class' => Campus::class,
'choice_label' => 'id',
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
'choice_label' => 'id',
            ])
            ->add('etat', EntityType::class, [
                'class' => Etat::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
