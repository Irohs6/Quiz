<?php

namespace App\Security;
use GuzzleHttp\Client;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
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

    /** @var UserRepository */
    private $userRepository;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $recaptchaToken = $request->request->get('recaptcha-response');
        
        $client = new Client();
        $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
              'secret' => '6Lcml0IpAAAAAAv-02L2Tf_9YZIZRCkwaH3Rf0wb',
              'response' => $recaptchaToken,
            ]
        ]);
        $body = json_decode($response->getBody()->getContents(), true);
        if (!$body['success']) {
            throw new CustomUserMessageAuthenticationException('Invalid reCAPTCHA');
        }
        $userEntity = $this->userRepository->findOneBy(['email' => $email]);
        if ($userEntity) {
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
