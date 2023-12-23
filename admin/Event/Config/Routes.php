<?php
namespace Admin\Event\Config;

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('event', 'Event\Controllers\Event::index');
    $routes->post('event/search', 'Event\Controllers\Event::search', ['permission' => false]);
    $routes->match(['get', 'post'], 'event/add', 'Event\Controllers\Event::add');
    $routes->match(['get', 'post'], 'event/edit/(:segment)', 'Event\Controllers\Event::edit/$1');
    $routes->get('event/delete/(:segment)', 'Event\Controllers\Event::delete/$1');
    $routes->post('event/delete', 'Event\Controllers\Event::delete');
});
