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
    //route vers le panneaux admin
    #[Route('/admin/pannel', name: 'app_admin_panel')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

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

        return $this->redirectToRoute('app_userManagement');
    }

    //Pour bannir un utilisateur
    #[Route(path: 'admin/panel/isBanned/{id}', name: 'app_userIsBanned')]
    public function bannedUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setIsBanned(true);

        $entityManager->flush();

        return $this->redirectToRoute('app_userManagement');
    }

    //pour débanir un utilisateur********************************************
    #[Route(path: 'admin/panel/unBanned/{id}', name: 'app_userUnBanned')]
    public function unbannedUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setIsBanned(false);
        $entityManager->flush();

        return $this->redirectToRoute('app_userManagement');
    }


}
