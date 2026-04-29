<?php

namespace App\Form;

use App\Entity\Swarm;
use App\Constant\SwarmOrigin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class SwarmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('origin', EnumType::class, [
                'class' => SwarmOrigin::class,
                'choice_label' => fn(SwarmOrigin $so) => $so->label(),
                ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'required'=>false,
            ])
            ->add('specy')
            ->add('capturePlace')
            ->add('queenAge', DateType::class, [
                'widget' => 'single_text',
                'required'=>false,
                ])
            ->add('queenOrigin')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Swarm::class,
        ]);
    }
}
