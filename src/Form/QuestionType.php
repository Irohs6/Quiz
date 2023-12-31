<?php

namespace App\Form;

use App\Entity\Link;
use App\Entity\Question;
use App\Form\AnswerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sentence', TextType::class,[
                'label' => 'Question',
                'attr' =>[ 
                    'class' => 'form-control',
                ]
            ])
            ->add('link', UrlType::class,[
                'label' => 'Lien ver la documentation officielle',
                'required' => false,
                'attr' =>[ 
                    'class' => 'form-control',
                ]
                ])
            ->add('answers', CollectionType::class,[
                'entry_type' => AnswerType::class,
                'prototype' => 'answers',
                'label' => false,
                //autoriser l'ajout de nouveau élément qui seront persiter grace au cascade persit sur l'élément Question
                //ca va va activer un data prototype qui sera un attribut html qu'on pourra manipuler en js
                'allow_add' => true, //autorise l'ajout 
                'allow_delete' => true, //autorise la suppression
                'by_reference' => false,// il est obligatoire car Quiz n'a pas de setQuestion mais c'est Question qui contient setQuiz
                //Question est propriétaire de la relations. Pour éviter un mapping => false on est obligé de rajouter un by_reference => false
                
            ])
        
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
