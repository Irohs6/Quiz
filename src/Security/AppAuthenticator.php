<?php

namespace App\Security;

use ReCaptcha\ReCaptcha;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    private $userRepository;
    private $recaptcha;
    

    public const LOGIN_ROUTE = 'app_login';


    public function __construct(private UrlGeneratorInterface $urlGenerator, UserRepository $userRepository, ReCaptcha $recaptcha)
    {
        $this->userRepository = $userRepository;
        $this->recaptcha = $recaptcha;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $recaptchaResponse = $request->request->get('g-recaptcha-response');
        dd($recaptchaResponse);
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        // On récupère le user grace a son email
        $userEntity = $this->userRepository->findOneBy(['email' => $email]);

        if ($userEntity) {
            
        
            // Et on Vérifiez si l'eamil est vérifié ou si le user est  banni
            if (!$userEntity->isVerified() || $userEntity->isIsBanned()) {
                $errorMessage = $userEntity && $userEntity->isIsBanned()
                    ? 'Votre compte est banni.'
                    : 'Votre adresse e-mail n\'est pas vérifiée.';

                throw new CustomUserMessageAuthenticationException($errorMessage);
            }

        }else{
            $errorMessage = "Cet email n'existe pas Veuillez créer un compte";
                
            throw new CustomUserMessageAuthenticationException($errorMessage);
        }

        $recaptchaResult = $this->recaptcha->verify($recaptchaResponse, $request->getClientIp());

        if (!$recaptchaResult->isSuccess() || $recaptchaResult->getScore() < 0.5) {
            throw new CustomUserMessageAuthenticationException('ReCAPTCHA non valide.');
        }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_home_quiz'));
    }


    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
