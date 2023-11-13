<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('plainPassword', RepeatedType::class, [
            'mapped' => false,
            'attr' =>[ 
                'class' => 'form-control'
            ],
            'constraints' => [
            new Regex([
                'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,64}$/',
                'message' => 'Votre mot de passe doit contenir au minimum 12 caractère dont 1 lettre majuscule, 1 lettre minuscule, 1 chiffre et 1 caractère spécial.'])
            ],
            'mapped' => false,
            'type' => PasswordType::class,
            'invalid_message' => 'Les password doivent être identique.',
            'options' => [
                'attr' => [
                    'class' => 'password-field',
                    'class' => 'form-control'
                ]
            ],
            'required' => true,
            'first_options'  => ['label' => 'Nouveau Password'],
            'second_options' => ['label' => 'Répéter Password']
            
        ])
       
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
