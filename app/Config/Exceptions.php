<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Setup how the exception handler works.
 */
class Exceptions extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * LOG EXCEPTIONS?
     * --------------------------------------------------------------------------
     * If true, then exceptions will be logged through Services::Log.
     *
     * Default: true
     *
     * @var bool
     */
    public bool $log = true;

    /**
     * --------------------------------------------------------------------------
     * DO NOT LOG STATUS CODES
     * --------------------------------------------------------------------------
     * Any status codes here will NOT be logged if logging is turned on.
     * By default, only 404 (Page Not Found) exceptions are ignored.
     *
     * @var array
     */
    public array $ignoreCodes = [404];

    /**
     * --------------------------------------------------------------------------
     * Error Views Path
     * --------------------------------------------------------------------------
    
