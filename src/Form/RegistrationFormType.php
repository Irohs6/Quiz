<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                'attr' =>[ 
                    'class' => 'form-control'
                ]
            ])
            ->add('username', TextType::class,[
                'attr' =>[ 
                    'class' => 'form-control'
                ]
            ])
            
            ->add('plainPassword', RepeatedType::class, [
                'attr' =>[ 
                    'class' => 'form-control'
                ],
                'constraints' => [
                new Regex([
                    'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,64}$/',
                    'message' => 'Votre mot de passe doit contenir au minimum 12 caractère dont 1 lettre majuscule, 
                    1 lettre minuscule, 1 chiffre et 1 caractère spécial.'])
                ],
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les password doivent être identique.',
                'options' => [
                    'attr' => [
                        'class' => 'password-field form-control',
                        
                    ]
                ],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password']
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => "J'accepte les terme",
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions.',
                    ]),
                ],
            ])

           
            ->add('Valider', SubmitType::class,[
                'attr' => [
                    'class' => 'btn btn-primary mx-auto d-block', 
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
