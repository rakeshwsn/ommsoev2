<?php
namespace Admin\Menu\Config;
if(!isset($routes))
{
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin','filter' => 'login'], function($routes)
{
    $routes->match(['get','post'],'menu', 'Menu\Controllers\Menu::index');
    $routes->match(['get','post'],'menu/(:num)', 'Menu\Controllers\Menu::index/$1');
    $routes->match(['get','post'],'menu/add', 'Menu\Controllers\Menu::add');
    $routes->match(['get','post'],'menu/edit/(:segment)', 'Menu\Controllers\Menu::edit/$1');
    $routes->post('menu/deleteMenuItem',   'Menu\Controllers\Menu::deleteMenuItem');
    $routes->get('menu/delete/(:segment)',   'Menu\Controllers\Menu::delete/$1');
});
