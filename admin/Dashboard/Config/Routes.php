<?php

namespace Admin\Dashboard\Config;

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    //Areacoverage
    $routes->get('dashboard/areacoverage', 'Dashboard\Controllers\AreaCoverage::index');
    $routes->add('dashboard/areacoverage/add', 'Dashboard\Controllers\AreaCoverage::add');
    $routes->add('dashboard/areacoverage/edit', 'Dashboard\Controllers\AreaCoverage::edit');
    $routes->get('dashboard/areacoverage/chart', 'Dashboard\Controllers\AreaCoverage::chart');
    $routes->add('dashboard/blocks', 'Dashboard\Controllers\AreaCoverage::ajaxBlocks');

    //procurement
    $routes->get('dashboard/procurement', 'Dashboard\Controllers\Procurement::index');
    $routes->add('dashboard/procurement/add', 'Dashboard\Controllers\Procurement::add');
    $routes->add('dashboard/procurement/edit', 'Dashboard\Controllers\Procurement::edit');
    $routes->get('dashboard/procurement/chart', 'Dashboard\Controllers\Procurement::chart');
    //Pds
    $routes->get('dashboard/pds', 'Dashboard\Controllers\Pds::index');
    $routes->add('dashboard/pds/add', 'Dashboard\Controllers\Pds::add');
    $routes->add('dashboard/pds/edit', 'Dashboard\Controllers\Pds::edit');
    $routes->get('dashboard/pds/chart', 'Dashboard\Controllers\Pds::chart');
    //Enterprise
    $routes->get('dashboard/enterprise', 'Dashboard\Controllers\Enterprise::index');
    $routes->add('dashboard/enterprise/add', 'Dashboard\Controllers\Enterprise::add');
    $routes->add('dashboard/enterprise/edit', 'Dashboard\Controllers\Enterprise::edit');
    $routes->get('dashboard/enterprise/chart', 'Dashboard\Controllers\Enterprise::chart');
    //Establishment
    $routes->get('dashboard/establishment', 'Dashboard\Controllers\Establishment::index');
    $routes->add('dashboard/establishment/add', 'Dashboard\Controllers\Establishment::add');
    $routes->add('dashboard/establishment/edit', 'Dashboard\Controllers\Establishment::edit');
    $routes->get('dashboard/establishment/chart', 'Dashboard\Controllers\Establishment::chart');
    $routes->get('dashboard/odishamap/chart', 'Dashboard\Controllers\Establishment::odishamap');
    //Dashboard district map
    $routes->add('dashboard/map', 'Dashboard\Controllers\DistrictMap::index');
    $routes->add('dashboard/map/add', 'Dashboard\Controllers\DistrictMap::add');
    $routes->add('dashboard/map/edit', 'Dashboard\Controllers\DistrictMap::edit');


    $routes->get('dashboard/currentyear', 'Dashboard\Controllers\Currentyear::index');

});


$routes->group('admin', ['namespace' => 'Admin'], function ($routes) {
    //Dashboard Chart
    $routes->get('dashboard/chartnew', 'Dashboard\Controllers\Dashboard::index');
});
