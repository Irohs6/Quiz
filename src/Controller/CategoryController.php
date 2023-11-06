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
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository, ThemeRepository $themeRepository): Response
    {
        $themes = $themeRepository->findAll();
        $categories = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'themes' => $themes,
            'categories' => $categories,
        ]);
    }

    #[Route('/category/new/{idTheme}', name: 'new_category')]
    #[Route('/category/edit/{id}', name: 'edit_category')]
    public function newEditCategory(Category $category = null,Request $request, ThemeRepository $themeRepository, EntityManagerInterface $entityManager,FileUploader $fileUploader): Response
    {
        if (!$category) {
            $category = new Category;
            $idTheme = $request->attributes->get('idTheme');
            $theme = $themeRepository->findOneBy(['id' => $idTheme]);
        }else{
            $theme = $category->getTheme();
            // Récupérez le nom du fichier depuis l'entité
            if ($category->getPicture()) {
                $category->setPicture(
                    new File($this->getParameter('pictures_directory').'/'.$category->getPicture()));
            }else{
                $category->setPicture(null);
            }
        }
        $category->setTheme($theme);
        $formNewCategory= $this->createForm(CategoryType::class, $category);//crer le formulaire
        $formNewCategory->handleRequest($request);
       
        //si le formulaire de Quiz est remplie et valide
        if ($formNewCategory->isSubmitted() && $formNewCategory->isValid()) {
            /** @var UploadedFile $pictureFile */
        $pictureFile = $formNewCategory->get('picture')->getData();
        
        if ($pictureFile) {
            $pictureFileName = $fileUploader->upload($pictureFile);
            $category->setPicture($pictureFileName);
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
