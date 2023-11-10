<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Answer;
use App\Entity\Category;
use App\Entity\Question;
use App\Form\TestQuizType;
use App\Form\TestQuestionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/test/{id}', name: 'app_test')]
    public function createAnswer(Request $request, Quiz $quiz): Response
    {
        $question = new Question;
        
        $form = $this->createForm(TestQuestionType::class, $question);
        $formQuiz = $this->createForm(TestQuizType::class, $quiz);
        // $formQuiz = $this->createForm(TestQuizType::class, $question);
        $category = $quiz->getCategory();
        $question = new Question();
        $answer = new Answer;
        $question->setCategory($category);
        $answer->setQuestion($question);
        
       
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Vous pouvez enregistrer le quiz en base de données ici
            }
        

        return $this->render('test/create.html.twig', [
            'form' => $form->createView(),
            'quizId' =>$quiz->getId(),
            'formQuiz' => $formQuiz->createView(),
        ]);
    }

    #[Route('/test/quiz/{id}', name: 'app_question')]
    public function createQuiz(Request $request, Quiz $quiz): Response
    {
        $formQuiz = $this->createForm(TestQuizType::class, $quiz);

        
        $category = $quiz->getCategory();
        $question = new Question();
        $question->setCategory($category);
        
       
            $formQuiz->handleRequest($request);
        // dd($formQuiz->get('questions'));
            if ($formQuiz->isSubmitted() && $formQuiz->isValid()) {
                // Vous pouvez enregistrer le quiz en base de données ici
            }
        
        return $this->render('test/create.html.twig', [
            'quiz' => $quiz,
            'formQuiz' => $formQuiz->createView(),
            'quizId' =>$quiz->getId(), 
        ]);
    }
}

