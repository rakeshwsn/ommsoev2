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
<<<<<<< HEAD
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
=======
    $routes->post('enterprises/search', 'Enterprises\Controllers\Enterprises::search',['permission'=>false]);
    // $routes->post('incentive/searchmain', 'Incentive\Controllers\Incentive::searchMain');
    // $routes->match(['get', 'post'], 'incentive/view/(:segment)', 'Incentive\Controllers\Incentive::view/$1');
    // $routes->match(['get', 'post'], 'incentive/add', 'Incentive\Controllers\Incentive::add');
    // $routes->match(['get', 'post'], 'incentive/addform', 'Incentive\Controllers\Incentive::addform');
    // $routes->match(['get', 'post'], 'incentive/edit/(:segment)', 'Incentive\Controllers\Incentive::edit/$1');
    // $routes->get('incentivemain/delete/(:segment)',   'Incentive\Controllers\Incentive::delete/$1');
    // $routes->post('incentive/delete', 'Incentive\Controllers\Incentive::delete');
    // $routes->add('incentive/incentivesearch', 'Incentive\Controllers\Incentive::incentivesearch');
    // $routes->post('incentive/searchall', 'Incentive\Controllers\Incentive::searchall');
>>>>>>> 526a4ae32e32441203cb5f267041a2fac3a63edd
});
