<?php
namespace Admin\Equipment\Config;

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('equipment', 'Equipment\Controllers\Equipment::index');
    $routes->post('equipment/search', 'Equipment\Controllers\Equipment::search', ['permission' => false]);
    $routes->match(['get', 'post'], 'equipment/add', 'Equipment\Controllers\Equipment::add');
    $routes->match(['get', 'post'], 'equipment/edit/(:segment)', 'Equipment\Controllers\Equipment::edit/$1');
    $routes->get('equipment/delete/(:segment)', 'Equipment\Controllers\Equipment::delete/$1');
    $routes->post('equipment/delete', 'Equipment\Controllers\Equipment::delete');
});
