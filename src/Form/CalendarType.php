<?php

namespace App\Form;

use App\Entity\Calendar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('clientName', TextType::class, [
                'mapped' => false,
                'data' => $options['client_name'],
                'disabled' => true,
                'label' => 'Nom du Client :',
                'label_attr' => ['class' => 'form-label mt-2'],
                'attr' => ['class' => 'form-control', 'readonly' => true],
            ])
            ->add('conseillerName', TextType::class, [
                'mapped' => false,
                'data' => $options['conseiller_name'],
                'disabled' => true,
                'label' => 'Nom du Conseiller :',
                'label_attr' => ['class' => 'form-label mt-2'],
                'attr' => ['class' => 'form-control', 'readonly' => true],
            ])
            ->add('title', TextType::class, [
                'attr' => ['class' => 'form-control '],
                'required' => true,
                'label' => 'Titre :',
                'label_attr' => ['class' => 'form-label mt-2'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 50])
                ]
            ])
            ->add('start', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control datetimepicker-hour-only'],
                'required' => true,
                'label' => 'Début :',
                'label_attr' => ['class' => 'form-label mt-2'],
            ])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control datetimepicker-hour-only'],
                'required' => true,
                'label' => 'Fin :',
                'label_attr' => ['class' => 'form-label mt-2'],
            ])
            ->add('all_day', CheckboxType::class, [
                'required' => false,
                'label' => 'Toute la journée :',
                'label_attr' => ['class' => 'form-label mt-2'],
            ])
            ->add('background_color', ColorType::class, [
                'required' => false,
                'label' => 'Couleur de fond :',
                'label_attr' => ['class' => 'form-label mt-2 mb-3'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter',
                'attr' => ['class' => 'btn btn-primary btn-lg mx-auto d-block mt-2'],
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
            'client_name' => '', 
            'conseiller_name' => '',
            'nomContrat' => null, 
            'nomCompte' => null, 
        ]);
    }
}
