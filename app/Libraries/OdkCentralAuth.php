<?php

namespace App\Libraries;

use Config\Services;

class OdkCentralAuth
{
    /**
     * @var string
     */
    private string $apiUrl;

    /**
     * @var string
     */
    private string $email;

    /**
     * @var string
     */
    private string $password;

    /**
     * @var int
     */
    private int $expires;

    /**
     * @var Services
     */
    private Services $client;

    /**
     * OdkCentralAuth constructor.
     * @param string $apiUrl
     * @param string $email
    
