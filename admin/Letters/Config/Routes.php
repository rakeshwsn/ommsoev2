<?php
namespace Admin\Users\Config;
if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('admin/letters', ['namespace' => 'Admin','filter' => 'login'], function($routes)
{
    //added by rakeshnayak
    $routes->add('user', 'Letters\Controllers\Users::index');
    $routes->post('user/search','Letters\Controllers\Users::search');
    $routes->match(['get','post'],'user/add', 'Letters\Controllers\Users::add');
    $routes->match(['get','post'],'user/edit/(:segment)', 'Letters\Controllers\Users::edit/$1');
    $routes->get('user/delete/(:segment)',   'Letters\Controllers\Users::delete/$1');
    $routes->post('user/delete','Letters\Controllers\Users::delete');

    //added by rakeshsahu
    $routes->get('/','Letters\Controllers\Letter::index');
    $routes->get('search','Letters\Controllers\Letter::search');
    $routes->match(['get','post'],'add', 'Letters\Controllers\Letter::add');
    $routes->match(['post'],'delete', 'Letters\Controllers\Letter::delete');
});
    