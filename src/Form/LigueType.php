<?php

namespace App\Form;

use App\Entity\Ligue;
use App\Entity\Region;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class LigueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom')
        ->add('region')
        ->add('president', EntityType::class,[
            'class' => User::class,
            // uses the User.username property as the visible option string
            //'choice_label' => 'ligue',
        ])
        ->add('brochure', FileType::class, [
            'label' => 'Image Logo (jpeg ou png) : ',
            // unmapped means that this field is not associated to any entity property
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize' => '10240k',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'VEUILLEZ CHARGER UNE IMAGE VALIDE',
                ])
            ],
        ])
    ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ligue::class,
        ]);
    }
}
