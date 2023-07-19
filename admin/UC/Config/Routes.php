<?php
namespace Admin\UC\Config;
if(!isset($routes))
{
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin','filter' => 'login'], function($routes)
{
    $routes->get('uc/allotment','UC\Controllers\Allotment::index');
    $routes->add('uc/allotment/add','UC\Controllers\Allotment::add');
    $routes->add('uc/allotment/edit','UC\Controllers\Allotment::edit');
    $routes->get('uc/allotment/info','UC\Controllers\Allotment::info');
    $routes->post('uc/allotment/delete','UC\Controllers\Allotment::delete');
    $routes->get('uc/submit','UC\Controllers\Submit::index');
    $routes->get('uc/submit/info','UC\Controllers\Submit::info');
    $routes->add('uc/submit/add','UC\Controllers\Submit::add');
    $routes->add('uc/submit/edit','UC\Controllers\Submit::edit');
    $routes->add('uc/submit/upload','UC\Controllers\Submit::upload');

});
