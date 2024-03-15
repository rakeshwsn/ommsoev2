<?php

namespace App\Libraries;

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
     * @var \Config\Services
     */
    private \Config\Services $client;

    /**
     * OdkCentralAuth constructor.
     */

