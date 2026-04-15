<?php

namespace App\Form;

use App\Entity\Apiculteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login')
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(
                        message: 'You should agree to our terms.',
                    ),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(
                        message: 'Entrer un mot de passe',
                    ),
                    new Length(
                        min: 6,
                        minMessage: 'Votre mot de passe doit faire {{ limit }} charactères',
                        // max length allowed by Symfony for security reasons
                        max: 4096,
                    ),
                ],
            ])
            ->add('codeAPE')
            ->add('codeAPI')
            ->add('siret')
            ->add('numagri')
            ->add('name')
            ->add('firstname')
            ->add('email', EmailType::class)
            ->add('city')
            ->add('street')
            ->add('streetNumber', TextType::class, [
                'attr' => [
                    'maxlength' => 10,
                    'style' => 'width: 12ch;'
                ]
            ])
            ->add('zipCode', TextType::class, [
                'attr' => [
                    'maxlength' => 10,
                    'style' => 'width: 7ch;',
                    
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Apiculteur::class,
        ]);
    }
}
