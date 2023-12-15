<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\User;
use App\Entity\Theme;
use App\Form\ThemeType;
use App\Entity\Category;
use App\Repository\UserRepository;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    //route vers le panneaux admin
    #[Route('/admin/pannel', name: 'app_admin_panel')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    //route pour gèrer les  roles et le statut des utilisateurs
    #[Route(path: 'admin/panel/userManagement', name: 'app_userManagement')]
    public function userList(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();//récupère tous les uttilisateur
       
        return $this->render('admin/user_list.html.twig', [
            'users' => $users,
        ]);
    }

    //changement de role pour un utilisateur
    #[Route(path: 'admin/panel/userManagementRole/{id}', name: 'app_userManagementRole')]
    public function addRoleModerator(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setRoles(['ROLE_MODERATOR']);//change le role a moderateur

        $entityManager->flush();

        return $this->redirectToRoute('app_userManagement');
    }

    //Pour bannir un utilisateur
    #[Route(path: 'admin/panel/isBanned/{id}', name: 'app_userIsBanned')]
    public function bannedUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setIsBanned(true);

        $entityManager->flush();

        return $this->redirectToRoute('app_userManagement');
    }

    //pour débanir un utilisateur********************************************
    #[Route(path: 'admin/panel/unBanned/{id}', name: 'app_userUnBanned')]
    public function unbannedUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setIsBanned(false);
        $entityManager->flush();

        return $this->redirectToRoute('app_userManagement');
    }

    #[Route('admin/create/theme', name: 'new_theme')]
    #[Route('admin/theme/edit/{id}', name: 'edit_theme')]
    public function createEditTheme(Theme $theme = null, EntityManagerInterface $entityManager, Request $request): Response
    {
        //si theme n'existe pas 
        if (!$theme) {
            $theme = new Theme;//on créer une nouvelle instance de theme
        }

        $formNewTheme = $this->createForm(ThemeType::class, $theme);//crer le formulaire

        $formNewTheme->handleRequest($request);
       
        //si le formulaire de Quiz est remplie et valide
        if ($formNewTheme->isSubmitted() && $formNewTheme->isValid()) {
            //récupère les donné du formulaire  
            $formNewTheme->getData();
            // prepare PDO(prepare la requete Insert ou Update)
            $entityManager->persist($theme);
            // execute PDO(la requete Insert ou Update)
            $entityManager->flush();
            //redirige vers la list des theme
            return $this->redirectToRoute('list_theme');
        }

        return $this->render('admin/new_edit_theme.html.twig', [
            'formNewTheme' => $formNewTheme,
            'edit' => $theme->getId(),
            'theme' =>$theme,
        ]);
    }

    //pour afficher la liste des themes
    #[Route('moderator/list/theme', name: 'list_theme')]
    public function listTheme(ThemeRepository $themeRepository): Response
    {
        $themes = $themeRepository->findAll();
    
        return $this->render('theme/list_theme.html.twig', [
            'themes' =>$themes
        ]);
    }
     
    #[Route('admin/quiz/{id}/delete', name: 'delete_quiz')]
    public function deleteQuiz(Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        foreach ($quiz->getQuestions() as $question) {
            $quiz->removeQuestion($question);
        }

        $entityManager->remove($quiz);//suprime un quiz
        $entityManager->flush();

        return $this->redirectToRoute('app_list_quiz');
    }

    #[Route('admin/category/{id}/delete', name: 'delete_category')]
    public function deleteCategory(Category $category, EntityManagerInterface $entityManager): Response
    {
        
        $entityManager->remove($category);//suprime cet category les quizs et les questions associer
        $entityManager->flush();

        return $this->redirectToRoute('app_list_quiz');
    }

    #[Route('admin/theme/{id}/delete', name: 'delete_theme')]
    public function deleteTheme(Theme $theme, EntityManagerInterface $entityManager): Response
    {
        
        $entityManager->remove($theme);//suprime ce theme
        $entityManager->flush();

        return $this->redirectToRoute('app_list_quiz');
    }

}
