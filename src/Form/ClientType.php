<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Users;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomClient', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Nom',
                    'minlength' => '2',
                    'maxlength' => '50',
                ],
                'label' => false,
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50])
                ]
            ])
            ->add('prenomClient', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Prénom',
                    'minlength' => '2',
                    'maxlength' => '50',
                ],
                'label' => false,
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50])
                ]
            ])
            ->add('adresseClient', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Adresse',
                    'minlength' => '2',
                    'maxlength' => '50',
                ],
                'label' => false,
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50])
                ]
            ])
            ->add('numTel', NumberType::class, [
                'attr' => [
                    'class' => 'intl-tel-input',
                    'placeholder' => 'Téléphone',
                    'style' => 'border: none;' 
                ],
                'label' => false,
            ])

            ->add('situation', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-select mt-3',
                    'placeholder' => 'Situation',
                    'minlenght' => '2',
                    'maxlenght' => '50',

                ],
                'choices' => [
                    'Célibataire' => 'Célibataire',
                    'En couple' => 'En couple',
                    'Pacsé(e)' => 'Pacsé',
                    'Marié(e)' => 'Marié',
                    'Veuf / Veuve' => 'Veuf'
                ],
                'label' => false,
            ])
            ->add('parent', EntityType::class, [
                'attr' => [
                    'class' => 'form-select mb-3',
                    'placeholder' => 'Choix du conseiller :',
                ],
                'label' => 'Attribution d\'un Conseiller',
                'class' => Users::class,
                'choice_label' => 'lastname',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.type = :type')
                        ->setParameter('type', 'conseiller');
                },

            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary btn-lg mx-auto d-block mt-4', 
                ],
                'label' => 'Sauvegarder'
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
