<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Game;
use App\Entity\Quiz;
use App\Form\QuizType;
use App\Form\PlayQuizzType;
use App\Repository\ThemeRepository;
use App\Repository\CategoryRepository;
use App\Repository\GameRepository;
use App\Repository\LevelRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;

class QuizController extends AbstractController
{
    //pour afficher les theme les catégorie et leurs quiz
    #[Route('/quiz', name: 'app_quiz')]
    public function home(ThemeRepository $themeRepository, CategoryRepository $categoryRepository, LevelRepository $levelRepository,GameRepository $gameRepository): Response
    {
        $allTheme = $themeRepository->findAll();//recupère toute les donné de la table theme
        $allCategories = $categoryRepository->findAll();//recupère toute les donné de la table category
        $allLevel = $levelRepository->findAll();//recupère toute les donné de la table level
        if (!$this->getUser()) {
            $gamesPlay = "";
        }else{
            $gamesPlay = $gameRepository->findBy(['userId'=>$this->getUser()->getId()]);
        }
        return $this->render('quiz/home.html.twig', [
            'allTheme' => $allTheme,
            'allCategories' => $allCategories,
            'allLevel'=>$allLevel,
            'games' => $gamesPlay

        ]);
    }

    //futur home de quiz en cour de création
    #[Route('home/quiz', name: 'app_home_quiz')]
    public function home_quiz(ThemeRepository $themeRepository, CategoryRepository $categoryRepository, LevelRepository $levelRepository, GameRepository $gameRepository): Response
    {
        $allTheme = $themeRepository->findAll();//recupère toute les donné de la table theme
        $allCategories = $categoryRepository->findAll();//recupère toute les donné de la table category
        $allLevel = $levelRepository->findAll();//recupère toute les donné de la table level
        if ($this->getUser()->getId()) {
            $gamePlay = $gameRepository->findOneBy(['userId'=>$this->getUser()->getId()]);
        }else{
            $gamePlay = "";
        }
        return $this->render('quiz/home_quiz.html.twig', [
            'allTheme' => $allTheme,
            'allCategories' => $allCategories,
            'allLevel'=> $allLevel,
            'game' => $gamePlay
        ]);
    }


    //pages pour jouer un quiz
    #[Route('/quiz/play/{id}', name: 'app_play')]
    public function playQuiz(Quiz $quiz, Request $request, EntityManagerInterface $entityManager,GameRepository $gameRepository): Response
    {
        $quizData = [
            'titre' => $quiz->getTitle(),//ajoute le titre du quiz
            'questions' => [], // Initialise le tableau des questions 
        ];
        
        foreach ($quiz->getQuestions() as $question) {
            $questionData = [
                'id' => $question->getId(),//ajoute l'id de la question
                'question' => $question->getSentence(),//ajoute l'intitulé de la question
                'reponses' => [], // Initialise le tableau des réponses 
            ];
        
            foreach ($question->getAnswers() as $reponse) {
                // Ajoutez les données de chaque réponse dans le tableau des réponses
                $questionData['reponses'][] = [
                    'id' => $reponse->getId(),//ajoute l'id de la réponse
                    'intitulle' => $reponse->getSentence(),//ajoute l'intitulé de la réponse
                    'isRihgt' => $reponse->isIsRight(),//ajoute le bolean isRight pour savoir si la réponse est juste au mauvaise
                ];
            }
        
            // Ajoute les données de la question et de ses réponses au tableau des questions
            $quizData['questions'][] = $questionData;
        }
        
        $quizJson = json_encode($quizData); //transforme le tableau en Json ((JavaScript Object Notation))
        $category = $quiz->getCategory(); // on récupère la catégorie pour en récupérer l'image
        $game = new Game; // nouvelle instance de Game
        
        $formQuiz = $this->createForm(PlayQuizzType::class, $question, ['attr' => ['class' => 'formQuiz']]); //creer le formulaire
        
        $formQuiz->handleRequest($request);
        $level = $quiz->getLevel(); // on récupère le niveaux de difficulté 
        $scoreCoeff = $level->getScoreCoef(); //on récupère le coefficient

        $gamePlay = $gameRepository->findOneBy(['userId'=>$this->getUser()->getId(), 'quiz' => $quiz->getId()]);
        if ($gamePlay) {
             $date = $gamePlay->getDateGame();
             $dateModify = date_modify($date ,"+7 day");
        }
       
        $now = new DateTime();

       
        if ($quiz->isIsVerified()|| $this->isGranted('ROLE_MODERATOR')) {
            if (!$gamePlay || $now == $dateModify || $this->isGranted('ROLE_MODERATOR')){
            
                //si le formQuizulaire est remplie et valide
                if ($formQuiz->isSubmitted() && $formQuiz->isValid()) {
                    $quiz->addGame($game);// ajout du quiz dans Game
                    $user = $this->getUser(); // on récupère l'user en session
                    $game->setUserId($user); // on rajoute l'user en session a la Game

                    $recapData = $request->request->get('recapData');//récupère le tableau de récapitulatif du quiz en json
                    
                    $recapDataArray = json_decode($recapData, true);// convertit en une structure de données PHP dans notre cas un tableau associatif
                
                foreach ($recapDataArray as $data) {
                    if (isset($data['score'])) {
                        $score = $data['score'];
                        $scoreSum = $score * $scoreCoeff / 10;
                    } else {
                        foreach ($data as $response) {
                            $answer = $entityManager->getRepository(Answer::class)->findOneBy(['id' => $response['answerId']]);//récupère la question grace a son id contenu dans le tableau
                            $game->addAnswer($answer);//ajoute les question a la game
                        }
                    }
                }
                $now = new DateTime();
                $game->setDateGame($now);
                $game->setScore($scoreSum);//ajoute le score a la game
                // dd($game);
                    // prepare PDO(prepare la requete Insert ou Update)
                    $entityManager->persist($game);
                    // execute PDO(la requete Insert ou Update)
                    $entityManager->flush();
                    //redirige ver le home qui est la liste des formation
                
                    return $this->redirectToRoute('app_quiz');
                
                }
            }else{
            
                $nbJour = $dateModify->diff($now)->format("%d");
                $this->addFlash('error', 'Vous pourez rejouer le quiz dans '.$nbJour.' jours');
                return $this->redirectToRoute('app_quiz');
            }
        }else{
            $this->addFlash('error', "Ce quiz n'est pas encore disponible");
            return $this->redirectToRoute('app_quiz');
        }
        
        return $this->render('quiz/playQuiz.html.twig', [
            'category' => $category,
            'quiz' => $quiz,
            'quizJson' => $quizJson,
            'formQuiz' => $formQuiz,
        ]);
    }

    //Pour créer ou modifier une catégorie
    #[Route('/quiz/{idCategory}/new/', name: 'new_quiz')]
    #[Route('/quiz/{id}/edit/', name: 'edit_quiz')]
    public function newQuiz(Quiz $quiz = null, Request $request, EntityManagerInterface $entityManager,CategoryRepository $categoryRepository): Response
    {
        //si quiz n'éxiste pas
        if (!$quiz) {
            $quiz = new Quiz; // créer une nouvelle intance de Quiz
            $idCategory = $request->attributes->get('idCategory'); // récupère l'id Category dans l'url
            $category = $categoryRepository->findOneBy(['id' => $idCategory]); // on recupère l'entity category grace a son id
            $user = $this->getUser();//on récupère le user connecté
            $quiz->setUserId($user);//on ajoute le user au quiz
        }else{
            $category = $quiz->getCategory(); // si quiz existe on recupère la catégory contenu dans quiz
        }

        $quiz->setCategory($category); //ajoute quiz a sa sous catégorie 
        $quiz->setIsVerified(false); // met a false par default
        $formNewQuiz= $this->createForm(QuizType::class, $quiz);//crer le formulaire

        $formNewQuiz->handleRequest($request);
       
        //si le formulaire de Quiz est remplie et valide
        if ($formNewQuiz->isSubmitted() && $formNewQuiz->isValid()) {
            //récupère les donné du formulaire  
            $formNewQuiz->getData();
            // prepare PDO(prepare la requete Insert ou Update)
            $entityManager->persist($quiz);
            // execute PDO(la requete Insert ou Update)
            $entityManager->flush();
            //redirige ajout de question et réponse
            return $this->redirectToRoute('new_question',['idQuiz' => $quiz->getId()]);
        }

        return $this->render('quiz/newQuiz.html.twig', [
            'category' => $category,
            'quiz' => $quiz,
            'edit' => $quiz->getId(),
            'formNewQuiz' => $formNewQuiz,
            'quizId' => $quiz->getId(),
            // 'questionId' => $question->getId(),
        ]);
    }


    #[Route('admin/quiz/{id}/delete', name: 'delete_quiz')]
    public function deleteQuiz(Quiz $quiz = null, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($quiz);//suprime un quiz
        $entityManager->flush();

        return $this->redirectToRoute('app_quiz');
    }

}
