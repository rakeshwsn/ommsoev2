<?php
namespace Front\Pages\Config;
if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('dashboard', ['namespace' => 'Front'], function($routes){
    
    
});