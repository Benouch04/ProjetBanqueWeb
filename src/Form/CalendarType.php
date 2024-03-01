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
            ->add('title', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label' => 'Titre :',
                'label_attr' => ['class' => 'form-label mt-4'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 50])
                ]
            ])
            ->add('start', DateTimeType::class,  [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control datetimepicker'],
                'required' => true,
                'label' => 'Début :',
                'label_attr' => ['class' => 'form-label mt-4']
            ])
            ->add('end', DateTimeType::class,  [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control datetimepicker'],
                'required' => true,
                'label' => 'Fin :',
                'label_attr' => ['class' => 'form-label mt-4']
            ])
            ->add('description', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label' => 'Description :',
                'label_attr' => ['class' => 'form-label mt-4'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 50])
                ]
            ])
            ->add('all_day', CheckboxType::class, [
                'required' => false,
                'label' => 'Toute la journée :',
                'label_attr' => ['class' => 'form-label mt-4'],
                // pas d'attr spécifique nécessaire pour une checkbox simple
            ])
            ->add('background_color', ColorType::class, [
                'attr' => ['class' => 'form-control form-color-picker'],
                'required' => false,
                'label' => 'Couleur de fond :',
                'label_attr' => ['class' => 'form-label mt-4'],
                // Vous pouvez envisager d'ajouter des contraintes pour le format de la couleur si nécessaire
            ])
            ->add('border_color', ColorType::class, [
                'attr' => ['class' => 'form-control form-color-picker'],
                'required' => false,
                'label' => 'Couleur de la bordure :',
                'label_attr' => ['class' => 'form-label mt-4'],
                // Des contraintes similaires pour la couleur pourraient être appliquées ici
            ])
            ->add('text_color', ColorType::class, [
                'attr' => ['class' => 'form-control form-color-picker'],
                'required' => false,
                'label' => 'Couleur du texte :',
                'label_attr' => ['class' => 'form-label mt-4'],
                // Et également ici pour la validation de la couleur du texte
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier',
                'attr' => ['class' => 'btn btn-primary btn-lg mx-auto d-block mt-4'],
            ]);
    
        // Ajoutez vos fichiers JavaScript pour les datepickers avec Webpack Encore si nécessaire
    }
    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }
}
