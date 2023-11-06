<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
   
    public function index()
    {
        
    }

    #[Route('/user/profile/{id}/', name: 'user_profile')]
    public function showQuiz(User $user): Response
    {
      
        return $this->render('user/show_user_profile.html.twig');
    }
}
