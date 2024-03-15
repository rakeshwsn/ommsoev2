<?php

namespace Config;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Resources\System\Filters\CSRF;
use CodeIgniter\Resources\System\Filters\DebugToolbar;
use CodeIgniter\Resources\System\Filters\Honeypot;

$routes = Services::routes();

if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

$routes->group('admin', ['namespace' => 'Admin', 'filter' => 'login'])
	->group('', ['namespace' => 'Common\Controllers'])
		->add('/', 'Dashboard::index')
		->add('login', 'Auth::login')
		->get('logout', 'Auth::logout')
		->add('relogin', 'Auth::reLogin')
		->get('oldportallogin', 'Auth::oldPortalLogin')
		->match(['get', 'post'], 'account/password', 'Auth::password')
	->end()
	->group('localisation', ['namespace' => 'Localisation\Controllers'])
		->add('district', 'District::index')
		->post('district/search', 'District::search')
		->add('district/block/', 'District::block/')
		->get('district/block/(:segment)', 'District::block/$1')
		->get('district/fundagency/', 'District::fundAgency')
		->get('district/fundagency/(:segment)', 'District::fundAgency/$1')
		->match(['get', 'post'], 'district/add', 'District::add')
		->match(['get', 'post'], 'district/edit/(:segment)', 'District::edit/$1')
		->get('district/delete/(:segment)', 'District::delete/$1')
		->post('district/delete', 'District::delete')
		->add('block', 'Block::index')
		->add('block/grampanchayat/', 'Block::grampanchayat/')
		->get('block/grampanchayat/(:segment)', 'Block::grampanchayat/$1')
		->get('block/grampanchayat/(:segment)/(:segment)', 'Block::grampanchayat/$1/$2')
		->get('block/cluster/(:segment)', 'Block::cluster/$1')
		->post('block/search', 'Block::search')
		->match(['get', 'post'], 'block/add', 'Block::add')
		->match(['get', 'post'], 'block/edit/(:segment)', 'Block::edit/$1')
		->get('block/delete/(:segment)', 'Block::delete/$1')
		->post('block/delete', 'Block::delete')
	->end()
	->group('users', ['namespace' => 'Users\Controllers'])
		->add('users', 'Users::index')
		->post('users/search', 'Users::search')
		->match(['get', 'post'], 'users/add', 'Users::add')
		->match(['get', 'post'], 'users/edit/(:segment)', 'Users::edit/$1')
		->get('users/delete/(:segment)', 'Users::delete/$1')
		->post('users/delete', 'Users::delete')
		->get('users/login/(:segment)', 'Users::login/$1')
		->add('users/allowupload', 'AllowUpload::index')
		->add('users/allowupload/update', 'AllowUpload::update')
		->add('users/uploadstatus', 'UploadStatus::index')
		->add('users/uploadstatus/update', 'UploadStatus::update')
	->end()
	->group('members', ['namespace' => 'Users\Controllers'])
		->add('members', 'Members::index')
		->post('members/search', 'Members::search')
		->match(['get', 'post'], 'members/add', 'Members::add')
		->match(['get', 'post'], 'members/edit/(:segment)', 'Members::edit/$1')
		->get('members/delete/(:segment)', 'Members::delete/$1')
		->post('members/delete', 'Members::delete')
	->end()
	->group('usergroup', ['namespace' => 'Users\Controllers'])
		->add('usergroup', 'Usergroup::index')
		->post('usergroup/search', 'Usergroup::search')
		->match(['get', 'post'], 'usergroup/add', 'Usergroup::add')
		->match(['get', 'post'], 'usergroup/edit/(:segment)', 'Usergroup::edit/$1')
		->get('usergroup/delete/(:segment)', 'Usergroup::delete/$1')
		->post('usergroup/delete', 'Usergroup::delete')
		->match(['get', 'post'], 'usergroup/permission/(:segment)', 'Usergroup::permission/$1')
	->end()
	->group('menu', ['namespace' => 'Menu\Controllers'])
		->match(['get', 'post'], 'menu', 'Menu::index')
		->match(['get', 'post'], 'menu/(:num)', 'Menu::index/$1')
		->match(['get', 'post'], 'menu/add', 'Menu::add')
		->match(['get', 'post'], 'menu/edit/(:segment)',
