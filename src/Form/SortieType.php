<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie :',
                'required' => true
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie :',
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('duree',IntegerType::class, [
                'label' => 'Durée (minutes)',
                'attr' => [
                    'value' => 90,
                    'min' => 0
                ]
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'label' => 'Date limite d\'inscription :',
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'attr' => [
                    'min' => 0
                ]
            ])
            ->add('infosSortie', TextareaType::class)
            ->add('siteOrganisateur', EntityType::class, [
                'class' => Campus::class,
                'label' => 'Campus :',
                'choice_label' => 'nom',
                'disabled' => true
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'mapped' => false,
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez une ville',
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez un lieu',
                'choices' => []
            ])
//            ->add('btnEnregistrer', SubmitType::class, [
//                'label' => 'Enregistrer',
//                'attr' => ['class' => 'btn btn-primary']
//            ])
//            ->add('btnPublier', SubmitType::class, [
//                'label' => 'Publier la sortie',
//                'attr' => ['class' => 'btn btn-secondary']
//            ])
        ;

            $formModifier = function(FormInterface $form, ?Ville $ville = null):void
            {
                $lieux = null === $ville ? [] : $ville->getLieux();
                $form->add('lieu', EntityType::class, [
                    'class' => Lieu::class,
                    'placeholder' => '',
                    'choices' => $lieux
                ]);
            };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($formModifier): void {
            $data = $event->getData();
            $lieux = $data->getLieu();
            $form = $event->getForm();

            if ($data instanceof Sortie && $lieux) {
                $ville = $lieux->getVille();
                $formModifier($event->getForm(), $ville);
                $form->add('ville', EntityType::class, [
                    'class' => Ville::class,
                    'mapped' => false,
                    'choice_label' => 'nom',
                    'data' => $ville
                    ]);
            }
        });

        $builder->get('ville')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($formModifier): void {
            $ville = $event->getForm()->getData();

            $formModifier($event->getForm()->getParent(), $ville);
        });

        $builder->setAction($options['action']);

//            ->add('lieuType', LieuType::class, [
//                'mapped' => false,
//                'required' = false,
//                'attr' => [
//                    'style' => 'display: none'
//                ],
//
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
