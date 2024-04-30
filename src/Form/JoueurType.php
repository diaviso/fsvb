<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Club;
use App\Entity\Joueur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JoueurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
            ->add('club', ChoiceType::class, [
                'choices'  => $options['clubs'],
                'choice_label' => function (?Club $cl) {
                    return $cl ? strtoupper($cl) : '';
                },
                'attr' => [
                    'class' => 'bg-danger'
                ],
                'required' => true,
            
                'label' => 'CLUB '
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'attr' => [
                    'class' => 'form-control select',
                    'data-live-search'=> 'true',
                ],
                'label'=> 'Type de licence'
            ])
            ->add('brochure', FileType::class, [
                'label' => 'Photo du licencié (jpeg ou png) : ',
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
            ->add('prenom')
            ->add('nom')
            ->add('dateDeNaissance', DateType::class, ['widget' => 'single_text','label'=>'Date de Naissance',])
            ->add('lieuDenaissance', TypeTextType::class,[
                'label' => 'Lieu de Naissance'
            ])
            ->add('sexe', ChoiceType::class, [
                'choices'  => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                ],
                'label' => 'Sexe'
            ])
            ->add('nationalite')
            ->add('email')
            ->add('adresse')
            ->add('telephone')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Joueur::class,
            'clubs' => array(),
        ]);
    }
}
