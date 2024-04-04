<?php

namespace App\Form;

use App\Entity\CompteClient;
use App\Entity\Compte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
class CompteClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*->add('dateOuverture')
            ->add('solde')
            ->add('montantDecouvert')
            ->add('client')
            ->add('compte')*/

            // Assurez-vous que le champ 'compte' est nécessaire ou ajustez en conséquence
            ->add('compte', EntityType::class, [
                'class' => Compte::class,
                'choice_label' => 'nomCompte',
                'placeholder' => 'Choisissez un compte',
                'attr' => ['class' => 'form-select mb-3'],
            ])
            ->add('typeOp', ChoiceType::class, [
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
                'label' => 'Appliquer', // Le texte du bouton est défini ici
                'attr' => [
                    'class' => 'btn btn-primary btn-lg mx-auto d-block mt-4', // Classes pour le style
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CompteClient::class,
        ]);
    }
}
