<?php
namespace Admin\Reports\Config;
if(!isset($routes))
{
    $routes = \Config\Services::routes(true);
}

$routes->group(env('app.adminRoute'), ['namespace' => 'Admin'], function ($routes) {
    $routes->get('pendingstatus', 'Reports\Controllers\Reports::pendingStatus');
    $routes->get('reports/mis','Reports\Controllers\MIS::index');
});

$routes->group('api', ['namespace' => 'Admin'],function($routes){
    $routes->get('mpr','Reports\Controllers\Api::mpr');
    $routes->get('mpr/filters','Reports\Controllers\Api::mprFilters');
    $routes->get('getBlocks','Reports\Controllers\Api::getBlocks');
    $routes->get('getAreaCoverage','Reports\Controllers\Api::getAreaCoverage');

    $routes->get('mpr1', 'Reports\Controllers\Api::mpr3');
    $routes->get('mpr1/filter','Reports\Controllers\Api::mprFilters');

});