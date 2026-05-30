<?php

namespace App\Form;

use App\Entity\Apiculteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ApiaryUsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $users = $options['users'];

        $builder
            ->add('userList', ChoiceType::class, [
                'choices' => $users,
                'choice_label' => fn(Apiculteur $user) => "{$user->getFirstname()} {$user->getName()}",
                'choice_value' => fn(?Apiculteur $user) => $user?->getId(),
                'placeholder' => '-- Choisir un utilisateur --',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'users'       => [],
        ]);
    }
}
