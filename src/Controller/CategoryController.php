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

  
}
