<?php

namespace App\Listener;

use App\Application;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Twig\Environment;

class ExceptionListener
{

    protected $twig;
    protected $application;

    public function __construct(Environment $twig, Application $application)
    {
        $this->twig = $twig;
        $this->application = $application;
    }

    #[AsEventListener()]
    public function onKernelException(ExceptionEvent $event)
    {
        if ($this->application->getEnvironment() == 'prod' && !$this->application->isDebug()) {
            if (is_a($event->getThrowable(), NotFoundHttpException::class)) {
                $view = $this->twig->render('bundles/TwigBundle/Exception/error404.html.twig');
                $reponse = new Response($view, 404);
            } elseif (is_a($event->getThrowable(), AccessDeniedHttpException::class)) {
                $view = $this->twig->render('bundles/TwigBundle/Exception/error403.html.twig');
                $reponse = new Response($view, 403);
            } elseif (is_a($event->getThrowable(), ServiceUnavailableHttpException::class)) {
                $view = $this->twig->render('bundles/TwigBundle/Exception/error503.html.twig');
                $reponse = new Response($view, 503);
            } else {
                $view = $this->twig->render('bundles/TwigBundle/Exception/error500.html.twig');
                $reponse = new Response($view, 500);
            }
            $event->setResponse($reponse);
        }
    }
}
