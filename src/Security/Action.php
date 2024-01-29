<?php

namespace App\Security;


/**
 * Cette classe definit toutes les actions
 * En fonctions du role de l'utilisateur, ces actions sont autorisées ou interdites (voire classe ActionVoter)
 */
class Action
{

    /**
     * Liste des actions
     */

    public const CONSULTER_ADMIN = 'consulter_admin';

    public const NAVBAR_ENVIRONNEMENT = 'navbar_environnement';

    /**
     *  fonctionalité-actions
     */
    public const HOME = 'home';
    public const BACKEND = 'backend';
    public const FRONTEND = 'frontend';
    public const DEBUG = 'debug';
    public const DEBUG_DB = 'debug_db';
    public const DEBUG_LOG = 'debug_log';
    public const DEBUG_JOB = 'debug_job';
    public const DEBUG_DIAGNOSTIQUE = "debug_diagnostique";
    public const SONDE_DIAGNOSTIQUE = "sonde_diagnostique";
    public const SETTINGS = 'settings';

    // Parameter
    public const PARAMETRE_READ ='parametre_read';
    public const PARAMETRE_WRITE ='parametre_write';



    /**
     * Fonction statique
     * Retourne la liste des actions défines, dans cette classe, par des constantes
     * @return array
     */
    public static function getActions()
    {
        $reflectionClass = new \ReflectionClass(self::class);

        return $reflectionClass->getConstants();
    }
}
