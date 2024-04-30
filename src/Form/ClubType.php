<?php

namespace App\Form;

use App\Entity\Club;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ClubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username',TextType::class, [
                'label' => 'Nom de la personne responsable',
                'mapped' => false,
                'required' => true,
                'data' => $options['username'],
            ])
            ->add('nom', TextType::class,[
                'label' => 'Nom du club',
                'required' => true,
            ])
            ->add('abreviation',TextType::class,[
                'label' => 'Nom court',
                'required' => true
            ])
            ->add('telephone')
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Club::class,
            'username' => array(),
        ]);
    }
}
