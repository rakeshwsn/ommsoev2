<?php
namespace Admin\Transaction\Config;
if(!isset($routes))
{
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'Admin','filter' => 'login'], function($routes)
{
    $routes->get('transaction','Transaction\Controllers\Transaction::index');
    $routes->get('transaction/search','Transaction\Controllers\Transaction::search',['permission'=>false]);
    $routes->post('transaction/upload','Transaction\Controllers\Transaction::upload');
    $routes->get('transaction/downloadtemplate','Transaction\Controllers\Transaction::downloadTemplate');
    $routes->add('transaction/edit/(:num)','Transaction\Controllers\Transaction::edit/$1');
    $routes->add('transaction/add','Transaction\Controllers\Transaction::add');
    $routes->add('transaction/delete/(:num)','Transaction\Controllers\Transaction::delete/$1');
    $routes->add('transaction/refund','Transaction\Controllers\Refund::index');
    $routes->add('transaction/refund/search','Transaction\Controllers\Refund::search',['permission'=>false]);
    $routes->add('transaction/refund/add','Transaction\Controllers\Refund::add');
    $routes->add('transaction/refund/edit/(:num)','Transaction\Controllers\Refund::edit/$1');
    $routes->get('transaction/isuploaded','Transaction\Controllers\Transaction::misIsUploaded');

    $routes->add('otherreceipt','Transaction\Controllers\OtherReceipt::index');
    $routes->add('otherreceipt/search','Transaction\Controllers\OtherReceipt::search',['permission'=>false]);
    $routes->add('otherreceipt/add','Transaction\Controllers\OtherReceipt::add');
    $routes->add('otherreceipt/edit/(:num)','Transaction\Controllers\OtherReceipt::edit/$1');

    $routes->add('closingbalance','Transaction\Controllers\ClosingBalance::index');
    $routes->get('closingbalance/search','Transaction\Controllers\ClosingBalance::search',['permission'=>false]);
    $routes->add('closingbalance/add','Transaction\Controllers\ClosingBalance::add');
    $routes->add('closingbalance/info/(:num)/(:num)/(:num)/(:num)','Transaction\Controllers\ClosingBalance::info/$1/$2/$3/$4');
    $routes->post('closingbalance/upload','Transaction\Controllers\ClosingBalance::upload');
    $routes->add('closingbalance/district','Transaction\Controllers\ClosingBalance::district');

    $routes->add('openingbalance','Transaction\Controllers\OpeningBalance::index');

    $routes->get('approve','Transaction\Controllers\Approve::index');
    $routes->add('approve/transaction','Transaction\Controllers\Approve::transaction');
    $routes->add('approve/fundreceipt','Transaction\Controllers\Approve::fundReceipt');
    $routes->add('approve/otherreceipt','Transaction\Controllers\Approve::otherReceipt');
    $routes->add('approve/closingbalance','Transaction\Controllers\Approve::closingBalance');
    $routes->add('approve/mis','Transaction\Controllers\Approve::mis');
    $routes->get('approve/getform','Transaction\Controllers\Approve::getForm');

    $routes->get('correction','Transaction\Controllers\Correction::index');

});
