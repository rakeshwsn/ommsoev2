<?php
namespace Api\Dashboard\Config;
if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}
$routes->group('api', ['namespace' => 'Api'], function($routes)
{
   $routes->add('areacoverage', 'Dashboard\Controllers\Dashboard::areacoverage'); 
   $routes->add('procurement', 'Dashboard\Controllers\Dashboard::procurement'); 
   $routes->add('pds', 'Dashboard\Controllers\Dashboard::pds'); 
   $routes->add('establishment', 'Dashboard\Controllers\Dashboard::establishment'); 
   $routes->add('enterprises2', 'Dashboard\Controllers\Dashboard::enterprise'); 
   
   $routes->add('districtarea', 'Dashboard\Controllers\Dashboard::districtarea');
   $routes->add('pds2', 'Dashboard\Controllers\Dashboard::pds2'); 
   $routes->add('procurement2', 'Dashboard\Controllers\Dashboard::procurement2'); 
   $routes->add('farmerarea', 'Dashboard\Controllers\Dashboard::farmerareayear'); 
   $routes->add('mapdata', 'Dashboard\Controllers\Dashboard::mapdata'); 
   $routes->add('enterprises', 'Dashboard\Controllers\Dashboard::enterprises'); 
   $routes->add('summerydata', 'Dashboard\Controllers\Dashboard::summerydata'); 
});

/*$routes->group('api', static function ($routes) {
    $routes->group('v1', static function ($routes) {
        $routes->get('/', 'Api::index');
        $routes->get('districtarea', 'Api::districtArea');
    });
});*/


