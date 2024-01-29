<?php

namespace App;

use Symfony\Component\Security\Core\Security;

class Application
{

    public const FORMAT_SHORT_DATE = 'd.m.Y';
    public const FORMAT_SHORT_DATETIME = 'd.m.Y - H:i';
    public const FORMAT_SHORT_TIME = 'H:i:s';
    public const FORMAT_MEDIUM_DATETIME = 'd.m.Y - H:i:s';

    public const FORMAT_ICU_SHORT_DATE = 'dd.MM.y';
    public const FORMAT_ICU_SHORT_DATETIME = 'dd.MM.y HH:mm';
    public const FORMAT_ICU_SHORT_TIME = 'HH:mm:ss';
    public const FORMAT_ICU_MEDIUM_DATETIME = 'dd.MM.y HH:mm:ss';

    /**
     * @var Security
     */
    protected $security;

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @param Security $security
     * @param Kernel $kernel
     */
    public function __construct(Security $security, Kernel $kernel)
    {
        $this->security = $security;
        $this->kernel = $kernel;
    }

    /**
     * Return type of server  (it's not symfony environment, don't confuse it)
     * @return string   values: 'prod', 'rec','dev' ou 'local'
     */
    public function getServerType()
    {
        return mb_strtolower($_ENV['SERVER_TYPE'] ?? 'prod');
    }

    /**
     * Indication if the application run on a local PC
     * @return bool
     */
    public function isServerLocal()
    {
        return 'local' == $this->getServerType();
    }

    /**
     * Return the symfony current environment
     *
     * @return string   values: 'prod', 'dev'
     */
    public function getEnvironment()
    {
        return $this->kernel->getEnvironment();
    }


    /**
     * Return indication of  the symfony debug mode
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->kernel->isDebug();
    }




    function getVersion()
    {
        $propertiesFile = __DIR__.'/../release.properties';
        $propertiesContent = parse_ini_file($propertiesFile);
        if (!isset($propertiesContent['version'])) {
            throw new \Exception("Proriete 'version' manquante dans le fichier release.properties");
        }

        return $propertiesContent['version'];
    }


    /**
     * Indication if a user is internal user of State of Geneva
     *
     *  the rules:
     *   check the server var :HTTP_RESEAU
     *
     * In other case check the ip address
     *   !^10\.5\. -> external
     * ^10\.  -> internal
     * ^160\.53\. -> internal
     * -> external
     *
     * @return bool
     */
    public static function isInternalUser()
    {
        if (isset($_SERVER["HTTP_RESEAU"])) {
            return ('INTERNE' == $_SERVER["HTTP_RESEAU"]);
        }
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = explode('.', $_SERVER["HTTP_X_FORWARDED_FOR"]);
            if (10 == $ip[0] ?? '') {
                if (5 != $ip[1] ?? '') {
                    return true;
                }
            } elseif ((160 == $ip[0] ?? '') && (53 == $ip[1] ?? '')) {
                return true;
            }
        }

        return false;
    }


    public function logoff() {
        $this->kernel->getContainer()->get('security.token_storage')->setToken(null);
        $session=$this->kernel->getContainer()->get('request_stack')->getSession();
        $session->clear();
    }
}
