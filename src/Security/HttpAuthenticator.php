<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class HttpAuthenticator extends AbstractAuthenticator
{

    protected function getLoginHeader()
    {
        $login = null;
        if (isset($_SERVER['HTTP_GINAUSER'])) {
            $login = mb_strtoupper($_SERVER['HTTP_GINAUSER']);
        } elseif (isset($_SERVER['REMOTE_USER'])) {
            $login = mb_strtoupper($_SERVER['REMOTE_USER']);
        } elseif (isset($_SERVER['HTTP_REMOTE_USER'])) {
            $login = mb_strtoupper($_SERVER['HTTP_REMOTE_USER']);
        }

        return $login;
    }

    public function supports(Request $request): ?bool
    {
        return filter_var($_ENV['AUTH_WITH_GINA_HEADER'] ?? false, FILTER_VALIDATE_BOOLEAN) &&
            $this->getLoginHeader() !== null;
    }

    public function authenticate(Request $request): Passport
    {
        $login = $this->getLoginHeader();
        if (null === $login) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        return new SelfValidatingPassport(
            new UserBadge($login, function ($login) {
                $user = new User();
                $user->setHttpAttributes(
                    [
                        User::USERNAME => [$login],
                        User::EMAIL => [$_SERVER['HTTP_GINAEMAIL'] ?? null],
                        User::NAME => [$_SERVER['HTTP_GINANAME'] ?? null],
                        User::FIRSTNAME => [$_SERVER['HTTP_GINAFIRSTNAME'] ?? null],
                        User::FULLNAME => [$_SERVER['HTTP_GINAFULLNAME'] ?? null],
                        User::INITIALS => [$_SERVER['HTTP_GINAINITIALS'] ?? null],
                        User::OU => [$_SERVER['HTTP_GINAOU'] ?? null],
                        User::PHONE => [$_SERVER['HTTP_GINAPHONE'] ?? null],
                        User::TITLE => [$_SERVER['HTTP_GINATITLE'] ?? null],
                        User::ROLES => explode('|', mb_strtoupper($_SERVER['HTTP_GINAROLES'] ?? '')),
                    ]
                );

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}