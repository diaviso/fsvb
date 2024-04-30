<?php

namespace App\Form;

use App\Entity\Affiliation;
use App\Entity\Club;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffiliationType extends AbstractType
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
            //->add('saison')
            ->add('siegeSocialDuClub', TextType::class, [
                'label' => 'Siège social du club'
            ])
            ->add('adresseDuClub')
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone'
            ])
            ->add('fax')
            ->add('couleurs', TextType::class, [
                'label' => 'Couleurs du club'
            ])
            ->add('mailOfficiel', TextType::class, [
                'label' => 'Adresse mail officiel du Club (obligatoire)',
                'required' => true,
            ])
            ->add('terrains')
            ->add('prefecture')
            ->add('dateDeDeclation', DateType::class, ['widget' => 'single_text','label'=>'Date de déclaration à la prefecture',])
            ->add('president', TextType::class, [
                'label' => 'Président'
            ])
            ->add('premiervicePresident', TextType::class, [
                'label' => 'Premier Vice Président'
            ])
            ->add('deuxiemeVidePresident', TextType::class, [
                'label' => 'Deuxième Vice Président'
            ])
            ->add('secretaireGeneral', TextType::class, [
                'label' => 'Secrétaire Général'
            ])
            ->add('TresorierGeneral', TextType::class, [
                'label' => 'Trésorier Général'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Affiliation::class,
            'clubs' => array(),
        ]);
    }
}
