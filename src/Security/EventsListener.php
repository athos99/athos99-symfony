<?php

namespace App\Security;

use App\Application;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Http\Event\AuthenticationTokenCreatedEvent;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;
use Symfony\Component\Security\Http\Event\TokenDeauthenticatedEvent;

class EventsListener
{
    public const SAML_ROLE_PREFIX = "APP.EDG.";

    protected Application $application;
    protected LoggerInterface $logger;

    public function __construct(Application $application, LoggerInterface $applicationLogger)
    {
        $this->application = $application;
        $this->logger = $applicationLogger;
    }

    #[AsEventListener()]
    public function responseEvent(ResponseEvent $event): void
    {
        // add security header
        $response = $event->getResponse();
        if ($this->application->isServerLocal()) {
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'none'; ".
                "script-src 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval';".
                "script-src-elem 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval';".
                "script-src-attr 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval';".
                "style-src 'self' 'unsafe-inline';".
                "connect-src 'self'  *.etat-ge.ch ge.ch *.ge.ch;".
                "font-src 'self';".
                "media-src 'self';".
                "form-action 'self' *.etat-ge.ch ge.ch *.ge.ch *.geneveid.ch *.localhost;".
                "img-src 'self' data: ge.ch *.ge.ch *.etat-ge.ch ;"
            );
        } else {
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'none'; ".
                "script-src 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval';".
                "script-src-elem 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval';".
                "script-src-attr 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval';".
                "style-src 'self' 'unsafe-inline';".
                "connect-src 'self'  *.etat-ge.ch ge.ch *.ge.ch;".
                "font-src 'self';".
                "media-src 'self';".
                "form-action 'self' *.etat-ge.ch ge.ch *.ge.ch *.geneveid.ch;".
                "img-src 'self' data: ge.ch *.ge.ch *.etat-ge.ch ;"
            );
        }
        $response->headers->set('Cache-Control', "max-age=0, must-revalidate, no-cache, no-store, private");
        $response->headers->set('Expires', "0");
        if ($this->application->isServerLocal()) {
            // local server (no proxy), add some header for simulate the proxy
            $response->headers->set('X-Frame-Options', "SAMEORIGIN");
        }
        $response->headers->set('Referrer-Policy', "strict-origin");
        $response->headers->set('X-Content-Type-Options', "nosniff");
        $response->headers->set('Referrer-Policy', "strict-origin");
        $response->headers->set('X-XSS-Protection', "1; mode=block");
    }


    #[AsEventListener()]
    public function loginSuccessEvent(LoginSuccessEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();
        $this->logger->info('Success login', $user->getAttributes());
    }


    #[AsEventListener()]
    public function loginFailureEvent(LoginFailureEvent $event): void
    {
        /** @var User $user */
        $user = $event->getPassport()?->getUser();
        $this->logger->info('Login failure', $user?->getAttributes());
    }


    #[AsEventListener()]
    public function checkPassportEvent(CheckPassportEvent $event): void
    {
        /**
         * C'est possible ici de déterminer les rôles par d'autre myoens (gestion des rôles par l'appli et non gina....)
         */

        /** @var User $user */
        $user = $event->getPassport()->getUser();
        // guarantee every user at least has ROLE_USER
        $roles = ['ROLE_USER' => 'ROLE_USER'];
        $ginaRoles = $user->getGinaRoles() ?? [];
        $lenPrefixSamlRole = mb_strlen(self::SAML_ROLE_PREFIX);
        foreach ($ginaRoles as $role) {
            $role = mb_strtoupper($role);
            if (!strncmp($role, self::SAML_ROLE_PREFIX, $lenPrefixSamlRole)) {
                $name = 'ROLE_'.mb_substr($role, $lenPrefixSamlRole);
                $roles[$name] = $name;
            }
        }
        $user->setRoles($roles);
    }


//    #[AsEventListener()]
//    public function authenticationTokenCreatedEvent(AuthenticationTokenCreatedEvent $event): void
//    {
//        $authenticationTokenCreatedEvent = $event;
//    }
//
//    #[AsEventListener()]
//    public function authenticationSuccessEvent(AuthenticationSuccessEvent $event): void
//    {
//        $authenticationSuccessEvent = $event;
//    }
//
//
//    #[AsEventListener()]
//    public function logoutEvent(LogoutEvent $event): void
//    {
//        $logoutEvent = $event;
//    }
//
//    #[AsEventListener()]
//    public function tokenDeauthenticatedEvent(TokenDeauthenticatedEvent $event): void
//    {
//        $tokenDeauthenticatedEvent = $event;
//    }
//
//
//    #[AsEventListener()]
//    public function switchUserEvent(SwitchUserEvent $event): void
//    {
//        $switchUserEvent = $event;
//    }

}