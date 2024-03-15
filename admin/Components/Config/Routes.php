<?php

namespace Admin\Components\Config;

use Config\Services;

$routes = Services::routes(true);

if (!isset($routes)) {
    $routes = Services::routes(true);
}

$routes->group(env('app.adminRoute'), [
    'namespace' => 'Admin',
    'filter' => 'login'
], function ($routes) {

    // Components routes
    $routes->group('components', function ($routes) {
        $routes->get('', 'Components\Controllers\Components::index');
        $routes->post('search', 'Components\Controllers\Components::search');
        $routes->get('autocomplete', 'Components\Controllers\Components::autocomplete');
        $routes->get('phase/(:num)', 'Components\Controllers\Components::phase/$1');

        $routes->get('add', 'Components\Controllers\Components::add');
        $routes->get('edit/(:num)', 'Components\Controllers\Components::edit/$1');
        $routes->delete('delete/(:num)', 'Components\Controllers\Components::delete/$1');
    });

    // Components assign routes
    $routes->group('components/assign', function ($routes) {
        $routes->get('', 'Components\Controllers\Assign::index');
        $routes->get('(:num)', 'Components\Controllers\Assign::index/$1');
        $routes->get('add', 'Components\Controllers\Assign::add');
        $routes->post('delete', 'Components\Controllers\Assign::delete');
    });

    // Components agency assign routes
    $routes->group('components/agencyassign', function ($routes) {
        $routes->get('', 'Components\Controllers\AgencyAssign::index');
        $routes->get('(:num)', 'Components\Controllers\AgencyAssign::index/$1');
    });

});
