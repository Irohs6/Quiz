<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\GameRepository;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    //route pour afficher le profil d'un uttilisateur
    #[Route('/user/profile/{id}/', name: 'user_profile')]
    public function showProfile(User $user = null, GameRepository $gameRepository): Response
    {   
        //si $user est bien le meme que celui connecté
        if ($user == $this->getUser() && $user){

            $games = $gameRepository->findBy(['userId'=> $user->getId()],['score'=>'DESC'],3); // on récupère ses 3 meilleurs score
            $gamesUser = $gameRepository->findBy(['userId'=> $user->getId()],['dateGame' => 'DESC'],5); // on récupère ses 5 dernière 'GAME'

        }else{
            $this->addFlash('warning', "Vous n'avez pas le droit de modifier la page d'un autre utilisateur");
            //sinon on le redirige vers sa propre page de modification de profil
            return $this->redirectToRoute('user_profile',['id' => $this->getUser()->getId()]);
        }   
        return $this->render('user/show_user_profile.html.twig', [
            'games' => $games,
            'gamesUser' => $gamesUser
        ]);
    }

    //Route pour modifier un profil
    #[Route('/user/profile/edit/{id}/', name: 'edit_profile')]
    public function editUser(User $user = null,Request $request,EntityManagerInterface $entityManager): Response
    {
        $formEditUser= $this->createForm(UserType::class, $user);//crer le formulaire
        
        $formEditUser->handleRequest($request);

        //si user est bien l'uttilisateur connecter
        if ($user == $this->getUser() && $user) {
            //si le formulaire  est remplie et valide
            if ($formEditUser->isSubmitted() && $formEditUser->isValid()) {
                //récupère les donné du formulaire  
                $formEditUser->getData();
                // prepare PDO(prepare la requete Insert ou Update)
                $entityManager->persist($user);
                // execute PDO(la requete Insert ou Update)
                $entityManager->flush();

                $this->addFlash('success', 'Votre profil a bien été modifier');
                //redirige vers sa page de profil
                return $this->redirectToRoute('user_profile',['id' => $user->getId()]);
            }
        }else{
            $this->addFlash('warning', "Vous n'avez pas le droit de modifier la page d'un autre utilisateur");
            //sinon on le redirige vers sa propre page de modification de profil
            return $this->redirectToRoute('edit_profile',['id' => $this->getUser()->getId()]);
        }

        return $this->render('user/edit_user_profile.html.twig', [

            'formEditUser' => $formEditUser,

        ]);
    }

    #[Route('/user/pasword/edit/{id}/', name: 'edit_pasword')]
    public function editUserPasword(User $user,Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
    {
        $formEditUserPasword= $this->createForm(ChangePasswordFormType::class, $user);//crer le formulaire
        
        $formEditUserPasword->handleRequest($request);

        //si user est bien l'uttilisateur connecter
        if ($user == $this->getUser() && $user) {
            //si le formulaire  est remplie et valide
            if ($formEditUserPasword->isSubmitted() && $formEditUserPasword->isValid()) {
    
                // Encode(hash) the plain password, and set it.
                $encodedPassword = $passwordHasher->hashPassword(
                    $user,
                    $formEditUserPasword->get('plainPassword')->getData()
                );
    
                $user->setPassword($encodedPassword);
                $entityManager->persist($user);
                $entityManager->flush();
    
                $this->addFlash('success', "Votre mot de passe a été modifier");
                return $this->redirectToRoute('user_profile',['id' => $user->getId()]);
            }
        }else{
            $this->addFlash('warning', "Vous n'avez pas le droit de modifier la page d'un autre utilisateur");
            //sinon on le redirige vers sa propre page de modification de profil
            return $this->redirectToRoute('user_profile',['id' => $this->getUser()->getId()]);
        }

        return $this->render('user/edit_user_pasword.html.twig', [

            'formEditUserPasword' => $formEditUserPasword,

        ]);
    }

    //Route pour modifier un profil
    #[Route('/user/profile/delete/{id}/', name: 'delete_profile')]
    public function deleteUser(User $user = null,Request $request,EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $userConnected = $this->getUser();
        if ($user == $userConnected) {
            $quizzes = $user->getQuizzes(); // récupère ses quiz créer
            foreach ($quizzes as $quiz) {

                $user->removeQuiz($quiz);// retire l'association entre les utilisateur et ses quiz créer et set le userid de quiz a null
            }

            $tokenStorage->setToken(null);
            $entityManager->remove($user);//suprime le compte utilisateur
            $entityManager->flush();
    

            $this->addFlash('success', 'Votre profil a bien été supprimé');
            return $this->redirectToRoute('app_home_quiz');
        } else {
            $this->addFlash('warning', "Vous ne pouvez pas supprimer le profil d'un autre utilisateur ");
        }

    }

    

    #[Route('user/add/quiz/favorite/{id}', name: 'app_add_favorite')]
    public function addFavoriteQuiz(Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
    
        // Ajouter le quiz aux favoris de l'utilisateur s'il ne l'a pas déjà ajouté
        if (!$user->getFavoritesQuizzes()->contains($quiz)) {
            $user->addFavoritesQuiz($quiz);
            $entityManager->persist($user);
            $entityManager->flush();
        }
        $this->addFlash('success', 'Ce Quiz a été ajouter a vos favoris');
        return $this->redirectToRoute('app_home_quiz');
    }

    #[Route('user/remove/quiz/favorite/{id}', name: 'app_remove_favorite')]
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

        $this->addFlash('success', 'Ce Quiz ne fait plus partie de vos favoris');

        return $this->redirectToRoute('app_home_quiz');
    }
}
