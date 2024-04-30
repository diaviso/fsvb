<?php

namespace App\Form;

use App\Entity\Affiliation;
use App\Entity\Document;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'STATUS' => 'STATUS',
                    'RECEPISSE DE DECLARATION' => 'RECEPISSE DE DECLARATION',
                    'PV AG CONSTITUTIVE' => 'PV AG CONSTITUTIVE',
                    'AUTRE' => 'AUTRE',
                ],
                'label' => 'TYPE DU DOCUMENT',
                'required' => true,
            ])
            ->add('brochure', FileType::class, [
                'label' => 'DOCUMENT EN PDF, JPG, OU PNG : ',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '102400k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'application/pdf'
                        ],
                        'mimeTypesMessage' => 'VEUILLEZ CHARGER UN FICHIER PDF VALIDE',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
