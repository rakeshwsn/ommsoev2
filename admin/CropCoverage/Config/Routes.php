<?php
namespace Admin\CropCoverage\Config;

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->match(['get', 'post'], 'cropcoverage/crops', 'CropCoverage\Controllers\AreaCoverageCrops::index');
    $routes->add('areacoverage/download', 'CropCoverage\Controllers\AreaCoverage::download');
    $routes->match(['get', 'post'], 'areacoverage', 'CropCoverage\Controllers\AreaCoverage::Index');
    $routes->post('areacoverage/search', 'CropCoverage\Controllers\AreaCoverage::search');


    $routes->match(['get', 'post'], 'areacoverage/target', 'CropCoverage\Controllers\AreaCoverageTarget::index');
    $routes->match(['get', 'post'], 'areacoverage/approval', 'CropCoverage\Controllers\AreaCoverageApproval::index');
    $routes->match(['get', 'post'], 'areacoverage/target/edit', 'CropCoverage\Controllers\AreaCoverageTarget::edit');
    $routes->post('areacoverage/fetch-blocks', 'CropCoverage\Controllers\AreaCoverageTarget::fetchBlocks');
    $routes->add('areacoverage/download', 'CropCoverage\Controllers\AreaCoverage::download');

});