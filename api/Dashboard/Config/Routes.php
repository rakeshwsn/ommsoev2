<?php

namespace Api\Dashboard\Config;

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}
$routes->group('api', ['namespace' => 'Api'], function ($routes) {
    $routes->get('areacoverage_c', 'Dashboard\Controllers\Dashboard::areacoverage');
    $routes->get('procurement_c', 'Dashboard\Controllers\Dashboard::procurement');
    $routes->get('pds_c', 'Dashboard\Controllers\Dashboard::pds');
    $routes->get('establishment', 'Dashboard\Controllers\Dashboard::establishment');
    $routes->get('enterprises2', 'Dashboard\Controllers\Dashboard::enterprise');
    $routes->get('currentyear', 'Dashboard\Controllers\Dashboard::currentyearchart');

    //api
    $routes->get('areacoverage', 'Dashboard\Controllers\Dashboard::areacoverage');
    $routes->get('districtarea', 'Dashboard\Controllers\Dashboard::districtarea');
    $routes->get('procurement', 'Dashboard\Controllers\Dashboard::procurement2');
    $routes->get('pds', 'Dashboard\Controllers\Dashboard::pds2');
    $routes->get('farmerarea', 'Dashboard\Controllers\Dashboard::farmerareayear');
    $routes->get('mapdata', 'Dashboard\Controllers\Dashboard::mapdata');
    $routes->get('enterprises', 'Dashboard\Controllers\Dashboard::enterprises');
    $routes->get('summary', 'Dashboard\Controllers\Dashboard::summary');
    $routes->get('odmapdata', 'Dashboard\Controllers\Dashboard::odmapdata');
    $routes->get('sumdata', 'Dashboard\Controllers\Dashboard::sumdata');


    //added by rakesh nayak
    $routes->get('localisation', 'Dashboard\Controllers\Localisation::localisation');
    $routes->post('partnerdashboard', 'Dashboard\Controllers\Partnerdashboard::partnerdashboard');
    $routes->post('partnerdashboardAreacoverage', 'Dashboard\Controllers\Partnerdashboard::partnerdashboardAreacoverage');
});
