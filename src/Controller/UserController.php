<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
   
    #[Route('/user/profile/{id}/', name: 'user_profile')]
    public function showProfile(User $user): Response
    {
      
        return $this->render('user/show_user_profile.html.twig');
    }

    #[Route('/user/profile/edit/{id}/', name: 'edit_profile')]
    public function editUser(User $user,Request $request,EntityManagerInterface $entityManager): Response
    {
        $formEditUser= $this->createForm(UserType::class, $user);//crer le formulaire

        $formEditUser->handleRequest($request);
       
        //si le formulaire de Quiz est remplie et valide
        if ($formEditUser->isSubmitted() && $formEditUser->isValid()) {
            //récupère les donné du formulaire  
            $formEditUser->getData();
            // prepare PDO(prepare la requete Insert ou Update)
            $entityManager->persist($user);
            // execute PDO(la requete Insert ou Update)
            $entityManager->flush();
            //redirige ajout de question et réponse
            return $this->redirectToRoute('user_profile',['id' => $user->getId()]);
        }

        return $this->render('user/edit_user_profile.html.twig', [
          
            'formEditUser' => $formEditUser,
           
        ]);


        return $this->render('user/show_user_profile.html.twig');
    }
}
