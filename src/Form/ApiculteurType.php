<?php

namespace App\Form;

use App\Entity\Apiculteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApiculteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login')
            ->add('roles')
            ->add('password')
            ->add('codeAPE')
            ->add('codeAPI')
            ->add('siret')
            ->add('numagri')
            ->add('name')
            ->add('firstname')
            ->add('email')
            ->add('city')
            ->add('street')
            ->add('streetNumber')
            ->add('zipCode')
            ->add('lane')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Apiculteur::class,
        ]);
    }
}
