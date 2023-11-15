<?php

namespace App\Controller;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();// créer une nouvelle instance de User
        $form = $this->createForm(RegistrationFormType::class, $user);//créer le fomrmulaire
        $form->handleRequest($request);
        //VerifAdmin000@ 
        //AdminVerifMdp000@
        if ($form->isSubmitted() && $form->isValid()) {
            $imageName =  $request->request->all('formEditUser')['selectedProfileImage']; //recupère l'image de profil selectioné
            $user->setProfileImage($imageName);//ajoute l'image au user
            $data = $form->getData();
            $user->setIsBanned(false);//met le statut banni a false par défault
            $user->setRoles(['ROLE_USER']);//met le role user par default
            $email= $data->getEmail();
            if ($email === 'adminMail@quiz-quest.com') {
                $user->setRoles(['ROLE_ADMIN']);//met le role admin si l'adresse mail 'adminMail@quiz-quest.com' est par default
            }else{
                $user->setRoles(['ROLE_USER']);//met le role user par default
            }
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('admin@quiz-quest.com', 'Admin Quiz Quest'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email/', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request,UserRepository $userRepository, TranslatorInterface $translator): Response
    {
          
         $id = $request->get('id'); // retrieve the user id from the url
        
        // Verify the user id exists and is not null
        if (null === $id) {
            return $this->redirectToRoute('app_quiz');
        }
        $user = $userRepository->findOneBy(['id' =>$id]);
        // Ensure the user exists in persistence
        if (null === $user) { 
                  
            return $this->redirectToRoute('app_quiz');
        }
        
      
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre email a bien été vérifier.');

        return $this->redirectToRoute('app_login');
    }
}

