<?php
namespace Admin\Dashboard\Config;
if(!isset($routes))
{
    $routes = \Config\Services::routes(true);
}

$routes->group(env('app.adminRoute'), ['namespace' => 'Admin','filter' => 'login'], function($routes)
{
    
});
