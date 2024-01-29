<?php

namespace App\Menu;

class MenuItem
{
    /** @var string */
    public $id;
    /** @var string */
    public $label;
    /** @var string|null */
    public $route;
    /** @var array|null */
    public $routeParams;
    /** @var string|null */
    public $url;
    /** @var array|null */
    public $options;
    /** @var MenuItem[]|null */
    public $subItems;

    /**
     * Make a item for the menu
     *
     * @param string $id id of menu item
     * @param string $label label of menu item
     * @param string|null $route route of menu item
     * @param array|null $routeParams route paramteres of the menu item
     * @param array|null $options otpions of item
     * @param MenuItem[]|null $subItems list of subItems
     */
    public function __construct($id, $label = null, $route=null, $routeParams = [], $options = [], $subItems = [])
    {
        $this->id=$id;
        $this->label = $label ?: $id;
        $this->route = $route;
        $this->routeParams = $routeParams;
        $this->options = $options;
        $this->subItems = $subItems;
    }
}
