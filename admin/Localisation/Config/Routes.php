<?php

namespace Admin\Localisation\Config;

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('district', 'Localisation\Controllers\District::index');
    $routes->post('district/search', 'Localisation\Controllers\District::search', ['permission' => false]);
    $routes->add('district/block/', 'Localisation\Controllers\District::block', ['permission' => false]);
    $routes->get('district/block/(:segment)', 'Localisation\Controllers\District::block/$1');
    $routes->get('district/fundagency/', 'Localisation\Controllers\District::fundAgency');
    $routes->get('district/fundagency/(:segment)', 'Localisation\Controllers\District::fundAgency/$1');
    $routes->match(['get', 'post'], 'district/add', 'Localisation\Controllers\District::add');
    $routes->match(['get', 'post'], 'district/edit/(:segment)', 'Localisation\Controllers\District::edit/$1');
    $routes->get('district/delete/(:segment)', 'Localisation\Controllers\District::delete/$1');
    $routes->post('district/delete', 'Localisation\Controllers\District::delete');

    $routes->add('block', 'Localisation\Controllers\Block::index');
    $routes->add('block/grampanchayat/', 'Localisation\Controllers\Block::grampanchayat/');
    $routes->get('block/grampanchayat/(:segment)', 'Localisation\Controllers\Block::grampanchayat/$1');
    $routes->get('block/grampanchayat/(:segment)/(:segment)', 'Localisation\Controllers\Block::grampanchayat/$1/$2');
    $routes->post('block/search', 'Localisation\Controllers\Block::search', ['permission' => false]);
    $routes->match(['get', 'post'], 'block/add', 'Localisation\Controllers\Block::add');
    $routes->match(['get', 'post'], 'block/edit/(:segment)', 'Localisation\Controllers\Block::edit/$1');
    $routes->get('block/delete/(:segment)', 'Localisation\Controllers\Block::delete/$1');
    $routes->post('block/delete', 'Localisation\Controllers\Block::delete');

    $routes->add('areacoverage/grampanchayat', 'Localisation\Controllers\Grampanchayat::index');
    $routes->match(['get', 'post'], 'grampanchayat/add', 'Localisation\Controllers\Grampanchayat::add');
    $routes->post('grampanchayat/ajaxadd', 'Localisation\Controllers\Grampanchayat::ajaxAdd');
    $routes->get('grampanchayat/delete/(:segment)', 'Localisation\Controllers\Grampanchayat::delete/$1');
    $routes->post('grampanchayat/delete', 'Localisation\Controllers\Grampanchayat::delete');
    $routes->match(['get', 'post'], 'grampanchayat/edit/(:segment)', 'Localisation\Controllers\Grampanchayat::edit/$1');
    $routes->post('grampanchayat/search', 'Localisation\Controllers\Grampanchayat::search', ['permission' => false]);

    //saraswatee
    $routes->add('grampanchayat', 'Localisation\Controllers\Grampanchayat::index');
    $routes->add('village', 'Localisation\Controllers\Village::index');
    $routes->post('village/search', 'Localisation\Controllers\Village::search', ['permission' => false]);

    $routes->get('village/delete/(:segment)', 'Localisation\Controllers\Village::delete/$1');
    $routes->post('village/delete', 'Localisation\Controllers\Village::delete');
    $routes->match(['get', 'post'], 'village/add', 'Localisation\Controllers\Village::add');
    $routes->post('village/ajaxadd', 'Localisation\Controllers\Village::ajaxAdd'); 
    $routes->match(['get', 'post'], 'village/edit/(:segment)', 'Localisation\Controllers\Village::edit/$1');

    $routes->get('blocks/grampanchayat/', 'Localisation\Controllers\Village::grampanchayat', ['permission' => false]);
    //edited by hemant
    $routes->add('districts/block', 'Localisation\Controllers\District::block');

    //LGD Url by saraswatee
    $routes->add('lgdblock', 'Localisation\Controllers\LgdBlocks::index');
    $routes->post('lgdblock/search', 'Localisation\Controllers\LgdBlocks::search', ['permission' => false]);
    $routes->add('lgd_district/block', 'Localisation\Controllers\LgdGps::block', ['permission' => false]);
    $routes->add('lgd_blocks/grampanchayat', 'Localisation\Controllers\LgdVillages::grampanchayat', ['permission' => false]);

    $routes->add('lgdgp', 'Localisation\Controllers\LgdGps::index');
    $routes->post('lgdgp/search', 'Localisation\Controllers\LgdGps::search', ['permission' => false]);
    $routes->add('lgdvillage', 'Localisation\Controllers\LgdVillages::index');
    $routes->add('lgdvillage/search', 'Localisation\Controllers\LgdVillages::search', ['permission' => false]);

});
