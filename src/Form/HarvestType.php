<?php

namespace App\Form;

use App\Entity\Harvest;
use App\Entity\Honey;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HarvestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('weight')
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('honeyKind', EntityType::class, [
                'class' => Honey::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Harvest::class,
        ]);
    }
}
