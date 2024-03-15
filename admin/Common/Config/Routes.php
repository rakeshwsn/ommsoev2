<?php
namespace Admin\Common\Config;
if(!isset($routes))
{
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin','filter' => 'login'], function($routes)
{
    $routes->add('/', 'Common\Controllers\Dashboard::index');
    $routes->add('login', 'Common\Controllers\Auth::login');
    $routes->get('logout', 'Common\Controllers\Auth::logout');
    $routes->add('relogin', 'Common\Controllers\Auth::reLogin');
    $routes->get('oldportallogin', 'Common\Controllers\Auth::oldPortalLogin');
    $routes->match(['get','post'],'account/password', 'Common\Controllers\Auth::password');
    $routes->add('error', 'Common\Controllers\Errors::index');

    $routes->get('spmu/chart', 'Common\Controllers\Dashboard::spmu_dashboard_chart');
    $routes->get('dashboard/chart','Common\Controllers\Dashboard::chart');
    $routes->get('dashboard/getabstractdetails','Common\Controllers\Dashboard::getabstractdetails');

});
