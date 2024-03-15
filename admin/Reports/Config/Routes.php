<?php

namespace App\Config;

use Config\Services;

class Routes
{
    public function __construct()
    {
        $this->routes = Services::routes(true);
    }

    public function defineRoutes()
    {
        $this->defineAdminRoutes();
        $this->defineApiRoutes();
    }

    protected function defineAdminRoutes()
    {
        $this->routes->group(env('app.adminRoute'), [
            'namespace' => 'Admin\Reports\Controllers',
        ], function ($routes) {
            $routes->get('pendingstatus', 'Reports::pendingStatus');
        });
    }

    protected function defineApiRoutes()
    {
        $this->routes->group('api', [
            'namespace' => 'Admin\Reports\Controllers\Api',
        ])->group('', function ($routes) {
            $routes->get('mpr', 'Mpr::index');
            $routes->get('mpr/filters', 'Mpr::filters');
            $routes->get('getBlocks', 'GetBlocks::index');
            $routes->get('getAreaCoverage', 'GetAreaCoverage::index');
        })->group('mpr1', function ($routes) {
            $routes->get('', 'Mpr::mpr3');
            $routes->get('filters', 'Mpr::mprFilters');
        });
    }
}
