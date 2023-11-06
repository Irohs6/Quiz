<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin/pannel', name: 'app_admin_panel')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route(path: 'admin/panel/userManagement', name: 'app_userManagement')]
    public function userList(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
       
        return $this->render('admin/user_list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route(path: 'admin/panel/userManagementRole/{id}', name: 'app_userManagementRole')]
    public function moderatorRole(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setRoles(['ROLE_MODERATOR']);
        $entityManager->flush();

        return $this->redirectToRoute('app_userManagement');
    }


}
