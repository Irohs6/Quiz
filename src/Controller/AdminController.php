<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\User;
use App\Entity\Theme;
use App\Form\ThemeType;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\FileUploader;
use App\Repository\QuizRepository;
use App\Repository\UserRepository;
use App\Repository\ThemeRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
   

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
        $this->addFlash('success', "Cet utilisateur est devenue un Modérateur.");
        return $this->redirectToRoute('app_userManagement');
    }

    //changement de role pour un utilisateur
    #[Route(path: 'admin/panel/userManagementRole/{id}', name: 'app_userManagementRole')]
    public function removeRoleModerator(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setRoles(['ROLE_USER']);//change le role a user

        $entityManager->flush();
        $this->addFlash('success', "Cet utilisateur n'est plus un Modérateur.");
        return $this->redirectToRoute('app_userManagement');
    }

    //Pour bannir un utilisateur
    #[Route(path: 'admin/panel/isBanned/{id}', name: 'app_userIsBanned')]
    public function bannedUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setIsBanned(true);

        $entityManager->flush();
        $this->addFlash('success', "Cet utilisateur a bien été banni.");
        return $this->redirectToRoute('app_userManagement');
    }

    //pour débanir un utilisateur********************************************
    #[Route(path: 'admin/panel/unBanned/{id}', name: 'app_userUnBanned')]
    public function unbannedUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setIsBanned(false);
        $entityManager->flush();
        $this->addFlash('success', "Cet Utilisateur a bien été débannie.");
        return $this->redirectToRoute('app_userManagement');
    }

    #[Route('admin/list/quizzes', name: 'list_quizzes')]
    public function listQuizes(QuizRepository $quizRepository): Response
    {
        $quizzes = $quizRepository->findAll();
    
        return $this->render('admin/list_quizzes_admin.html.twig', [
            'quizzes' =>$quizzes
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
        $this->addFlash('success', "Quiz supprimé avec succès.");
        return $this->redirectToRoute('app_list_quiz');
    }

    #[Route('admin/list/categories', name: 'list_categories')]
    public function listCategory(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
    
        return $this->render('admin/list_categories_admin.html.twig', [
            'categories' =>$categories
        ]);
    }

    //Pour créer un nouvelle catégorie dans un thème définie *************************************************************************************************
    #[Route('admin/category/new/{idTheme}', name: 'new_category')]
    #[Route('admin/category/edit/{id}', name: 'edit_category')]
    public function newEditCategory(Category $category = null,Request $request, ThemeRepository $themeRepository, EntityManagerInterface $entityManager,FileUploader $fileUploader): Response
    {
        //si la catégorie n'existe pas 
        if (!$category) {
            $category = new Category;// on créer une nouvelle instance de catégorie
            $idTheme = $request->attributes->get('idTheme');// on récupère l'id theme contenu dans l'url
            $theme = $themeRepository->findOneBy(['id' => $idTheme]); // on récupère l'entity theme garce a cet id
            $picture = null; // on set la variable a null si la catégorie n'existe pas
        }else{
            //si la catégorie existe
            $theme = $category->getTheme(); //on récupère le theme contenu dans la catégorie
            // Récupérez le nom du fichier depuis l'entité
            $picture = $category->getPicture();//on récupère l'image de la catégorie
        }
          
        $category->setTheme($theme);//on ajoute le thème a la catégorie
        $formNewCategory= $this->createForm(CategoryType::class, $category);//créer le formulaire

        $formNewCategory->handleRequest($request);
        
        //si le formulaire de Quiz est remplie et valide
        if ($formNewCategory->isSubmitted() && $formNewCategory->isValid()) {
            /** @var UploadedFile $pictureFile */
        // on récupère l'image
        $pictureFile = $formNewCategory->get('picture')->getData();
        //si l'image existe
        if ($pictureFile) {
            //on utilise la fonction upload du service File upload qi va modifier le name avec un id unique
            $pictureFileName = $fileUploader->upload($pictureFile);
            // on set l'image modifier dans la catégorie
            $category->setPicture($pictureFileName);
        }else{
            //si l'image n'a pas changer on remet la meme
            $category->setPicture($picture);
        }
        
            //récupère les donné du formulaire  
            $formNewCategory->getData();
            // prepare PDO(prepare la requete Insert ou Update)
            $entityManager->persist($category);
            // execute PDO(la requete Insert ou Update)
            $entityManager->flush();
            //redirige ajout de question et réponse
            return $this->redirectToRoute('list_categories');
        }

        return $this->render('category/new_edit_category.html.twig', [
            'category' => $category,
            'edit' => $category->getId(),
            'formNewCategory' => $formNewCategory,
            'theme' => $theme
        ]);
    }
      
    #[Route('admin/category/{id}/delete', name: 'delete_category')]
    public function deleteCategory(Category $category, EntityManagerInterface $entityManager): Response
    {
        
        $entityManager->remove($category);//suprime cet category les quizs et les questions associer
        $entityManager->flush();

        return $this->redirectToRoute('list_categories');
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
            $this->addFlash('success', "Votre Theme a bien été ajouté.");
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

    #[Route('admin/theme/{id}/delete', name: 'delete_theme')]
    public function deleteTheme(Theme $theme, EntityManagerInterface $entityManager): Response
    {
        
        $entityManager->remove($theme);//suprime ce theme
        $entityManager->flush();
        $this->addFlash('warning', 'Votre thême a bien été supprimer');
        return $this->redirectToRoute('list_theme');
    }

}
