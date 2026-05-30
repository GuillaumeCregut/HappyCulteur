<?php

namespace App\Form;

use App\Dto\ApiaryDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class HiveTransferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $dtos = $options['dtos'];

        $builder
            ->add('apiaryList', ChoiceType::class,[
                'choices'=> $dtos,
                'choice_label' => fn(ApiaryDto $dto) => $dto->name,
                'choice_value' => fn(?ApiaryDto $dto) => $dto?->id,
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
