<?php
namespace Admin\Incentive\Config;
if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin','filter' => 'login'], function($routes)
{
    $routes->add('incentive', 'Incentive\Controllers\Incentive::index');
    $routes->post('incentive/search','Incentive\Controllers\Incentive::search',['permission'=>false]);
    $routes->post('incentive/searchmain','Incentive\Controllers\Incentive::searchMain',['permission'=>false]);
    $routes->match(['get','post'],'incentive/view/(:segment)', 'Incentive\Controllers\Incentive::view/$1');
    $routes->match(['get','post'],'incentive/add', 'Incentive\Controllers\Incentive::add');
    $routes->match(['get','post'],'incentive/addform', 'Incentive\Controllers\Incentive::addform');
    $routes->match(['get','post'],'incentive/edit/(:segment)', 'Incentive\Controllers\Incentive::edit/$1');
    $routes->get('incentivemain/delete/(:segment)',   'Incentive\Controllers\Incentive::delete/$1');
    $routes->post('incentive/delete','Incentive\Controllers\Incentive::delete');
    // $routes->add('incentive/incentivesearch/(:segment)', 'Incentive\Controllers\Incentive::incentivesearch/$1');
    $routes->add('incentive/incentivesearch', 'Incentive\Controllers\Incentive::incentivesearch',['permission'=>false]);
    $routes->post('incentive/searchall','Incentive\Controllers\Incentive::searchall',['permission'=>false]);
    $routes->post('incentive/ajaxverify','Incentive\Controllers\Incentive::ajaxverify');
    $routes->post('incentive/ajaxverifyupdate','Incentive\Controllers\Incentive::ajaxverifyupdate');
    $routes->get('incentive/downloadexcel','Incentive\Controllers\Incentive::downloadExcelFile');
    $routes->get('incentive/countExcelHeaders','Incentive\Controllers\Incentive::countExcelHeaders');
    $routes->get('incentive/uploadstatus','Incentive\Controllers\Incentive::uploadStatus');

});