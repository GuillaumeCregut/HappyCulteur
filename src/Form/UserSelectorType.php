<?php

namespace App\Form;

use App\Entity\Apiculteur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSelectorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => Apiculteur::class,
                'choice_label' => function (Apiculteur $user): string {
                    return $user->getFirstname() . ' ' . $user->getName();
                },
                'placeholder' => '-- Choisir un utilisateur --', 
                'required' => true

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
