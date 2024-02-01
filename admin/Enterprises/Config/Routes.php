<?php

namespace Admin\Enterprises\Config;

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('enterprises/unit', 'Enterprises\Controllers\EnterpriseUnit::index');
    $routes->add('enterprises/unit/edit/(:num)', 'Enterprises\Controllers\EnterpriseUnit::edit/$1');
    $routes->add('enterprises/unit/add', 'Enterprises\Controllers\EnterpriseUnit::add');
    $routes->add('enterprises/unit/search', 'Enterprises\Controllers\EnterpriseUnit::search');
    $routes->add('enterprises/unit/delete/(:num)', 'Enterprises\Controllers\EnterpriseUnit::delete/$1');
    // enterprises
    $routes->add('enterprises/add', 'Enterprises\Controllers\Enterprises::add');
    $routes->add('enterprises', 'Enterprises\Controllers\Enterprises::index');
    $routes->add('enterprises/blocks', 'Enterprises\Controllers\Enterprises::ajaxBlocks');
    $routes->add('enterprises/doe', 'Enterprises\Controllers\Enterprises::ajaxDoe');
    $routes->add('enterprises/exceldownld', 'Enterprises\Controllers\Enterprises::download');
    $routes->add('enterprises/excel', 'Enterprises\Controllers\Enterprises::excel');


    $routes->add('enterprises/edit', 'Enterprises\Controllers\Enterprises::edit');
    $routes->get('enterprises/cancel', 'Enterprises\Controllers\Enterprises::index');
    $routes->get('enterprises/transaction', 'Enterprises\Controllers\EstablishmentTransaction::index');
    $routes->get('enterprisestrans/download', 'Enterprises\Controllers\EstablishmentTransaction::download');
    $routes->add('enterprisestrans/edit', 'Enterprises\Controllers\EstablishmentTransaction::edit');
    $routes->add('enterprises/delete', 'Enterprises\Controllers\EstablishmentTransaction::delete');
    
    $routes->add('enterprisestrans/upload', 'Enterprises\Controllers\EstablishmentTransaction::upload');

    $routes->add('enterprises/gps', 'Enterprises\Controllers\Enterprises::ajaxgps');
    $routes->add('enterprises/center', 'Enterprises\Controllers\Enterprises::ajaxcenter');
    $routes->get('enterprises/getlgdgps', 'Enterprises\Controllers\Enterprises::getLgdGps');
    $routes->get('enterprises/getlgdvillages', 'Enterprises\Controllers\Enterprises::getLgdVillages');

    $routes->add('enterprises/villages', 'Enterprises\Controllers\Enterprises::ajaxvillages');

    $routes->post('enterprises/search', 'Enterprises\Controllers\Enterprises::search',['permission'=>false]);

    $routes->get('enterprises/report', 'Enterprises\Controllers\EstablishmentReport::index');
    $routes->add('enterprises/report/blocks', 'Enterprises\Controllers\EstablishmentReport::ajaxBlocks');
    
    $routes->get('enterprises/enttxnreport', 'Enterprises\Controllers\EstablishmentTransReport::index');
    $routes->add('enttxnreport/blocks', 'Enterprises\Controllers\EstablishmentTransReport::ajaxBlocks');

    // $routes->get('entexcelreport/download', 'Enterprises\Controllers\EstablishmentReport::excelDownload');
    $routes->add('demo', 'Enterprises\Controllers\EstablishmentReport::demo');
    $routes->add('estmaster', 'Enterprises\Controllers\EnterprisesMaster::index');
    $routes->add('distwisethr', 'Enterprises\Controllers\DistWiseThr::index');
    
    $routes->add('dwlndthr', 'Enterprises\Controllers\DistWiseThr::download_thr');


});
