<?php
namespace Admin\Users\Config;
if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin','filter' => 'login'], function($routes)
{
    //added by rakeshnayak
    $routes->add('letters/user', 'Letters\Controllers\Users::index');
    $routes->post('letters/user/search','Letters\Controllers\Users::search');
    $routes->match(['get','post'],'letters/user/add', 'Letters\Controllers\Users::add');
    $routes->match(['get','post'],'letters/user/edit/(:segment)', 'Letters\Controllers\Users::edit/$1');
    $routes->get('letters/user/delete/(:segment)',   'Letters\Controllers\Users::delete/$1');
    $routes->post('letters/user/delete','Letters\Controllers\Users::delete');

    //added by rakeshsahu
    $routes->get('letters','Letters\Controllers\Letter::index');
    $routes->get('letters/search','Letters\Controllers\Letter::search');
    $routes->match(['get','post'],'letters/add', 'Letters\Controllers\Letter::add');
    $routes->match(['post'],'letters/delete', 'Letters\Controllers\Letter::delete');
});
    