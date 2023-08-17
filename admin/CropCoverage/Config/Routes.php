<?php
namespace Admin\CropCoverage\Config;

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->match(['get', 'post'], 'cropcoverage/crops', 'CropCoverage\Controllers\AreaCoverageCrops::index');
    $routes->add('areacoverage/download', 'CropCoverage\Controllers\AreaCoverage::download');
    $routes->match(['get', 'post'], 'areacoverage', 'CropCoverage\Controllers\AreaCoverage::Index');
    $routes->post('areacoverage/search', 'CropCoverage\Controllers\AreaCoverage::search', ['permission' => false]);
    $routes->match(['get', 'post'], 'areacoverage/target', 'CropCoverage\Controllers\AreaCoverageTarget::index');
    $routes->match(['get', 'post'], 'cropcoverage/reports', 'CropCoverage\Controllers\CropCoverageReports::index');
    $routes->match(['get', 'post'], 'areacoverage/dashboard', 'CropCoverage\Controllers\AreaCoverageDashboard::index');
    $routes->match(['get', 'post'], 'areacoverage/approval', 'CropCoverage\Controllers\AreaCoverageApproval::index');
    $routes->match(['get', 'post'], 'areacoverage/target/edit', 'CropCoverage\Controllers\AreaCoverageTarget::edit');
    $routes->post('areacoverage/fetch-blocks', 'CropCoverage\Controllers\AreaCoverageTarget::fetchBlocks');
    $routes->add('areacoverage/download', 'CropCoverage\Controllers\AreaCoverage::download');
    $routes->add('areacoverage/upload', 'CropCoverage\Controllers\AreaCoverage::upload');
    $routes->add('areacoverage/edit', 'CropCoverage\Controllers\AreaCoverage::edit');
    $routes->add('areacoverage/delete', 'CropCoverage\Controllers\AreaCoverage::delete');
    $routes->get('areacoverage/approve', 'CropCoverage\Controllers\Approve::index');
    $routes->add('areacoverage/approve/block', 'CropCoverage\Controllers\Approve::block');
    $routes->get('areacoverage/dashboard/chart', 'CropCoverage\Controllers\AreaCoverageDashboard::chart');
    $routes->get('areacoverage/dashboard/milletchart', 'CropCoverage\Controllers\AreaCoverageDashboard::milletChart');
    $routes->add('areacoverage/approve/district', 'CropCoverage\Controllers\Approve::district');
//    $routes->post('areacoverage/approve/reject', 'CropCoverage\Controllers\Approve::reject');
    $routes->get('dashboard/chart', 'CropCoverage\Controllers\AreaCoverageDashboard::chart');


});