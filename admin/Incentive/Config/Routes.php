<?php
namespace Admin\Incentive\Config;
if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin','filter' => 'login'], function($routes)
{
    $routes->add('incentive', 'Incentive\Controllers\Incentive::index');
    $routes->post('incentive/search','Incentive\Controllers\Incentive::search');
    $routes->match(['get','post'],'incentive/add', 'Incentive\Controllers\Incentive::add');
    $routes->match(['get','post'],'incentive/edit/(:segment)', 'Incentive\Controllers\Incentive::edit/$1');
    $routes->get('incentive/delete/(:segment)',   'Incentive\Controllers\Incentive::delete/$1');
    $routes->post('incentive/delete','Incentive\Controllers\Incentive::delete');

});