<?php

namespace App\Form;

use App\Dto\SwarmDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SwarmTransertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $dtos = $options['dtos'];

        $choices =[];
        /**@var SwarmDto $dto */
        foreach($dtos as $dto) {
            $choices[$dto->name] = $dto->id;
        }
        $builder
            ->add('swarmList', ChoiceType::class,[
                'choices'=> $choices,
                'attr' =>[
                    'size' => count($choices) +1
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'dtos'       => [], 
        ]);
    }
}
