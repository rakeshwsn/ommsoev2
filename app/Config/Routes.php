<?php
namespace Config;

// Create a new instance of our RouteCollection class.
use Admin\Module\Controllers\Module;
use CodeIgniter\Controller;

$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override('Admin\Common\Controllers\Errors::index');
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
//$routes->get('/', 'Home::index');

//$routes->get('/', 'Auth::login');

$routes->group(env('app.adminRoute'), ['namespace' => 'Admin', 'filter' => 'login'], function ($routes) {

});
$routes->group('/', ['namespace' => 'Admin'], function ($routes) {
    $routes->add('profile', 'Users\Controllers\Profile::index');
    $routes->post('profile/upload', 'Users\Controllers\Profile::upload');
});
/*
{
    $routes->get('/', 'Pages\Controllers\Home::index');
    $routes->get('page/info/(:segment)',      'Pages\Controllers\Page::info/$1');
*/
/**
 * ------------------------------------------------------
 * DATABASE ROUTING
 * ------------------------------------------------------
 */
/*
    $db = \Config\Database::connect();
    $builder = $db->table('seo_url');
    $query   = $builder->get();
    $result = $query->getResult();

    foreach( $result as $row )
    {
        $routes->add($row->slug,$row->namespace)  ;
    }
});
*/
$routes->get('api', 'Api::index');

//$routes->get('/', 'Admin\Common\Controllers\Dashboard::index',['filter' => 'login']);

//$routes->group('', ['namespace' => 'Front'], function($routes){
//    $routes->get('/', 'App\Controllers\Home::index');
//});


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}