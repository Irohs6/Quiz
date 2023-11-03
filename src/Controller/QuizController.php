<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Game;
use App\Entity\Quiz;
use App\Form\QuizType;
use App\Form\PlayQuizzType;

use App\Repository\ThemeRepository;
use App\Repository\CategoryRepository;
use App\Repository\LevelRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuizController extends AbstractController
{
    #[Route('/quiz', name: 'app_quiz')]
    public function index(ThemeRepository $themeRepository, CategoryRepository $categoryRepository, LevelRepository $levelRepository): Response
    {
        $allTheme = $themeRepository->findAll();//recupère toute les donné de la table theme
        $allCategories = $categoryRepository->findAll();//recupère toute les donné de la table category
        $allLevel = $levelRepository->findAll();//recupère toute les donné de la table level

        return $this->render('quiz/index.html.twig', [
            'allTheme' => $allTheme,
            'allCategories' => $allCategories,
            'allLevel'=>$allLevel
        ]);
    }

    #[Route('/quiz/play/{id}', name: 'app_play')]
    public function playQuiz(Quiz $quiz, Request $request, EntityManagerInterface $entityManager): Response
    {
        $quizData = [
            'titre' => $quiz->getTitle(),
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

        $game = new Game; // nouvelle instance de Game
        $quiz->addGame($game);// ajout du quiz dans Game

        $formQuiz = $this->createForm(PlayQuizzType::class, $question, ['attr' => ['class' => 'formQuiz']]); //creer le formulaire
        
        $formQuiz->handleRequest($request);
     
       
        //si le formQuizulaire est remplie et valide
        if ($formQuiz->isSubmitted() && $formQuiz->isValid()) {
            $recapData = $request->request->get('recapData');//récupère le tableau de récapitulatif du quiz en json
            
            $recapDataArray = json_decode($recapData, true);// convertit en une structure de données PHP dans notre cas un tableau associatif
           
            $data = $formQuiz->getData();
            
            foreach ($recapDataArray as $questionId => $value ) {  // pour chaque id question on récupère les valeur
                //dd($recapdataArray)=
                //     array:1 [▼
                    //     12 => array:1 [▼
                    //       0 => array:5 [▼
                    //         "questionId" => 12
                    //         "questionIntitulle" => "qqch"
                    //         "answerId" => "31"
                    //         "answerIntitulle" => "untruc"
                    //         "answerIsright" => true
                    //       ]
                    //     ]
                    //]
                $answer = $entityManager->getRepository(Answer::class)->findOneBy(['id' => $value[0]['answerId']]);// je récupère l'id de la réponse pour récupérer mon entity answer
                $game->addAnswer($answer);   //j'ajoute l'entity answer a la game  

            }  
                            
            // prepare PDO(prepare la requete Insert ou Update)
            $entityManager->persist($game);
            // execute PDO(la requete Insert ou Update)
            $entityManager->flush();
            //redirige ver le home qui est la liste des formation
            return $this->redirectToRoute('app_quiz');
        
        }

        return $this->render('quiz/playQuiz.html.twig', [
            'quiz' => $quiz,
            'quizJson' => $quizJson,
            'formQuiz' => $formQuiz,
        ]);
    }

    #[Route('/quiz/{idCategory}/new/', name: 'new_quiz')]
    #[Route('/quiz/{id}/edit/', name: 'edit_quiz')]
    public function newQuiz(Quiz $quiz = null, Request $request, EntityManagerInterface $entityManager,CategoryRepository $categoryRepository): Response
    {
        if (!$quiz) {
            $quiz = new Quiz; // créer une nouvelle intance de Quiz
            $idCategory = $request->attributes->get('idCategory'); // récupère l'id Category dans l'url
            $category = $categoryRepository->findOneBy(['id' => $idCategory]); // on recupère l'entity category grace a son id
           
        }else{
            $category = $quiz->getCategory(); // si quiz existe on recupère la catégory contenu dans quiz
        }
        // $question = new Question;
        // $reponse = New Answer;
        // $reponse->setQuestion($question);
        // $question->addAnswer($reponse);
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

    #[Route('/quiz/{id}/delete', name: 'delete_quiz')]
    public function deleteQuiz(Quiz $quiz = null, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($quiz);//suprime un quiz
        $entityManager->flush();

        return $this->redirectToRoute('app_quiz');
    }

    #[Route('/quiz/{id}/show', name: 'show_quiz')]
    public function showQuiz(Quiz $quiz, QuestionRepository $questionRepository): Response
    {
        $questionNotInQuiz = $questionRepository->questionsNotInQuiz($quiz->getId());//pour afficher la liste des question qui ne sont pas dans ce quiz
        return $this->render('quiz/show_quiz.html.twig', [
            'questionNotInQuiz' => $questionNotInQuiz,
            'quiz' => $quiz,
        ]);
    }

    
}
