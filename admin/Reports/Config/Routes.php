<?php
namespace Admin\Reports\Config;
if(!isset($routes))
{
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin','filter' => 'login'], function ($routes) {
    $routes->get('pendingstatus', 'Reports\Controllers\Reports::pendingStatus');
    $routes->get('reports/mis','Reports\Controllers\MIS::index');
    $routes->get('reports','Reports\Controllers\Reports::index');
    $routes->get('reports/uploadstatus','Reports\Controllers\Reports::uploadStatus');
    $routes->get('reports/mpr','Reports\Controllers\Mpr::index');
    $routes->get('reports/mpr/status','Reports\Controllers\Mpr::uploadStatus');
    $routes->get('reports/mpr/download','Reports\Controllers\Mpr::index/download');
    $routes->get('reports/bankinterest','Reports\Controllers\BankInterest::index');
    $routes->get('reports/sfp','Reports\Controllers\SFP::index');
    $routes->get('reports/abstract','Reports\Controllers\Mpr::abstractMpr');
    $routes->add('reports/mprupload/upload','Reports\Controllers\MprUpload::upload');
    $routes->add('reports/mprupload','Reports\Controllers\MprUpload::index');
    $routes->add('reports/abstractmpr','Reports\Controllers\Mpr::abstractMpr');
    $routes->add('reports/abstractmpr/download','Reports\Controllers\Mpr::abstractMpr/download');
    $routes->get('reports/mprtest','Reports\Controllers\Mpr::mprtest');
    $routes->get('reports/mprtestfinal','Reports\Controllers\Mpr::mprtestfinal');
    $routes->get('reports/oldmpr','Reports\Controllers\Oldmpr::index');

    $routes->get('reports/areacoverage','Reports\Controllers\AreaCoverage::index');
    $routes->get('reports/areacoverage/allblocks','Reports\Controllers\AreaCoverage::allblocks');
    $routes->get('reports/areacoverage/download','Reports\Controllers\AreaCoverage::index/download');
    $routes->get('reports/areacoverage/allblocks/download','Reports\Controllers\AreaCoverage::allblocks/download');
    $routes->get('reports/areacoverage/getUploadStatus','Reports\Controllers\AreaCoverage::getUploadStatus');

});

$routes->group('api', ['namespace' => 'Admin'],function($routes){
    $routes->get('mpr','Reports\Controllers\Api::mpr');
    $routes->get('mpr/filters','Reports\Controllers\Api::mprFilters');
    $routes->get('getBlocks','Reports\Controllers\Api::getBlocks');
    $routes->get('getAreaCoverage','Reports\Controllers\Api::getAreaCoverage');
    $routes->get('mpr1', 'Reports\Controllers\Api::mpr3');
    $routes->get('mpr1/filter','Reports\Controllers\Api::mprFilters');
});