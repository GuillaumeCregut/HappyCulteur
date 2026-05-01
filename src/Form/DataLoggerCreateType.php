<?php

namespace App\Form;

use App\Dto\DataloggerConfigDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class DataLoggerCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('extTemp', CheckboxType::class,[
                'required' => false
            ])
            ->add('intTemp', CheckboxType::class,[
                'required' => false
            ])
            ->add('intHygro', CheckboxType::class,[
                'required' => false
            ])
            ->add('extHygro', CheckboxType::class,[
                'required' => false
            ])
            ->add('weight', CheckboxType::class,[
                'required' => false
            ])
            ->add('frequency', IntegerType::class, [
                'attr' =>[
                    'min' => 1,
                    'max' => 24
                ],
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DataloggerConfigDto::class
        ]);
    }
}
