<?php
namespace Admin\MIS\Config;
if(!isset($routes))
{
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin','filter' => 'login'], function($routes)
{
    $routes->add('mis','MIS\Controllers\MIS::index');
    $routes->add('mis/add','MIS\Controllers\MIS::add');
    $routes->add('mis/edit/(:num)','MIS\Controllers\MIS::edit/$1');
    $routes->get('mis/delete/(:num)','MIS\Controllers\MIS::delete/$1');
	$routes->add('mis/upload','MIS\Controllers\MIS::upload');
	$routes->post('mis/search','MIS\Controllers\MIS::search');

});
