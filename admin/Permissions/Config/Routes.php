<?php
if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}
$routes->group('admin', ['namespace' => 'Admin','filter' => 'login'], function($routes)
{
    $routes->add('permissions', 'Permissions\Controllers\Permissions::index');
    $routes->post('permissions/search','Permissions\Controllers\Permissions::search');
    $routes->match(['get','post'],'permissions/add', 'Permissions\Controllers\Permissions::add');
    $routes->match(['get','post'],'permissions/edit/(:segment)', 'Permissions\Controllers\Permissions::edit/$1');
    $routes->get('permissions/delete/(:segment)',   'Permissions\Controllers\Permissions::delete/$1');
    $routes->post('permissions/delete','Permissions\Controllers\Permissions::delete');
    $routes->add('permissions/assign/(:segment)',   'Permissions\Controllers\Permissions::assign/$1');
    
});
    