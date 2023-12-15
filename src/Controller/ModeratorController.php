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

    #[Route('/moderator/verified/quiz/{id}', name: 'verified_quiz')]
    public function verifiedQuiz(Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
       $quiz->setIsVerified(true);
       $entityManager->flush();

       return $this->redirectToRoute('list_quizzes');
       
    }

    #[Route('/moderator/unVerified/quiz/{id}', name: 'unVerified_quiz')]
    public function unVerifiedQuiz(Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
       $quiz->setIsVerified(false);
       $entityManager->flush();

       return $this->redirectToRoute('list_quizzes');
       
    }

    


}
