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
    // Use route grouping to group all FPO routes
    $fpoRoutes = function ($routes) {
        $routes->add('', 'FPO\Controllers\FPO::index')->name('fpo.index');
        $routes->add('create', 'FPO\Controllers\FPO::add')->name('fpo.create');
        $routes->add('edit/(:num)', 'FPO\Controllers\FPO::edit/$1')->name('fpo.edit');
        $routes->add('view/(:num)', 'FPO\Controllers\FPO::details/$1')->name('fpo.view');
        $routes->add('geview/(:num)', 'FPO\Controllers\FPO::gedit/$1')->name('fpo.geview');
        $routes->post('upload', 'FPO\Controllers\FPO::upload')->name('fpo.upload');
    };

    // Call the FPO route group
    $fpoRoutes($routes);
});
