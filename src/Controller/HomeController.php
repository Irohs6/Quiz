<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\QuizRepository;
use App\Repository\LevelRepository;
use App\Repository\ThemeRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home_quiz')]
    public function index(ThemeRepository $themeRepository, CategoryRepository $categoryRepository, LevelRepository $levelRepository, GameRepository $gameRepository,QuizRepository $quizRepository): Response
    {
        $allTheme = $themeRepository->findAll();//recupère toute les donné de la table theme
        $allCategories = $categoryRepository->findAll();//recupère toute les donné de la table category
        $allGames = $gameRepository->findAllBestGame();//recupère toute les donné de la game 
        if ($this->getUser()) {
            $allGamesUser = $gameRepository->findBy(['userId'=>$this->getUser()->getId()],['score' => 'ASC']);//recupère toute les donné de la table game
            
        } else {
           $allGamesUser = null;
        }
        
         // Récupérer tous les quizzes
        $allQuizzes = $quizRepository->findAll();

        $gamesForQuizzes = [];

        // Pour chaque quiz, récupérer les trois meilleurs jeux
        foreach ($allQuizzes as $quiz) {
            $gamesForQuizzes[$quiz->getId()] = $gameRepository->findBestGameByQuiz($quiz);
        }

        if (!$this->getUser()) {
            $gamesPlay = [];
            
            
        }else{
            
            $gamesPlay = $gamesPlay = $gameRepository->findLatestGamesByQuiz($this->getUser()->getId());
           
        }
        return $this->render('home/index.html.twig', [
            'allTheme' => $allTheme,
            'allCategories' => $allCategories,
            'gamesPlay' => $gamesPlay,
            'gamesForQuizzes' => $gamesForQuizzes,
            'allGames' => $allGames,
            'allGamesUser' => $allGamesUser
        ]);
       
    }

}
