<?php

namespace Admin\Physicalcomponents\Config;

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('physicalcomponents', 'Physicalcomponents\Controllers\Physicalcomponents::index');
    $routes->get('physicalcomponentsearch', 'Physicalcomponents\Controllers\Physicalcomponents::componentsearch');

    $routes->post('physicalcomponents/search', 'Physicalcomponents\Controllers\Physicalcomponents::search');
    $routes->match(['get', 'post'], 'physicalcomponents/add', 'Physicalcomponents\Controllers\Physicalcomponents::add');
    $routes->match(['get', 'post'], 'physicalcomponents/addform', 'Physicalcomponents\Controllers\Physicalcomponents::addform');
    $routes->match(['get', 'post'], 'physicalcomponents/edit/(:segment)', 'Physicalcomponents\Controllers\Physicalcomponents::edit/$1');
    $routes->get('physicalcomponents/delete/(:segment)',   'Physicalcomponents\Controllers\Physicalcomponents::delete/$1');
    $routes->post('physicalcomponents/delete', 'Physicalcomponents\Controllers\Physicalcomponents::delete');
  
});
