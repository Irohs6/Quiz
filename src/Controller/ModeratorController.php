<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Category;
use App\Repository\QuizRepository;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModeratorController extends AbstractController
{
    //route vers le pannel modérator
    #[Route('/moderator/pannel', name: 'app_moderator_panel')]
    public function panel(): Response
    {
        return $this->render('moderator/panel.html.twig');
    }

    // route vers la liste des quizs ou un modérateur pourravoir le statut d'un quiz, voir le détail d'un quiz, modifier le titre un quiz et ajouter des question a un quiz
    #[Route(path: 'moderator/list/quiz', name: 'app_list_quiz')]
    public function quizList(QuizRepository $quizRepository,CategoryRepository $categoryRepository): Response
    {
        $quizes = $quizRepository->findAll();//récupère toute les données quiz enregistré
        $categories = $categoryRepository->findAll();//récupère toute les données de catégorie enregistré
       
        return $this->render('moderator/list_quiz.html.twig', [
            'quizes' => $quizes,
            'categories' => $categories,
        ]);
    }

    //permet de voir le détail d'un quiz ou un modérateur pourra enlever ou ajouter des questions modifier une question et ses réponses
    #[Route('/moderator/quiz/{id}/show', name: 'show_quiz')]
    public function showQuiz(Quiz $quiz, QuestionRepository $questionRepository): Response
    {
        $questionNotInQuiz = $questionRepository->questionsNotInQuiz($quiz->getId());//pour afficher la liste des question qui ne sont pas dans ce quiz
        return $this->render('moderator/show_quiz.html.twig', [
            'questionNotInQuiz' => $questionNotInQuiz,
            'quiz' => $quiz,
        ]);
    }


    //
    #[Route('/moderator/verified/quiz/{id}', name: 'verified_quiz')]
    public function verifiedQuiz(Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
       $quiz->setIsVerified(true);
       $entityManager->flush();

       return $this->redirectToRoute('show_quiz',['id' => $quiz->getId()]);
       
    }

    


}
