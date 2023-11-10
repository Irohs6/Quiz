<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Form\ThemeType;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ThemeController extends AbstractController
{
    #[Route('moderator/create/theme', name: 'new_theme')]
    #[Route('moderator/theme/edit/{id}', name: 'edit_theme')]
    public function createEditTheme(Theme $theme = null, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$theme) {
            $theme = new Theme;
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
            //redirige ajout de question et réponse
            return $this->redirectToRoute('list_theme');
        }

        return $this->render('theme/new_edit_theme.html.twig', [
            'formNewTheme' => $formNewTheme,
            'edit' => $theme->getId(),
            'theme' =>$theme,
        ]);
    }


    #[Route('moderator/list/theme/create', name: 'list_theme')]
    public function listTheme(ThemeRepository $themeRepository): Response
    {
        $themes = $themeRepository->findAll();
       
        return $this->render('theme/list_theme.html.twig', [
            'themes' =>$themes
        ]);
    }

    
}
