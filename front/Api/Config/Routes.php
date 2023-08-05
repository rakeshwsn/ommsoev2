<?php
namespace Front\Api\Config;
if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}
$routes->group('api', ['namespace' => 'Front'], function($routes)
{
   $routes->add('areacoverage', 'Api\Controllers\Dashboard::areacoverage'); 
   $routes->add('procurement', 'Api\Controllers\Dashboard::procurement'); 

   $routes->add('pds', 'API\Controllers\Dashboard::pds'); 

   $routes->add('establishment', 'API\Controllers\Dashboard::establishment'); 
   $routes->add('enterprises', 'API\Controllers\Dashboard::enterprise'); 

});


