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
use Symfony\Component\Filesystem\Filesystem;
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
    #[Route(path: 'admin/panel/addRoleModerator/{id}', name: 'app_addRoleModerator')]
    public function addRoleModerator(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setRoles(['ROLE_MODERATOR']);//change le role a moderateur

        $entityManager->flush();
        $this->addFlash('success', "Cet utilisateur est devenue un Modérateur.");
        return $this->redirectToRoute('app_userManagement');
    }

    //changement de role pour un utilisateur
    #[Route(path: 'admin/panel/removeRoleModerator/{id}', name: 'app_removeRoleModerator')]
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
        $user->setIsBanned(true);// met le boolean a vrais l'uttilisateur est alors banni et ne peux plus se connecter

        $entityManager->flush();

        $this->addFlash('success', "Cet utilisateur a bien été banni.");
        //redirige vers la liste des utilisateur de l'admin pannel
        return $this->redirectToRoute('app_userManagement');
    }

    //pour débanir un utilisateur********************************************
    #[Route(path: 'admin/panel/unBanned/{id}', name: 'app_userUnBanned')]
    public function unbannedUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setIsBanned(false); // met le bollean a faux l'uttilisateur n'est plus bannie il peux a nouveau se connecter

        $entityManager->flush();

        $this->addFlash('success', "Cet Utilisateur a bien été débannie.");
        //redirige vers la liste des utilisateur de l'admin pannel
        return $this->redirectToRoute('app_userManagement');
    }

    //liste de quiz pour le pannel admin
    #[Route('admin/list/quizzes', name: 'list_quizzes')]
    public function listQuizes(QuizRepository $quizRepository): Response
    {
        $quizzes = $quizRepository->findAll();// récupère tous les quiz enregistrer
    
        //envoit les données vers la vue
        return $this->render('admin/list_quizzes_admin.html.twig', [
            'quizzes' =>$quizzes
        ]);
    }
     
    //route pour supprimer un quiz
    #[Route('admin/quiz/{id}/delete', name: 'delete_quiz')]
    public function deleteQuiz(Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        foreach ($quiz->getQuestions() as $question) {//boucle pour récupérer les question d'un quiz
            $quiz->removeQuestion($question);// enlève l'association entre quiz et question et set a nul le quiz_id de question
        }

        $entityManager->remove($quiz);//suprime le quiz
        $entityManager->flush();
        $this->addFlash('success', "Quiz supprimé avec succès.");
        //redirige a la liste de quiz du pannel admin
        return $this->redirectToRoute('app_list_quiz');
    }

    //route pour la liste des catégorie du pannel admin
    #[Route('admin/list/categories', name: 'list_categories')]
    public function listCategory(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();// récupère toute les catégorie enregistré
        // retourne les données vers la vue 
        return $this->render('admin/list_categories_admin.html.twig', [
            'categories' =>$categories
        ]);
    }

    //Pour créer/modifier une  catégorie dans un thème définie *************************************************************************************************
    #[Route('admin/category/new/{idTheme}', name: 'new_category')]
    #[Route('moderator/category/edit/{id}', name: 'edit_category')]
    public function newEditCategory(Category $category = null,Request $request, ThemeRepository $themeRepository, EntityManagerInterface $entityManager,FileUploader $fileUploader): Response
    {
        //si la catégorie n'existe pas 
        if (!$category) {
            $category = new Category;// on créer une nouvelle instance de catégorie
            $idTheme = $request->attributes->get('idTheme');// on récupère l'id theme contenu dans l'url
            $theme = $themeRepository->findOneBy(['id' => $idTheme]); // on récupère l'entity theme garce a cet id
            $picture = null; // on set la variable a null si la catégorie n'existe pas
            $message = "Cet Categorie a été ajouter avec succès.";
        }else{
            //si la catégorie existe
            $theme = $category->getTheme(); //on récupère le theme contenu dans la catégorie
            // Récupérez le nom du fichier depuis l'entité
            $picture = $category->getPicture();//on récupère l'image de la catégorie
            $message = "Cet Categorie a bien été modifier";// message stocké 
        }
          
        $category->setTheme($theme);//on ajoute le thème a la catégorie
        $formNewCategory= $this->createForm(CategoryType::class, $category);//créer le formulaire

        $formNewCategory->handleRequest($request);
        
        //si le formulaire de Quiz est remplie et valide
        if ($formNewCategory->isSubmitted() && $formNewCategory->isValid()) {
            /** @var UploadedFile $pictureFile */
            // on récupère l'image contenu dans le formulaire
            $pictureFile = $formNewCategory->get('picture')->getData();
            //si l'image existe  et qu'elle n'a pas changé on garde la meme image
            if ($pictureFile && $picture != null && $pictureFile == $picture) {
                $category->setPicture($picture);
                // sinon si l'image existe mais qu'elle a changer on supprime l'ancienne du dossier upload et on stock la nouvelle
            }else if ($pictureFile && $picture != null && $pictureFile != $picture){
                // Récupère le chemin absolu vers le répertoire racine du projet Symfony
                $projectDir = $this->getParameter('kernel.project_dir');
            
                unlink($projectDir.'/public/uploads/img/category/'.$picture); // suprime l'image du dossier upload
                 //on utilise la fonction upload du service File upload qi va modifier le name avec un id unique
                $pictureFileName = $fileUploader->upload($pictureFile);
                // on set l'image modifier dans la catégorie
                $category->setPicture($pictureFileName);
                // sinon c'est une création donc on enregistre juste l'image
            }else{

                //on utilise la fonction upload du service File upload qi va modifier le name avec un id unique
                $pictureFileName = $fileUploader->upload($pictureFile);
                // on set l'image modifier dans la catégorie
                $category->setPicture($pictureFileName);
            }
               
            //récupère les donné du formulaire  
            $formNewCategory->getData();
            // prepare PDO(prepare la requete Insert ou Update)
            $entityManager->persist($category);
            // execute PDO(la requete Insert ou Update)
            $entityManager->flush();
            $this->addFlash('success', $message);
            //redirige vers la liste des catégorie du pannel admin
            return $this->redirectToRoute('list_categories');
        }

        return $this->render('category/new_edit_category.html.twig', [
            'category' => $category,
            'edit' => $category->getId(),
            'formNewCategory' => $formNewCategory,
            'theme' => $theme
        ]);
    }
      
    // route pour la suppression d'une catégorie et des question et des quiz qu'elle contient
    #[Route('admin/category/{id}/delete', name: 'delete_category')]
    public function deleteCategory(Category $category, EntityManagerInterface $entityManager): Response
    {
        
        $image = $category->getPicture();
        
        if (isset($image) ){
          
            $projectDir = $this->getParameter('kernel.project_dir');
            
            unlink($projectDir.'/public/uploads/img/category/'.$image); // suprime l'image du dossier upload
        } else {
            $image = null;
        }
        $entityManager->remove($category);//suprime cet category les quizs et les questions associer
        $entityManager->flush();
        $this->addFlash('success', 'Cet Categorie a été supprimer avec succes');
        return $this->redirectToRoute('list_categories');
    }

        // route pour crer modifier un thème
    #[Route('admin/create/theme', name: 'new_theme')]
    #[Route('admin/theme/edit/{id}', name: 'edit_theme')]
    public function createEditTheme(Theme $theme = null, EntityManagerInterface $entityManager, Request $request): Response
    {
        //si theme n'existe pas 
        if (!$theme) {
            $theme = new Theme;//on créer une nouvelle instance de theme
            $message = 'Thème ajouter avec sucès';
        }else{
            $message = 'Thème modifier avec sucès';
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
            $this->addFlash('success', $message);
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
        $themes = $themeRepository->findAll();// récupère tous les thèmes enregistrer en base
        // les retourne a la vue
        return $this->render('theme/list_theme.html.twig', [
            'themes' =>$themes
        ]);
    }

    // pour supprimer un thème, suprime les catégorie quiz question et réponse en cascade
    #[Route('admin/theme/{id}/delete', name: 'delete_theme')]
    public function deleteTheme(Theme $theme, EntityManagerInterface $entityManager): Response
    {
        
        $entityManager->remove($theme);//suprime ce theme
        $entityManager->flush();
        $this->addFlash('warning', 'Votre thême a bien été supprimer');
        return $this->redirectToRoute('list_theme');
    }

}
