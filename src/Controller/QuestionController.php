<?php

namespace App\Controller;


use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuestionController extends AbstractController
{
  
    //pour créer un nouvelle question ou editer une question 
    #[Route('/question/new/{idQuiz}', name: 'new_question')]
    #[Route('/question/edit/{id}', name: 'edit_question')]
    public function addEditQuestion(Question $question = null, Request $request, EntityManagerInterface $entityManager, QuizRepository $quizRepository): Response
    {
        // si question n'existe pas 
        if(!$question){
            $question = new Question; // créer une nouvelle intance de question
            $quizId = $request->attributes->get('idQuiz');//on récupère l'id de quiz dans l'url
            $quiz = $quizRepository->findOneBy(['id' => $quizId]);// on récupére le quiz grace a son id
            $category = $quiz->getCategory();
            $message = 'Votre question a bien été ajouté'; 
            
        }else{
            $quiz = $question->getQuiz();// si quiz existe on récupère le Quiz appartenant a la question
            $category = $quiz->getCategory(); //// si quiz existe on récupère la catégorie appartenant a la question
        }
        $question->setQuiz($quiz); //ajoute la question dans son  quiz
        $question->setCategory($category);// ajoute la catégorie a la question
        
        $formNewquestion = $this->createForm(QuestionType::class, $question);//crer le formulaire

        $formNewquestion->handleRequest($request);
        
        //si le formulaire de question est remplie et valide
        if ($formNewquestion->isSubmitted() && $formNewquestion->isValid()) {
            //récupère les donné du formulaire  
            $question = $formNewquestion->getData();
            // prepare PDO(prepare la requete Insert ou Update)
            $entityManager->persist($question);
            // execute PDO(la requete Insert ou Update)
            $entityManager->flush();
            //redirige vers la liste des question
            return $this->redirectToRoute('show_quiz',['id' => $quiz->getId()]); // redirige vers la création des question 
        }

        return $this->render('question/newQuestion.html.twig', [
            'quiz' => $quiz,
            'edit' => $question->getId(),
            'formNewQuestion' => $formNewquestion,
            'questionId' => $question->getId(),
        ]);
    }


    // pour retirer une question d'un quiz
    #[Route('unset/question/{id}/{idQuiz}/unset_quiz', name: 'app_unset_quiz')]
    public function unsetQuiz(Question $question,Request $request, EntityManagerInterface $entityManager, QuizRepository $quizRepository): Response
    {   

        $idQuiz = $request->attributes->get('idQuiz'); //on recupère l'id du quiz contenu dans l'url
        $quiz = $quizRepository->findOneBy(['id'=> $idQuiz]); //on récupère le quiz grace a cet id
        $quiz->removeQuestion($question);// retire la question de la collection
        // prepare PDO(prepare la requete Insert ou Update)
        $entityManager->persist($quiz);
        // execute PDO la requete insert ou update
        $entityManager->flush();

        return $this->redirectToRoute('show_quiz', ['id'=> $quiz->getId()]); //// redirige vers le détail d'un quiz
 
    }  

    //pour ajouter une question et ses réponses a un quiz
    #[Route('add/question/{id}/{idQuiz}/add_quiz', name: 'app_add_quiz')]
    public function addQuiz(Question $question,Request $request, EntityManagerInterface $entityManager, QuizRepository $quizRepository): Response
    {   

        $idQuiz = $request->attributes->get('idQuiz');  //on recupère l'id du quiz contenu dans l'url
        $quiz = $quizRepository->findOneBy(['id'=> $idQuiz]); //on récupère le quiz grâce a cet id
        $question->setQuiz($quiz); // on ajoute la question dans le quiz
        // prepare PDO(prepare la requete Insert ou Update)
        $entityManager->persist($quiz);
        // execute PDO la requete insert ou update
        $entityManager->flush();

        return $this->redirectToRoute('show_quiz', ['id'=> $quiz->getId()]);// redirige vers le détail d'un quiz
         
    }  

}
