<?php

namespace App\Controller;

use DateTime;
use App\Entity\Game;
use App\Entity\Quiz;
use App\Entity\Answer;
use App\Form\QuizType;
use App\Entity\Question;
use App\Form\PlayQuizzType;
use App\Repository\GameRepository;
use App\Repository\LevelRepository;
use App\Repository\ThemeRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class QuizController extends AbstractController
{
    // //pour afficher les theme les catégorie et leurs quiz
    // #[Route('/quiz', name: 'app_quiz')]
    // public function home(ThemeRepository $themeRepository, CategoryRepository $categoryRepository, LevelRepository $levelRepository,GameRepository $gameRepository): Response
    // {
    //     $allTheme = $themeRepository->findAll();//recupère toute les donné de la table theme
    //     $allCategories = $categoryRepository->findAll();//recupère toute les donné de la table category
    //     $allLevel = $levelRepository->findAll();//recupère toute les donné de la table level
    //     if (!$this->getUser()) {
    //         $gamesPlay = "";
    //     }else{
    //         $gamesPlay = $gameRepository->findBy(['userId'=>$this->getUser()->getId()]);
    //     }
    //     return $this->render('quiz/home.html.twig', [
    //         'allTheme' => $allTheme,
    //         'allCategories' => $allCategories,
    //         'allLevel'=>$allLevel,
    //         'games' => $gamesPlay

    //     ]);
    // }

    //futur home de quiz en cour de création
    #[Route('home/quiz', name: 'app_home_quiz')]
    public function home_quiz(ThemeRepository $themeRepository, CategoryRepository $categoryRepository, LevelRepository $levelRepository, GameRepository $gameRepository): Response
    {
        $allTheme = $themeRepository->findAll();//recupère toute les donné de la table theme
        $allCategories = $categoryRepository->findAll();//recupère toute les donné de la table category
        $allLevel = $levelRepository->findAll();//recupère toute les donné de la table level
        if (!$this->getUser()) {
            $gamesPlay = "";
        }else{
            $gamesPlay = $gameRepository->findBy(['userId'=>$this->getUser()->getId()]);
        }
        return $this->render('quiz/home_quiz.html.twig', [
            'allTheme' => $allTheme,
            'allCategories' => $allCategories,
            'allLevel'=> $allLevel,
            'game' => $gamesPlay
        ]);
    }


    //pages pour jouer un quiz
    #[Route('/quiz/play/{id}', name: 'app_play')]
    public function playQuiz(Quiz $quiz, Request $request, EntityManagerInterface $entityManager,GameRepository $gameRepository): Response
    {

        $session = $request->getSession();
        $quizData = [
            'titre' => $quiz->getTitle(),//ajoute le titre du quiz
            'questions' => [], // Initialise le tableau des questions 
        ];
        
        foreach ($quiz->getQuestions() as $question) {
            if ($question->getLink()) {
                $link = $question->getLink();
            }else{
                $link = '';
            }
            $questionData = [
                'id' => $question->getId(),//ajoute l'id de la question
                'question' => $question->getSentence(),//ajoute l'intitulé de la question
                'link' => $link,
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
       
        
        $formQuiz = $this->createForm(PlayQuizzType::class, $question, ['attr' => ['class' => 'formQuiz']]); //creer le formulaire
        
        $formQuiz->handleRequest($request);
        // $level = $quiz->getLevel(); // on récupère le niveaux de difficulté 
        // $scoreCoeff = $level->getScoreCoef(); //on récupère le coefficient

        $gamePlay = $gameRepository->findOneBy(['userId'=>$this->getUser()->getId(), 'quiz' => $quiz->getId()]);
        if ($gamePlay) {
             $date = $gamePlay->getDateGame();
             $dateModify = date_modify($date ,"+7 day");
        }
       
        $now = new DateTime();

        if ($quiz->isIsVerified()|| $this->isGranted('ROLE_MODERATOR')) {
            if (!$gamePlay || $now == $dateModify || $this->isGranted('ROLE_MODERATOR')){
                $allGameUser = $gameRepository->findBy(['userId'=>$this->getUser()->getId(), 'quiz' => $quiz->getId()],['score'=> 'ASC']);
               
                //si le formQuizulaire est remplie et valide
                if ($formQuiz->isSubmitted() && $formQuiz->isValid()) {

                    $recapData = $request->request->get('recapData');//récupère le tableau de récapitulatif du quiz en json
                    
                    $recapDataArray = json_decode($recapData, true);// convertit en une structure de données PHP dans notre cas un tableau associatif
                    
                    $session->set('recap', $recapDataArray);

                    foreach ($recapDataArray as $data) {
                        if (isset($data['score'])) {
                            $score = $data['score'];
                            
                        } else {
                            foreach ($data as $response) {
                                $answer = $entityManager->getRepository(Answer::class)->findOneBy(['id' => $response['answerId']]);//récupère la question grace a son id contenu dans le tableau
                            }
                        }
                    }
                    if (count($allGameUser) == 5) {
                        $game = $gameRepository->findOneBy(['userId'=>$this->getUser()->getId(), 'quiz' => $quiz->getId()],['score'=> 'ASC']);
                        $now = new DateTime();
                        $game->setDateGame($now);
                        
                        if ($game->getScore() < $score) {
                            $game->setScore($score);
                        }
                      
                        $entityManager->persist($game);
                        // execute PDO(la requete Insert ou Update)
                        $entityManager->flush();
                    }else{

                        $game = new Game; // nouvelle instance de Game
                        $game->addAnswer($answer);//ajoute les question a la game
                        $quiz->addGame($game);// ajout du quiz dans Game
                        $user = $this->getUser(); // on récupère l'user en session
                        $game->setUserId($user); // on rajoute l'user en session a la Game
                        $now = new DateTime();
                        $game->setDateGame($now);
                        $game->setScore($score);//ajoute le score a la game
                        // prepare PDO(prepare la requete Insert ou Update)
                        $entityManager->persist($game);
                        // execute PDO(la requete Insert ou Update)
                        $entityManager->flush();
                        //redirige ver le home qui est la liste des formation
                    
                    }
                    return $this->redirectToRoute('app_recap');
                
                }

            }else{
            
                $nbJour = $dateModify->diff($now)->format("%d");
                $this->addFlash('error', 'Vous pourez rejouer le quiz dans '.$nbJour.' jours');
                return $this->redirectToRoute('app_home_quiz');
            }
        }else{
            $this->addFlash('error', "Ce quiz n'est pas encore disponible");
            return $this->redirectToRoute('app_home_quiz');
        }
        
        return $this->render('quiz/playQuiz.html.twig', [
            'category' => $category,
            'quiz' => $quiz,
            'quizJson' => $quizJson,
            'formQuiz' => $formQuiz,
        ]);
    }

    #[Route('/quiz/recap/', name: 'app_recap')]
    public function recapQuiz()
    {
        $session = new Session();
        $recapData = $session->get('recap');

        foreach ($recapData as $data) {
            if (isset($data['score'])) {
                $score = $data['score'];
                
            } else {
                foreach ($data as $quizRecap) {
                    $quizRecaps = [$quizRecap] ;
                }
            }
        }
        return $this->render('quiz/recap_game_quiz.html.twig',[
            'score' => $score,
            'quizsRecap' => $quizRecaps
        ]);
    }
    //Pour créer ou modifier une catégorie
    #[Route('/quiz/{idCategory}/new/', name: 'new_quiz')]
    
    public function newQuiz(Quiz $quiz = null, Request $request, EntityManagerInterface $entityManager,CategoryRepository $categoryRepository): Response
    {
        
        $quiz = new Quiz;
        $idCategory = $request->attributes->get('idCategory');
        $category = $categoryRepository->findOneBy(['id' => $idCategory]);
        $user = $this->getUser();
        $quiz->setUserId($user); 

        $quiz->setCategory($category); //ajoute quiz a sa sous catégorie 
        $quiz->setIsVerified(false); // met a false par default
        $formNewQuiz= $this->createForm(QuizType::class, $quiz);//crer le formulaire

        $formNewQuiz->handleRequest($request);
        if ($formNewQuiz->isSubmitted() && $formNewQuiz->isValid()) {
            // Récupérer les données du formulaire
            $data = $formNewQuiz->getData();
            foreach ($quiz->getQuestions() as $question ) {
                $question->setCategory($category); // Associer la catégorie à la nouvelle question
               
            }
            
            // Persistez le quiz mis à jour
            $entityManager->persist($quiz);
            $entityManager->flush();
    
            
            return $this->redirectToRoute('app_list_quiz'); // redirige vers le détail d'un quiz
        }
    
        return $this->render('quiz/newQuiz.html.twig', [
            'category' => $category,
            'quiz' => $quiz,
            'edit' => $quiz->getId(),
            'formNewQuiz' => $formNewQuiz,
            'quizId' => $quiz->getId(),
        ]);
    }

    #[Route('/quiz/{id}/edit/', name: 'edit_quiz')]
    public function editQuiz(Quiz $quiz, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les questions et réponses associées au quiz existant
        $questions = $quiz->getQuestions();
        $category = $quiz->getCategory();

        // Créer le formulaire en utilisant le quiz et ses données associées
        $formEditQuiz = $this->createForm(QuizType::class, $quiz);

        $formEditQuiz->handleRequest($request);

        if ($formEditQuiz->isSubmitted() && $formEditQuiz->isValid()) {
            // Mettre à jour les questions et réponses existantes si nécessaire
            foreach ($questions as $question) {
                $question->setCategory($category); // Associer la catégorie à la nouvelle questio
                $question->setQuiz($quiz);
            }

            $entityManager->persist($quiz);
            $entityManager->flush();

            // Redirection vers une page de confirmation ou vers la gestion du quiz
            return $this->redirectToRoute('show_quiz',['id',$quiz->getId()]);
        }

        return $this->render('quiz/edit_quiz.html.twig', [
           
            'quiz' => $quiz,
            'edit' => $quiz->getId(),
            
            'quizId' => $quiz->getId(),
            'formEditQuiz' => $formEditQuiz->createView(),
        ]);
    }

    #[Route('admin/quiz/{id}/delete', name: 'delete_quiz')]
    public function deleteQuiz(Quiz $quiz = null, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($quiz);//suprime un quiz
        $entityManager->flush();

        return $this->redirectToRoute('app_home_quiz');
    }

}
