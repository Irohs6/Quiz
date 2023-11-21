<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    //route pour afficher le profil d'un uttilisateur
    #[Route('/user/profile/{id}/', name: 'user_profile')]
    public function showProfile(User $user, GameRepository $gameRepository): Response
    {   
        $games = $gameRepository->findBy(['userId'=> $user->getId()],[],3);
        $gamesUser = $gameRepository->findBy(['userId'=> $user->getId()]);
        return $this->render('user/show_user_profile.html.twig', [
            'games' => $games,
            'gamesUser' => $gamesUser
        ]);
    }

    //Route pour modifier un profil
    #[Route('/user/profile/edit/{id}/', name: 'edit_profile')]
    public function editUser(User $user,Request $request,EntityManagerInterface $entityManager): Response
    {
        $formEditUser= $this->createForm(UserType::class, $user);//crer le formulaire
        
        $formEditUser->handleRequest($request);

        //si user est égal au l'uttilisateur connecter
        if ($user == $this->getUser()) {
            //si le formulaire de Quiz est remplie et valide
            if ($formEditUser->isSubmitted() && $formEditUser->isValid()) {
                //récupère l'image selectionner par le user
                $imageName =  $request->request->all('formEditUser')['selectedProfileImage']; 
                //ajoute l'image au user
                $user->setProfileImage($imageName);
                //récupère les donné du formulaire  
                $formEditUser->getData();
                // prepare PDO(prepare la requete Insert ou Update)
                $entityManager->persist($user);
                // execute PDO(la requete Insert ou Update)
                $entityManager->flush();
                //redirige ajout de question et réponse
                return $this->redirectToRoute('user_profile',['id' => $user->getId()]);
            }
        }else{
            //sinon on le redirige vers sa propre page de modification de profil
            return $this->redirectToRoute('edit_profile',['id' => $this->getUser()->getId()]);
        }

        return $this->render('user/edit_user_profile.html.twig', [

            'formEditUser' => $formEditUser,

        ]);
    }

    //route pour afficher un tableau contenant les scores d'un utilisateur
    #[Route('/table_score/{id}/', name: 'table_score')]
    public function showScore(User $user, GameRepository $gameRepository)
    {
        $games = $gameRepository->findBy(['userId'=> $user->getId()]);
          

        return $this->render('user/template_show_tablescore.html.twig', [
           'gamesUser' => $games
        ]);

    }

    #[Route('add/quiz/favorite/{id}', name: 'app_add_favorite')]
    public function addFavoriteQuiz(Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
    
        // Ajouter le quiz aux favoris de l'utilisateur s'il ne l'a pas déjà ajouté
        if (!$user->getFavoritesQuizzes()->contains($quiz)) {
            $user->addFavoritesQuiz($quiz);
            $entityManager->persist($user);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_home_quiz');
    }

    #[Route('remove/quiz/favorite/{id}', name: 'app_remove_favorite')]
    public function removeFavoriteQuiz(Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Retirer le quiz des favoris de l'utilisateur s'il est présent
        if ($user->getFavoritesQuizzes()->contains($quiz)) {
            $user->removeFavoritesQuiz($quiz);
        }

        // Persister les modifications
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_home_quiz');
    }
}
