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
    public function create(Request $request, Category $category): Response
    {
        $quiz = new Quiz();
        $form = $this->createForm(TestQuizType::class, $quiz);
        $question = new Question();
        $formNewQuestion = $this->createForm(TestQuestionType::class, $question);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Vous pouvez enregistrer le quiz en base de données ici
            }
        }

        return $this->render('test/create.html.twig', [
            'form' => $form->createView(),
            'formNewQuestion' => $formNewQuestion->createView(),
            'quizId' =>$quiz->getId(),
            'questionId' => $question->getId()
        ]);
    }

   
    public function createAnswer(Request $request): Response
    {
        $question = new Question();
        $formNewQuestion = $this->createForm(TestQuestionType::class, $question);

        if ($request->isMethod('POST')) {
            $formNewQuestion->handleRequest($request);

            if ($formNewQuestion->isSubmitted() && $formNewQuestion->isValid()) {
                // Vous pouvez enregistrer le quiz en base de données ici
            }
        }

        return $this->render('test/question_template.html.twig', [
            'formNewQuestion' => $formNewQuestion->createView(),
        ]);
    }
}

