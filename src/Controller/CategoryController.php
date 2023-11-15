<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\FileUploader;
use App\Repository\ThemeRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class CategoryController extends AbstractController
{
    //affiche la liste complete des catégorie**************************************************************
    #[Route('moderator/category/list', name: 'app_category')]
    public function liste_category(CategoryRepository $categoryRepository, ThemeRepository $themeRepository): Response
    {
        $themes = $themeRepository->findAll();
        $categories = $categoryRepository->findAll();
        return $this->render('category/list.html.twig', [
            'themes' => $themes,
            'categories' => $categories,
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
            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/new_edit_category.html.twig', [
            'category' => $category,
            'edit' => $category->getId(),
            'formNewCategory' => $formNewCategory,
            'theme' => $theme
        ]);
    }
}
