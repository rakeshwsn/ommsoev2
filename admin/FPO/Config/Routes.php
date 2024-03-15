<?php

namespace Admin\Proceeding\Config;

use Config\Services;

if (!isset($routes)) {
    $routes = Services::routes(true);
}

$routes->group(env('app.adminRoute'), [
    'namespace' => 'Admin',
    'filter' => 'login'
], function ($routes) {
    $routes->add('fpo', 'FPO\Controllers\FPO::index');
    $routes->add('fpo/create', 'FPO\Controllers\FPO::add');
    $routes->add('fpo/edit/(:num)', 'FPO\Controllers\FPO::edit/$1');
    $routes->add('fpo/view/(:num)', 'FPO\Controllers\FPO::details/$1');
    $routes->add('fpo/geview/(:num)', 'FPO\Controllers\FPO::gedit/$1');
    $routes->post('fpo/upload', 'FPO\Controllers\FPO::upload');
});
