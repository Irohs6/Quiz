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
    public function create(Request $request, Quiz $quiz): Response
    {
        
        $form = $this->createForm(TestQuizType::class, $quiz);
        $category = $quiz->getCategory();
        $question = new Question();
        $question->setCategory($category);
        // $formNewQuestion = $this->createForm(TestQuestionType::class, $question);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Vous pouvez enregistrer le quiz en base de données ici
            }
        }

        return $this->render('test/create.html.twig', [
            'form' => $form->createView(),
            // 'formNewQuestion' => $formNewQuestion->createView(),
            'quizId' =>$quiz->getId(),
            
        ]);
    }
}

