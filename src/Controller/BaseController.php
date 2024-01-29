<?php


namespace App\Controller;


use App\Menu\Menu;
use App\Menu\MenuTwig;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    /**
     * @var MenuTwig
     */
    public $menuTwig;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(Menu $menu, LoggerInterface $faoLogger)
    {
        $this->menuTwig = $menu->menuTwig;
        $this->logger = $faoLogger;
    }

    public function redirectAccessInterdit()
    {
        $this->logger->info('Accès interdit !!!');
        $content = $this->renderView('accesDenied.html.twig');
        return new Response($content, 403);
    }


    /**
     * Write a debug info in logger
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function logDebug($message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    /**
     * Write a info in logger
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function logInfo($message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }


    /**
     * Write a error info in logger
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function logError($message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    /**
     * Write a warning info in logger
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function logWarning($message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

}
