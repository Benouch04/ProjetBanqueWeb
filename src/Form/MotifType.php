<?php

namespace App\Form;

use App\Entity\Motif;
use App\Entity\PieceJustif;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MotifType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('motifPj', EntityType::class, [
            'class' => PieceJustif::class,
            'choice_label' => 'nomPieceJustif', // Utilisez le champ réel de l'entité PieceJustif qui doit être affiché
            'multiple' => true,
            'expanded' => true, // Mettez à true pour utiliser des checkboxes
        ])
        // Ajoutez d'autres champs comme nécessaire...
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Motif::class,
        ]);
    }
}
