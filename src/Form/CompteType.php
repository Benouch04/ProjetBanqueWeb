<?php

namespace App\Form;

use App\Entity\Compte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class CompteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomCompte', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2', // Corrigé ici
                    'maxlength' => '50', // Corrigé ici
                ],
                'required' => false,
                'label' => 'Modifier le nom du contrat',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50])
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Appliquer', // Le texte du bouton est défini ici
                'attr' => [
                    'class' => 'btn btn-primary btn-lg mx-auto d-block mt-4', // Classes pour le style
                ]
            ])

            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Compte::class,
        ]);
    }
}
