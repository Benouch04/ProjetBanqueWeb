<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UsersAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;
    
    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('username', '');

        $request->getSession()->set(Security::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
{
    // Récupérer l'utilisateur courant et ses rôles
    $user = $token->getUser();
    $roles = $user->getRoles();

    // Déterminer le tableau de bord de redirection basé sur les rôles de l'utilisateur
    if (in_array('ROLE_ADMIN', $roles)) {
        $redirectRoute = 'app_admin'; // Remplacez 'admin_dashboard' par la route de votre tableau de bord admin
    } elseif (in_array('ROLE_CONSEILLER', $roles)) {
        $redirectRoute = 'app_conseiller'; // Remplacez 'conseiller_dashboard' par la route de votre tableau de bord conseiller
    } elseif (in_array('ROLE_AGENT', $roles)) {
        $redirectRoute = 'app_agent'; // Remplacez 'agent_dashboard' par la route de votre tableau de bord agent
    } elseif (in_array('ROLE_DIRECTEUR', $roles)) {
        $redirectRoute = 'app_directeur'; // Remplacez 'directeur_dashboard' par la route de votre tableau de bord directeur
    } else {
        // Si l'utilisateur n'a aucun des rôles ci-dessus ou un rôle par défaut, redirigez vers une page par défaut
        $redirectRoute = 'main'; // Remplacez 'default_route' par la route de la page d'accueil ou une autre page par défaut
    }

    // Rediriger vers le tableau de bord approprié
    return new RedirectResponse($this->urlGenerator->generate($redirectRoute));
}

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
