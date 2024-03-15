<?php

namespace Admin\Enterprises\Config;

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

// Group all admin enterprise routes
$routes->group('admin', [
    'namespace' => 'Admin',
    'filter' => 'login'
], function ($routes) {
    // The index page for enterprises
    $routes->add('enterprises', 'Enterprises\Controllers\Enterprise::index');

    // Search for enterprises
    $routes->post('enterprises/search', 'Enterprises\Controllers\Enterprise::search');

    // Additional routes for incentives have been commented out
    // Uncomment these lines if you want to add them back

    // $routes->match(['get', 'post'], 'incentive/view/(:segment)', 'Incentive\Controllers\Incentive::view/$1');
    // $routes->match(['get', 'post'], 'incentive/add', 'Incentive\Controllers\Incentive::add');
    // $routes->match(['get', 'post'], 'incentive/addform', 'Incentive\Controllers\Incentive::addform');
    // $routes->match(['get', 'post'], 'incentive/edit/(:segment)', 'Incentive\Controllers\Incentive::edit/$1');
    // $routes->get('incentivemain/delete/(:segment)', 'Incentive\Controllers\Incentive::delete/$1');
    // $routes->post('incentive/delete', 'Incentive\Controllers\Incentive::delete');
    // $routes->add('incentive/incentivesearch', 'Incentive\Controllers\Incentive::incentivesearch');
    // $routes->post('incentive/searchall', 'Incentive\Controllers\Incentive::searchall');
});
