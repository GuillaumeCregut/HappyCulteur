<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;

class ApiaryPictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('picture',FileType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' =>  [
                    new Assert\File(
                        maxSize: '1024k',
                        extensions: ['png', 'jpg'],
                        extensionsMessage: 'Veuillez télécharger un png valide',
                        maxSizeMessage: 'Le fichier est trop volumineux ({{ size }} ko). Maximum autorisé : {{ limit }} ko'
                    )
                ],
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
