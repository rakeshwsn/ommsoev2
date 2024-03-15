<?php

/*
 |--------------------------------------------------------------------------
 | ERROR REPORTING
 |--------------------------------------------------------------------------
 | Report all errors except E_NOTICE and E_STRICT in development environment
 | and display no errors in production environment.
 */
$environment = (php_sapi_name() === 'cli') ? env('APP_ENV') : 'production';
switch ($environment) {
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 1);
        break;
    case 'production':
    default:
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
        break;
}

/*
 |--------------------------------------------------------------------------
 | DEBUG MODE
 |--------------------------------------------------------------------------
 | Debug mode is an experimental flag that can allow changes throughout
 | the system. It's not widely used currently, and may not survive
 | release of the framework.
 */
defined('CI_DEBUG') || define('CI_DEBUG', $environment === 'development');
