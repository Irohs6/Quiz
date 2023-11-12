<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\QuizRepository;
use App\Repository\UserRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModeratorController extends AbstractController
{
    #[Route('/moderator/pannel', name: 'app_moderator_panel')]
    public function index(): Response
    {
        return $this->render('moderator/index.html.twig');
    }

    #[Route(path: 'moderator/list/quiz', name: 'app_list_quiz')]
    public function userList(QuizRepository $quizRepository,CategoryRepository $categoryRepository): Response
    {
        $quizes = $quizRepository->findAll();
        $categories = $categoryRepository->findAll();
       
        return $this->render('moderator/list_quiz.html.twig', [
            'quizes' => $quizes,
            'categories' => $categories,
        ]);
    }


    #[Route('/moderator/quiz/{id}/show', name: 'show_quiz')]
    public function showQuiz(Quiz $quiz, QuestionRepository $questionRepository): Response
    {
        $questionNotInQuiz = $questionRepository->questionsNotInQuiz($quiz->getId());//pour afficher la liste des question qui ne sont pas dans ce quiz
        return $this->render('moderator/show_quiz.html.twig', [
            'questionNotInQuiz' => $questionNotInQuiz,
            'quiz' => $quiz,
        ]);
    }

    #[Route('/moderator/verified/quiz/{id}', name: 'verified_quiz')]
    public function verifiedQuiz(Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
       $quiz->setIsVerified(true);
       $entityManager->flush();

       return $this->redirectToRoute('show_quiz',['id' => $quiz->getId()]);
       
    }
}
