<?php

namespace App\Twig;


use App\Application;
use App\Parameter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use DateTime;
use Exception;

class Extension extends AbstractExtension
{
    /**
     * @var Application
     */
    public $application;

    /**
     * @var Parameter
     */
    public $parameter;


    /**
     * @param Application $application
     */
    public function __construct(Application $application, Parameter $parameter)
    {
        $this->application = $application;
        $this->parameter = $parameter;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('serverType', [$this, 'serverType'], ['is_safe' => ['html']]),
            new TwigFunction('version', [$this, 'version'], ['is_safe' => ['html']]),
            new TwigFunction('getParameter', [$this, 'getParameter'], ['is_safe' => ['html']]),
            new TwigFunction('isModeMaintenance', [$this, 'isModeMaintenance'],
                ['is_safe' => ['html']]),
            new TwigFunction('isPageInfo', [$this, 'isPageInfo'], ['is_safe' => ['html']]),
            new TwigFunction('getPageInfo', [$this, 'getPageInfo'], ['is_safe' => ['html']]),
            new TwigFunction('getPageInfoId', [$this, 'getPageInfoId'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters()
    {
        return [
            'shortDate' => new TwigFilter('shortDate', [$this, 'shortDate'], ['is_safe' => ['html']]),
            'shortDateTime' => new TwigFilter('shortDateTime', [$this, 'shortDateTime'], ['is_safe' => ['html']]),
            'mediumDateTime' => new TwigFilter('mediumDateTime', [$this, 'mediumDateTime'], ['is_safe' => ['html']]),
        ];
    }


    /**
     * @param DateTime|null $dateTime
     * @param String $format
     * @return string|null
     * @throws Exception
     */
    protected function formatDate($dateTime, $format)
    {
        if (!$dateTime) {
            return null;
        }
        if ($dateTime instanceof DateTime) {
            /** @var DateTime $dateTime */
            return $dateTime->format($format);
        } else {
            throw new \Exception('Not a datetime object');
        }
    }

    /**
     * @param DateTime|null $dateTime
     * @return string|null
     * @throws Exception
     */
    public function shortDate($dateTime)
    {
        return $this->formatDate($dateTime, Application::FORMAT_SHORT_DATE);
    }

    /**
     * @param DateTime|null $dateTime
     * @return string|null
     * @throws Exception
     */
    public function shortDateTime($dateTime)
    {
        return $this->formatDate($dateTime, Application::FORMAT_SHORT_DATETIME);
    }

    /**
     * @param DateTime|null $dateTime
     * @return string|null
     * @throws Exception
     */
    public function mediumDateTime($dateTime)
    {
        return $this->formatDate($dateTime, Application::FORMAT_MEDIUM_DATETIME);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function version()
    {
        return $this->application->getVersion();
    }

    /**
     * Retourne le contenu de PHPinfo
     *
     * @return string
     */
    public function serverType()
    {
        return $this->application->getServerType();
    }

    /**
     * @param $parameter
     * @return mixed
     */
    public function getParameter($parameter)
    {
        return $this->parameter->{$parameter};
    }


    /**
     * Indique si le site BO est en mode de mainteance
     *
     * @return bool
     */
    public function isModeMaintenance()
    {
        return ($this->parameter->modeMaintenance );
    }


    /**
     * Indique si un message doit être affiché
     *
     * @return bool
     */
    public function isPageInfo()
    {
        return ($this->parameter->pageInfo !== '' );
    }

    /**
     * Contenu du message pour le BO à afficher
     *
     * @return string
     */
    public function getPageInfo()
    {
        return $this->parameter->pageInfo;
    }


    /**
     * retourne l'ID du message pour le BO à afficher
     *
     * @return string
     */
    public function getPageInfoId()
    {
        return md5($this->parameter->pageInfo );
    }

}
