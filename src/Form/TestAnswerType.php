<?php

namespace App\Form;

use App\Entity\Answer;
use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TestAnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder

        // ->add('question', EntityType::class,[
        //     'class' =>  Question::class,
        //     'label' => 'Question'
        // ])

        ->add('sentence', TextType::class,[
            'label' => 'Réponse',
        ])

        ->add('isRight',ChoiceType::class,[
            'choices' => [
                'Bonne réponse' => '1',
                'Mauvaise réponse' => '0'
            ],
            'expanded' => true, // Affiche les genres comme des checkboxes
            'multiple' => false, // Permet de n'en sélectionner qu'un seul
        ]) 
        
        
        ;    
          
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
        ]);
    }
}
