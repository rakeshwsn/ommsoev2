<?php

namespace Config;

use CodeIgniter\Controller;

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

// Dashboard routes
$routes->group(env('app.adminRoute'), ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('/', 'Common\Controllers\Dashboard::index');
    $routes->add('logout', 'Common\Controllers\Auth::logout');
    $routes->add('relogin', 'Common\Controllers\Auth::reLogin');
    $routes->get('oldportallogin', 'Common\Controllers\Auth::oldPortalLogin');
});

// Authentication routes
$routes->group(env('app.adminRoute'), ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('login', 'Common\Controllers\Auth::login');
    $routes->match(['get', 'post'], 'account/password', 'Common\Controllers\Auth::password');
});

// Localisation routes
$routes->group(env('app.adminRoute'), ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('district', 'Localisation\Controllers\District::index');
    // ... other district routes

    $routes->add('block', 'Localisation\Controllers\Block::index');
    // ... other block routes
});

// Users routes
$routes->group(env('app.adminRoute'), ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('users', 'Users\Controllers\Users::index');
    // ... other users routes

    $routes->add('members', 'Users\Controllers\Members::index');
    // ... other members routes

    $routes->add('usergroup', 'Users\Controllers\Usergroup::index');
    // ... other usergroup routes
});

// Menu routes
$routes->group(env('app.adminRoute'), ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->match(['get', 'post'], 'menu', 'Menu\Controllers\Menu::index');
    // ... other menu routes
});

// Transaction routes
$routes->group(env('app.adminRoute'), ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('transaction', 'Transaction\Controllers\Transaction::index');
    // ... other transaction routes

    $routes->add('transaction/refund', 'Transaction\Controllers\Refund::index');
    // ... other refund routes

    $routes->add('otherreceipt', 'Transaction\Controllers\OtherReceipt::index');
    // ... other otherreceipt routes

    $routes->add('closingbalance', 'Transaction\Controllers\ClosingBalance::index');
    // ... other closingbalance routes
});

// Component routes
$routes->group(env('app.adminRoute'), ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('component', 'Component\Controllers\Component::index');
    // ... other component routes

    $routes->get('component/agency', 'Component\Controllers\Agency::index');
    // ... other agency routes

    $routes->add('component/budget', 'Component\Controllers\Budget::index');
});

// Approve routes
$routes->group(env('app.adminRoute'), ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('approve', 'Transaction\Controllers\Approve::index');
    // ... other approve routes
});

// Reports routes
$routes->group(env('app.adminRoute'), ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('reports', 'Reports\Controllers\Reports::index');
    // ... other reports routes
});

// UC routes
$routes->group(env('app.adminRoute'), ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('uc/allotment', 'UC\Controllers\Allotment::index');
    // ... other allotment routes

    $routes->get('uc/submit', 'UC\Controllers\Submit::index');
    // ... other submit routes
});

// MIS routes
$routes->group(env('app.adminRoute'), ['namespace' => 'Admin'], function ($routes) {
    $routes->add('mis', 'MIS\Controllers\MIS::index');
    // ... other mis routes
});

// Profile routes
$routes->group('', ['namespace' => 'Admin'], function ($routes) {
    $routes->add('profile', 'Users\Controllers\Profile::index');
});

$routes->get('api', 'Api::index');

/*
foreach (glob(ROOTPATH . 'admin/*', GLOB_ONLYDIR) as $item_dir)
{
    if (file_exists($item_dir . '/Config/Routes.php'))
    {
        require_once($item_dir . '/Config/Routes.php');
    }
}

foreach (glob(ROOTPATH . 'front/*', GLOB_ONLYDIR) as $item_dir)
{
    if (file_exists($item_dir . '/Config/Routes.php'))
    {
        require_once($item_dir . '/Config/Routes.php');
    }
}
*/

if (file
