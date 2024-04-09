<?php

namespace App\Form;

use App\Entity\Calendar;
use App\Entity\Users;
use App\Entity\Client;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $nomContrats = $options['nomContrat'];
        $nomComptes = $options['nomCompte'];

        $builder
            /*->add('clients', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function ($client) {
                    return $client->getNomClient(); // Assurez-vous que cette méthode renvoie la représentation en chaîne du client
                },
                'label' => 'Client :',
            ])
            ->add('users', EntityType::class, [
                'class' => Users::class, // Ou Conseiller::class
                'choice_label' => function ($user) {
                    return $user->getLastname(); // Assurez-vous que cette méthode renvoie la représentation en chaîne du conseiller
                },
                'label' => 'Conseiller :',
            ])*/
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
                // Supposons que nous utilisons un attr pour ajouter une classe personnalisée
                'attr' => ['class' => 'form-control datetimepicker-hour-only'],
                'required' => true,
                'label' => 'Début :',
                'label_attr' => ['class' => 'form-label mt-2'],
                // Personnalisation côté client pour ajuster le comportement du sélecteur
            ])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control datetimepicker-hour-only'],
                'required' => true,
                'label' => 'Fin :',
                'label_attr' => ['class' => 'form-label mt-2'],
                // Identique au champ 'start'
            ])

            ->add('description', ChoiceType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'choices' => array_combine(array_merge($nomContrats, $nomComptes), array_merge($nomContrats, $nomComptes)),
                'label' => 'Motif :',
                'placeholder' => 'Choix du motif',
                'label_attr' => ['class' => 'form-label mt-2'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 50])
                ]
            ])
            ->add('all_day', CheckboxType::class, [
                'required' => false,
                'label' => 'Toute la journée :',
                'label_attr' => ['class' => 'form-label mt-2'],
                // pas d'attr spécifique nécessaire pour une checkbox simple
            ])
            ->add('background_color', ColorType::class, [
                'required' => false,
                'label' => 'Couleur de fond :',
                'label_attr' => ['class' => 'form-label mt-2 mb-3'],
                // Vous pouvez envisager d'ajouter des contraintes pour le format de la couleur si nécessaire
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter',
                'attr' => ['class' => 'btn btn-primary btn-lg mx-auto d-block mt-2'],
            ]);

        // Ajoutez vos fichiers JavaScript pour les datepickers avec Webpack Encore si nécessaire
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
            'client_name' => '', // Ajoutez une valeur par défaut pour éviter les erreurs
            'conseiller_name' => '',
            'nomContrat' => null, // assurez-vous que les noms correspondent exactement
            'nomCompte' => null, // assurez-vous que les noms correspondent exactement
        ]);
    }
}
