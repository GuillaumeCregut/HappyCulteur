<?php

namespace App\Form;

use App\Constant\HiveState;
use App\Entity\Hive;
use App\Entity\Apiary;
use App\Entity\HiveKind;
use App\Entity\HiveRise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class HiveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('frameNumber', ChoiceType::class, [
                'choices' => [
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9,
                    '10' => 10,
                    '11' => 11,
                    '12' => 12
                ],
            ])
            ->add('riseNumber', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9,
                ],
            ])
            ->add('identification')
            ->add('state', EnumType::class, [
                'class' => HiveState::class,
                'choice_label' => fn(HiveState $hs) => $hs->translate(),
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('observation')
            ->add('kind', EntityType::class, [
                'class' => HiveKind::class,
                'choice_label' => 'name',
            ])
            ->add('rise', EntityType::class, [
                'class' => HiveRise::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Hive::class,
        ]);
    }
}
