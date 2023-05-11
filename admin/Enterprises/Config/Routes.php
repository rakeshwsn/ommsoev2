<?php

namespace Admin\Enterprises\Config;

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('enterprises', 'Enterprises\Controllers\Enterprises::index');
    $routes->post('enterprises/search', 'Enterprises\Controllers\Enterprises::search');
    // $routes->post('incentive/searchmain', 'Incentive\Controllers\Incentive::searchMain');
    // $routes->match(['get', 'post'], 'incentive/view/(:segment)', 'Incentive\Controllers\Incentive::view/$1');
    // $routes->match(['get', 'post'], 'incentive/add', 'Incentive\Controllers\Incentive::add');
    // $routes->match(['get', 'post'], 'incentive/addform', 'Incentive\Controllers\Incentive::addform');
    // $routes->match(['get', 'post'], 'incentive/edit/(:segment)', 'Incentive\Controllers\Incentive::edit/$1');
    // $routes->get('incentivemain/delete/(:segment)',   'Incentive\Controllers\Incentive::delete/$1');
    // $routes->post('incentive/delete', 'Incentive\Controllers\Incentive::delete');
    // $routes->add('incentive/incentivesearch', 'Incentive\Controllers\Incentive::incentivesearch');
    // $routes->post('incentive/searchall', 'Incentive\Controllers\Incentive::searchall');
});
