<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Encryption configuration class.
 *
 * These are the settings used for encryption, if you don't pass a parameter
 * array to the encrypter for creation/initialization.
 */
class Encryption extends BaseConfig
{
    /**
     * Encryption key (seed)
     *
     * If you use the Encryption class, you must set an encryption key (seed).
     * You need to ensure it is long enough for the cipher and mode you plan to use.
     *
     * @var string
     */
    public $encryptionKey = '';

    /**
     * Encryption driver to use
     *
     * One of the supported encryption drivers.
     *
     * Available drivers:
     * - OpenSSL
     * - Sodium
     *
     * @var string
     */
    public $driver = 'OpenSSL';

    /**
     * SodiumHandler's padding length in bytes
     *
     * This is the number of bytes that will be padded to the plaintext message
     * before it is encrypted. This value should be greater than zero.
     *
     * @var integer
     */
    public $paddingLength = 16;

    /**
     * Encryption digest
     *
     * HMAC digest to use, e.g. 'SHA512' or 'SHA256'. Default value is 'SHA512'.
     *
     * @var string
     */
    public $hmacDigest = 'SHA512';
}
