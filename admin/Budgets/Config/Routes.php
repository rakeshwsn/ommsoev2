<?php
namespace Admin\Budgets\Config;
if(!isset($routes))
{
    $routes = \Config\Services::routes(true);
}

$routes->group(env('app.adminRoute'), ['namespace' => 'Admin','filter' => 'login'], function($routes)
{
    $routes->add('budgets','Budgets\Controllers\Budgets::index');
    $routes->post('budgets/search','Budgets\Controllers\Budgets::search');
    $routes->add('budgets/add','Budgets\Controllers\Budgets::add');
    $routes->add('budgets/edit/(:num)','Budgets\Controllers\Budgets::edit/$1');
    $routes->add('budgets/delete/(:num)','Budgets\Controllers\Budgets::delete/$1');

    $routes->add('budgets/tupdate','Budgets\Controllers\Budgets::updateTransaction');

    $routes->add('budgets/details/(:num)','Budgets\Controllers\Budgets::details/$1');
    $routes->add('budgets/view/(:num)','Budgets\Controllers\Budgets::view/$1');
    $routes->get('reports/mpr1','Reports\Controllers\Mpr3::index');

});
