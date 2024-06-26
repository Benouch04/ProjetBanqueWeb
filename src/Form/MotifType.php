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
            'choice_label' => 'nomPieceJustif', 
            'multiple' => true,
            'expanded' => true, 
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Motif::class,
        ]);
    }
}
