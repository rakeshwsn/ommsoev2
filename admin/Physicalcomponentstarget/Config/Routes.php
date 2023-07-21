<?php

namespace Admin\physicalcomponentstarget\Config;

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('physicalcomponentstarget', 'Physicalcomponentstarget\Controllers\Physicalcomponentstarget::index');
    $routes->match(['get', 'post'], 'physicalcomponentstarget/add', 'Physicalcomponentstarget\Controllers\Physicalcomponentstarget::add');
    $routes->match(['get', 'post'], 'Physicalcomponentstarget/edit/(:segment)', 'Physicalcomponentstarget\Controllers\Physicalcomponentstarget::edit/$1');
    $routes->get('physicalcomponents/delete/(:segment)',   'Physicalcomponents\Controllers\Physicalcomponents::delete/$1');
    $routes->post('physicalcomponents/delete', 'Physicalcomponents\Controllers\Physicalcomponents::delete');

});
