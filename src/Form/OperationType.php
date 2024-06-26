<?php

namespace App\Form;

use App\Entity\CompteClient;
use App\Entity\Operation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
class OperationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeOperation', ChoiceType::class, [
                'attr' => ['class' => 'form-select mb-3'],
                'choices' => [
                    'Dépôt' => 'dépôt',
                    'Retrait' => 'retrait',
                ],
                'label' => 'Type d\'opération',
                'placeholder' => 'Choisir une opération',
                'required' => true,
            ])
        
            ->add('montant', NumberType::class, [
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Montant',
                    'minlength' => '2',
                    'maxlength' => '50',
                ],
                'label' => 'Montant',
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Appliquer', 
                'attr' => [
                    'class' => 'btn btn-primary btn-lg mx-auto d-block mt-4', 
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Operation::class,
        ]);
    }
}
