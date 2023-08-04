<?php
namespace Admin\Users\Config;
if(!isset($routes))
{
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin','filter' => 'login'], function($routes)
{
    $routes->add('users', 'Users\Controllers\Users::index');
    $routes->add('users/search','Users\Controllers\Users::search');
    $routes->match(['get','post'],'users/add', 'Users\Controllers\Users::add');
    $routes->match(['get','post'],'users/edit/(:segment)', 'Users\Controllers\Users::edit/$1',['filter'=>'permission']);
    $routes->get('users/delete/(:segment)',   'Users\Controllers\Users::delete/$1');
    $routes->post('users/delete','Users\Controllers\Users::delete');
    $routes->get('users/login/(:segment)','Users\Controllers\Users::login/$1');
    $routes->get('users/allowupload','Users\Controllers\AllowUpload::index');
    $routes->add('users/allowupload/update','Users\Controllers\AllowUpload::update');
    $routes->get('users/uploadstatus','Users\Controllers\UploadStatus::index');
    $routes->add('users/uploadstatus/update','Users\Controllers\UploadStatus::update');

    $routes->add('members', 'Users\Controllers\Members::index');
    $routes->post('members/search','Users\Controllers\Members::search',['permission' => false]);
    $routes->match(['get','post'],'members/add', 'Users\Controllers\Members::add');
    $routes->match(['get','post'],'members/edit/(:segment)', 'Users\Controllers\Members::edit/$1');
    $routes->get('members/delete/(:segment)',   'Users\Controllers\Members::delete/$1');
    $routes->post('members/delete','Users\Controllers\Members::delete');

    $routes->add('usergroup', 'Users\Controllers\Usergroup::index');
    $routes->post('usergroup/search','Users\Controllers\Usergroup::search',['permission' => false]);
    $routes->match(['get','post'],'usergroup/add', 'Users\Controllers\Usergroup::add');
    $routes->match(['get','post'],'usergroup/edit/(:segment)', 'Users\Controllers\Usergroup::edit/$1');
    $routes->get('usergroup/delete/(:segment)',   'Users\Controllers\Usergroup::delete/$1');
    $routes->post('usergroup/delete','Users\Controllers\Usergroup::delete');
    $routes->match(['get','post'],'usergroup/permission/(:segment)', 'Users\Controllers\Usergroup::permission/$1');

});
