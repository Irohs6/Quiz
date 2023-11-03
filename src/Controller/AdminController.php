<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/pannel', name: 'app_admin_panel')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route(path: 'panel/admin/userManagement', name: 'app_userManagement')]
    public function userList(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
       
        return $this->render('admin/user_list.html.twig', [
            'users' => $users,
        ]);
    }
}
