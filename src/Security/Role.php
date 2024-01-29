<?php

namespace App\Security;

use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

/**
 * Défintion des rôles Gina pour l'application
 */
class Role
{
    public const ADMINISTRATEUR = 'ROLE_ADMIN';   // hérite du rôle ADMIN_SITE et UTILISATEUR
    public const ADMIN_SITE = 'ROLE_ADMIN-SITE';  // hérite du rôle UTILISATEUR
    public const UTILISATEUR = 'ROLE_UTILISATEUR';

    public const ALL = 'ROLE_USER';                // all user of frontend, backend and anonymous have this role

    public static function getRoles()
    {
        $oClass = new \ReflectionClass(__CLASS__);

        return $oClass->getConstants();
    }

}
