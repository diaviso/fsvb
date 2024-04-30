<?php

namespace App\Form;

use App\Entity\Club;
use App\Entity\Joueur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransfertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
        ->add('club', EntityType::class, [
            'class' => Club::class,
            'attr' => [
                'class' => 'bg-success'
            ],
            'required' => true,
            'label' => 'NOUVEAU CLUB '
        ])
        ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $joueur = $event->getData();
            $form = $event->getForm();
            // Stocker le club original dans une option de formulaire
            $form->add('clubOriginal', HiddenType::class, [
                'data' => $joueur->getClub()->getNom(),
                'mapped' => false
            ]);
        })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Joueur::class,
        ]);
    }
}
