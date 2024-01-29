<?php

namespace App\Listener;

use App\Application;
use App\Parameter;
use App\Security\Role;
use mysql_xdevapi\Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Twig\Environment;


class KernelRequestListener
{
    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /**
     * @var Parameter
     */
    protected $parameter;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var Security
     */
    protected $security;
    /**
     * @var Environment
     */
    protected $twig;


    /**
     * @param Parameter $parameter
     * @param Application $application
     * @param Security $security
     * @param Environment $twig
     */
    public function __construct(
        Parameter $parameter,
        Application $application,
        Security $security,
        Environment $twig,
    ) {
        $this->parameter = $parameter;
        $this->application = $application;
        $this->security = $security;
        $this->twig = $twig;
    }

    #[AsEventListener()]
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        // test le mode maintenance doit être affiché
        if ($this->parameter->modeMaintenance) {
            if ($request->attributes->get('_route') != 'saml_login') {
                if (!$this->security->isGranted(Role::ADMINISTRATEUR)) {
                    throw  new  ServiceUnavailableHttpException();
                }
            }
        }
    }
}
