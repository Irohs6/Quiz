<?php

namespace App\Controller;

use DateTime;
use App\Entity\Game;
use App\Entity\Quiz;
use App\Entity\Answer;
use App\Form\QuizType;
use App\Form\PlayQuizzType;
use App\Repository\GameRepository;
use App\Repository\LevelRepository;
use App\Repository\ThemeRepository;
use App\Repository\CategoryRepository;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class QuizController extends AbstractController
{

    //futur home de quiz en cour de création
    #[Route('home/quiz', name: 'app_home_quiz')]
    public function home_quiz(ThemeRepository $themeRepository, CategoryRepository $categoryRepository, LevelRepository $levelRepository, GameRepository $gameRepository,QuizRepository $quizRepository): Response
    {
        $allTheme = $themeRepository->findAll();//recupère toute les donné de la table theme
        $allCategories = $categoryRepository->findAll();//recupère toute les donné de la table category
        $allLevel = $levelRepository->findAll();//recupère toute les donné de la table level
        $allGames = $gameRepository->findAllBestGame();//recupère toute les donné de la table game
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
        return $this->render('quiz/home_quiz.html.twig', [
            'allTheme' => $allTheme,
            'allCategories' => $allCategories,
            'allLevel'=> $allLevel,
            'gamesPlay' => $gamesPlay,
            'gamesForQuizzes' => $gamesForQuizzes,
            'allGames' => $allGames
        ]);
    }
   
    //pages pour jouer un quiz
    #[Route('user/quiz/play/{id}', name: 'app_play')]
    public function playQuiz(Quiz $quiz, Request $request, EntityManagerInterface $entityManager,GameRepository $gameRepository): Response
    {

        $session = $request->getSession();
        $quizData = [
            'titre' => $quiz->getTitle(),//ajoute le titre du quiz
            'questions' => [], // Initialise le tableau des questions 
        ];
        // boucle sur la collection de question d'un quiz
        foreach ($quiz->getQuestions() as $question) {
            if ($question->getLink()) {
                $link = $question->getLink(); // si $link est vrais le stock dans la variable
            }else{
                $link = ''; // si $link est faut set la varaible avec une chaine vide
            }
            $questionData = [
                'id' => $question->getId(),//ajoute l'id de la question au tableau questionData
                'question' => $question->getSentence(),//ajoute l'intitulé de la question au tableau questionData
                'link' => $link, // ajoute le link ou la chaine de caractère vide au tableau questionData
                'reponses' => [], // Initialise le tableau des réponses au tableau questionData
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

        $gamePlay = $gameRepository->findOneBy(['userId'=>$this->getUser()->getId(), 'quiz' => $quiz->getId()], ['dateGame'=> 'DESC' ]); // récupère la dernière partit de l'uttilisateur connecté sur ce quiz
        // si une partie existe
        if ($gamePlay) {
             $date = $gamePlay->getDateGame(); // Stocke la date dans une variable
             $dateModify = date_modify($date ,"+7 day"); // modify la date de 7 jours
        }
       
        $now = new DateTime(); // stocke la date du jour

        //si le quiz est vérifier et si le role est modérateur ou admin
        if ($quiz->isIsVerified()|| $this->isGranted('ROLE_MODERATOR')) {
            // si il n'y a pas de partie jouer ou si la date du jours est égal a la date modifier ou si le role est moderateur ou admin
            if (!$gamePlay || $now == $dateModify || $this->isGranted('ROLE_MODERATOR')){

                $allGameUser = $gameRepository->findBy(['userId'=>$this->getUser()->getId(), 'quiz' => $quiz->getId()],['score'=> 'ASC']); // récupère toute les partit du user sur se quiz 
               
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
                $this->addFlash('warning', 'Vous pourez rejouer le quiz dans '.$nbJour.' jours');
                return $this->redirectToRoute('app_home_quiz');
            }
        }else{
            $this->addFlash('warning', "Ce quiz n'est pas encore disponible");
            return $this->redirectToRoute('app_home_quiz');
        }
        
        return $this->render('quiz/playQuiz.html.twig', [
            'category' => $category,
            'quiz' => $quiz,
            'quizJson' => $quizJson,
            'formQuiz' => $formQuiz,
        ]);
    }

    #[Route('user/quiz/recap/', name: 'app_recap')]
    public function recapQuiz()
    {
        $session = new Session();
        $recapData = $session->get('recap');
        
        $score = 0; // Initialiser la variable $score
        $quizRecaps = []; // Initialiser le tableau $quizRecaps
        
        if ($recapData) {
           
            foreach ($recapData as $data) {
                if (isset($data['score'])) {
                    $score = $data['score'];
                } else {
                    foreach ($data as $quizRecap) {
                        $quizRecaps[] = $quizRecap; // Ajouter les éléments à $quizRecaps
                    }
                }
            }
        } else {
            $this->addFlash('warning', "Vous n'avez jouer aucune partit sur cet session vous n'avez donc aucun recapitulatif a affiché.");
            return $this->redirectToRoute('app_home_quiz');
        }
        return $this->render('quiz/recap_game_quiz.html.twig', [
            'score' => $score,
            'quizsRecap' => $quizRecaps
        ]);
    }

    //Pour créer ou modifier un quiz
    #[Route('user/quiz/{idCategory}/new/', name: 'new_quiz')]
    public function newQuiz(Quiz $quiz = null, Request $request, EntityManagerInterface $entityManager,CategoryRepository $categoryRepository): Response
    {

        if ( $this->getUser()  || $this->isGranted('ROLE_MODERATOR')  ) {
            $quiz = new Quiz;
            $idCategory = $request->attributes->get('idCategory');
            $category = $categoryRepository->findOneBy(['id' => $idCategory]);
            $user = $this->getUser();
            $quiz->setUserId($user); 

            $quiz->setCategory($category); //ajoute quiz a sa catégorie 
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
        
                $this->addFlash('success', "Votre Quiz a bien été créer vous recevrez une confirmation par email une fois qu'il sera validé.");
                return $this->redirectToRoute('app_list_quiz'); // redirige vers le détail d'un quiz
            }
        } else {
            
            $this->addFlash('warning', "Vous n'avez pas les autorisation pour cet action.");
            // Redirection vers une page de confirmation ou vers la gestion du quiz
            return $this->redirectToRoute('app_home_quiz');
        }
        return $this->render('quiz/newQuiz.html.twig', [
            'category' => $category,
            'quiz' => $quiz,
            'edit' => $quiz->getId(),
            'formNewQuiz' => $formNewQuiz,
            'quizId' => $quiz->getId(),
        ]);
    }

    #[Route('user/quiz/{id}/edit/', name: 'edit_quiz')]
    public function editQuiz(Quiz $quiz, Request $request, EntityManagerInterface $entityManager): Response
    {
        
        if ( $this->getUser() == $quiz->getUserId() || $this->isGranted('ROLE_MODERATOR')  ) {
            
            // Récupérer les questions et réponses associées au quiz existant
            $questions = $quiz->getQuestions();
            $category = $quiz->getCategory();

            // Créer le formulaire en utilisant le quiz et ses données associées
            $formEditQuiz = $this->createForm(QuizType::class, $quiz);

            $formEditQuiz->handleRequest($request);

            if ($formEditQuiz->isSubmitted() && $formEditQuiz->isValid()) {
                $data = $formEditQuiz->getData();
                // Mettre à jour les questions et réponses existantes si nécessaire
                foreach ($questions as $question) {
                    $question->setCategory($category); // Associer la catégorie à la nouvelle questio
                    $question->setQuiz($quiz);
                }

                $entityManager->persist($quiz);
                $entityManager->flush();

                $this->addFlash('success', 'Votre Quiz a bien été modifier.');
                // Redirection vers une page de confirmation ou vers la gestion du quiz
                return $this->redirectToRoute('show_quiz',['id'=> $quiz->getId()]);
            }

        } else {
            
            $this->addFlash('warning', "Vous n'avez pas les autorisation pour cet action.");
            // Redirection vers une page de confirmation ou vers la gestion du quiz
            return $this->redirectToRoute('app_home_quiz');
        }

        return $this->render('quiz/edit_quiz.html.twig', [
            'quiz' => $quiz,
            'edit' => $quiz->getId(),
            'quizId' => $quiz->getId(),
            'formEditQuiz' => $formEditQuiz->createView(),
        ]);
    }

    //permet de voir le détail d'un quiz ou un modérateur pourra enlever ou ajouter des questions modifier une question et ses réponses
    #[Route('user/quiz/{id}/show', name: 'show_quiz')]
    public function showQuiz(Quiz $quiz, QuestionRepository $questionRepository): Response
    {
        $user = $this->getUser();
        if ($user == $quiz->getUserId() ||$this->isGranted('ROLE_MODERATOR') || !$user ) {

            $questionNotInQuiz = $questionRepository->questionsNotInQuiz($quiz->getId());//pour afficher la liste des question qui ne sont pas dans ce quiz
        }else{
            $this->addFlash('warning', "Vous n'avez pas les autorisation pour cet action.");
            return $this->redirectToRoute('app_home');
        }
        return $this->render('quiz/show_quiz.html.twig', [
            'questionNotInQuiz' => $questionNotInQuiz,
            'quiz' => $quiz,
        ]);
    }

}
