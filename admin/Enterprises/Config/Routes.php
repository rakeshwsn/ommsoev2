<?php

namespace Admin\Enterprises\Config;

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('enterpriseunit', 'Enterprises\Controllers\EnterpriseUnit::index');
    $routes->add('enterpriseunit/edit/(:num)', 'Enterprises\Controllers\EnterpriseUnit::edit/$1');
    $routes->add('enterpriseunit/add', 'Enterprises\Controllers\EnterpriseUnit::add');
    $routes->add('enterpriseunit/search', 'Enterprises\Controllers\EnterpriseUnit::search');
    $routes->add('enterpriseunit/delete/(:num)', 'Enterprises\Controllers\EnterpriseUnit::delete/$1');
    // enterprises
    $routes->add('enterprises/add', 'Enterprises\Controllers\Enterprises::add');
    $routes->add('enterprises', 'Enterprises\Controllers\Enterprises::index');
    $routes->add('enterprises/blocks', 'Enterprises\Controllers\Enterprises::ajaxblocks');
    $routes->add('enterprises/gps', 'Enterprises\Controllers\Enterprises::ajaxgps');
    $routes->add('enterprises/villages', 'Enterprises\Controllers\Enterprises::ajaxvillages');
    $routes->add('enterprises/edit', 'Enterprises\Controllers\Enterprises::edit');

    // $routes->add('enterprises', 'Enterprises\Controllers\Enterprises::enterprises');
    // $routes->get('ajax/blocks', 'Enterprises\Controllers\Enterprises::ajaxBlocks');

//Budget
$routes->add('budgetcode', 'Enterprises\Controllers\Budget::index');
$routes->add('budgetcode/edit/(:num)', 'Enterprises\Controllers\Budget::edit/$1');
$routes->add('budgetcode/add', 'Enterprises\Controllers\Budget::add');
$routes->add('budgetcode/search', 'Enterprises\Controllers\Budget::search');
$routes->add('budgetcode/delete/(:num)', 'Enterprises\Controllers\Budget::delete/$1');
});
