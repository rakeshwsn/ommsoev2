<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', [
    'namespace' => 'Admin',
    'filter'    => 'login',
], function ($routes) {
    $routes->add('permission', 'Permission\Controllers\Permission::index');
    $routes->post('permission/search', 'Permission\Controllers\Permission::search');

    $permissionRoutes = [
        'add'    => 'Permission\Controllers\Permission::add',
        'edit/{segment}' => 'Permission\Controllers\Permission::edit/$1',
        'delete/{segment}' => 'Permission\Controllers\Permission::delete/$1',
    ];

    foreach ($permissionRoutes as $method => $route) {
        $routes->match(['get', 'post'], "permission/$method", $route);
    }

    $routes->post('permission/delete', 'Permission\Controllers\Permission::delete');
});
