<?php

namespace Admin\Physicalcomponentachievement\Config;

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('phyachtraining', 'Physicalachievement\Controllers\Physicalachievement::index');
    $routes->get('phyachenterprise', 'Physicalachievement\Controllers\Physicalachievement::enterprisesindex');
    $routes->get('physicalachievement/report', 'Physicalachievement\Controllers\Physicalachievement::trackreport');
    $routes->match(['get', 'post'], 'physicalachievement/add', 'Physicalachievement\Controllers\Physicalachievement::add');
    $routes->match(['get', 'post'], 'Physicalcomponentstarget/edit/(:segment)', 'Physicalcomponentstarget\Controllers\Physicalcomponentstarget::edit/$1');
    $routes->get('physicalcomponents/delete/(:segment)',   'Physicalcomponents\Controllers\Physicalcomponents::delete/$1');
    $routes->post('physicalcomponents/delete', 'Physicalcomponents\Controllers\Physicalcomponents::delete');
    $routes->post('physicalachievement/searchtargetdata', 'Physicalachievement\Controllers\Physicalachievement::searchtargetdata',['permission'=>false]);

});
