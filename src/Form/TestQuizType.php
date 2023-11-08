<?php

namespace App\Form;

use App\Entity\Quiz;
use App\Entity\Level;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TestQuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                'label' => 'Titre du Quiz',
                'attr' =>[ 
                    'class' => 'form-control'
                ]
            ])
         
            ->add('level', EntityType::class,[
                'class' => Level::class,
                'choice_label' => 'label',
                'multiple' => false,
                'expanded' => true, 
                
            ])
            ->add('questions', CollectionType::class,[
                'entry_type' => QuestionType::class,
                'prototype' => true,
                //autoriser l'ajout de nouveau élément qui seront persiter grace au cascade persit sur l'élément Question
                //ca va va activer un data prototype qui sera un attribut html qu'on pourra manipuler en js
                'allow_add' => true, //autorise l'ajout 
                'allow_delete' => true, //autorise la suppression
                'by_reference' => false,// il est obligatoire car Quiz n'a pas de setQuestion mais c'est Question qui contient setQuiz
                //Question est propriétaire de la relations. Pour éviter un mapping => false on est obligé de rajouter un by_reference => false
                'label' => 'Questions',
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
