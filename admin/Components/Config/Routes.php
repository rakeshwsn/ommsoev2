<?php
namespace Admin\Components\Config;
if(!isset($routes))
{
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin','filter' => 'login'], function($routes)
{
    $routes->add('components','Components\Controllers\Components::index');
    $routes->post('components/search','Components\Controllers\Components::search');
    $routes->add('components/autocomplete','Components\Controllers\Components::autocomplete');
    $routes->add('components/phase/(:num)','Components\Controllers\Components::phase/$1');

    $routes->add('components/add','Components\Controllers\Components::add');
    $routes->add('components/edit/(:num)','Components\Controllers\Components::edit/$1');
    $routes->add('components/delete/(:num)','Components\Controllers\Components::delete/$1');

    $routes->add('components/assign','Components\Controllers\Assign::index');
    $routes->add('components/assign/(:num)','Components\Controllers\Assign::index/$1');
    $routes->add('components/assign/add','Components\Controllers\Assign::add');
    $routes->post('components/assign/delete','Components\Controllers\Assign::delete');

    $routes->add('components/agencyassign','Components\Controllers\AgencyAssign::index');
    $routes->add('components/agencyassign/(:num)','Components\Controllers\AgencyAssign::index/$1');

   

});
