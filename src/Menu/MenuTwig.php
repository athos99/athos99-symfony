<?php

namespace App\Menu;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MenuTwig extends AbstractExtension
{
    /**
     * @var MenuItem[][][]
     */
    public $menu = [];

    /**
     * @var UrlGeneratorInterface $urlGenerator
     */
    public $urlGenerator;

    /**
     * @var Request|null
     */
    public $request;


    /**
     * @var string, nom de l'url
     */
    public $activeUri;

    /**
     * Menu constructor.
     *
     * @param RouterInterface $router
     * @param RequestStack $requestStack
     */
    public function __construct(RouterInterface $router, RequestStack $requestStack)
    {
        /** @var Router $router */
        $this->urlGenerator = $router->getGenerator();

        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('getMenuItems', [$this, 'getMenuItems']),
            new TwigFunction('getMenuSubItems', [$this, 'getMenuSubItems']),
            new TwigFunction('getMenuOptions', [$this, 'getMenuOptions']),
            new TwigFunction('isMenuItemActiv', [$this, 'isMenuItemActiv']),
            new TwigFunction('isUrlMenuItemActiv', [$this, 'isUrlMenuItemActiv']),
            new TwigFunction('addMenuItem', [$this, 'addItem']),
            new TwigFunction('newMenuItem', [$this, 'newItem']),
        ];
    }

    /**
     * @param string $id
     * @param string $label
     * @param string $route
     * @param array $routeParams
     * @param array $options
     * @param array $subItems
     * @return MenuItem
     */
    public function newItem($id, $label, $route, $routeParams = [], $options = [], $subItems = [])
    {
        return new MenuItem($id, $label, $route, $routeParams, $options, $subItems);
    }


    /**
     * @param string $menuName
     * @param MenuItem $item
     * @return $this
     */
    public function addItem($menuName, MenuItem $item)
    {
        $item->url = $this->getUrl($item);

        if (property_exists($item, 'subItems') && $item->subItems) {
            foreach ($item->subItems as $subItem) {
                $subItem->url = $this->getUrl($subItem);
            }
        }
        $this->menu[$menuName]['items'][$item->label] = $item;

        return $this;
    }


    /**
     * @param string $menuName
     * @param string $label
     * @return MenuItem|false
     */
    public function getItem($menuName, $label)
    {
        return $this->menu[$menuName]['items'][$label] ?? false;
    }


    /**
     * Get the URL of menu item
     *
     * @param MenuItem $item
     * @return string
     */
    public function getUrl(MenuItem $item)
    {
        if ($item->route) {
            return $this->urlGenerator->generate($item->route, $item->routeParams ?? []);
        }

        return $item->options['url'] ?? null;
    }


    /**
     * @param MenuItem $item
     * @return bool
     */
    public function isMenuItemActiv(MenuItem $item)
    {
        $activRoute = $this->request->get('_route');
        if ($item->route && $activRoute == $item->route) {
            return true;
        }
        if ($routes = $item->options['activRoutes'] ?? false) {
            foreach ($routes as $route) {
                if ($activRoute == $route) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * @param MenuItem $item
     * @return bool
     */
    public function isUrlMenuItemActiv(MenuItem $item)
    {
        return ($item->url == $this->request->getRequestUri());
    }

    /**
     * @param string $nom
     * @return MenuItem[]
     */
    public function getMenuItems($nom)
    {
        return $this->menu[$nom]['items'] ?? [];
    }

    /**
     * @param MenuItem $item
     * @return array|null
     */
    public function getMenuSubItems(MenuItem $item)
    {
        return $item->subItems ?? [];
    }

    /**
     * @param string $nom
     * @return array
     */
    public function getMenuOptions($nom)
    {
        return $this->menu[$nom]['options'] ?? [];
    }

}
