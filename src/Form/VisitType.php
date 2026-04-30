<?php

namespace App\Form;

use App\Entity\Visit;
use App\Entity\Disease;
use App\Constant\Weather;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class VisitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('weather', EnumType::class, [
                'class' => Weather::class,
                'choice_label' => fn(Weather $hs) => $hs->label(),
                'placeholder' => 'Choisir',
            ])
            ->add('isDisease')
            ->add('temperature')
            ->add('hygrometry')
            ->add('isWorksToDo')
            ->add('isFeeded')
            ->add('feeding')
            ->add('weight')
            ->add('isQueenVisible')
            ->add('population')
            ->add('behaviour')
            ->add('notes')
            ->add('disease', EntityType::class, [
                'class' => Disease::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Visit::class,
        ]);
    }
}
