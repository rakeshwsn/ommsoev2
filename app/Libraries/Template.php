<?php

namespace App\Libraries;

use Config\Services;

class Template
{
    /**
     * @var string
     */
    protected $themePath = 'themes';

    /**
     * @var string
     */
    private $_module = '';

    /**
     * @var string
     */
    private $_controller = '';

    /**
     * @var string
     */
    private $_method = '';

    /**
     * @var string
     */
    private $_theme = NULL;

    /**
     * @var string
     */
    private $_title = '';

    /**
     * @var bool
     */
    private $_layout = FALSE;

    /**
     * @var string
     */
    private $_layoutSubdir = '';

    /**
     * @var string
     */
    private $_titleSeparator = ' | ';

    /**
     * @var bool
     */
    private $_parserEnabled = false;

    /**
     * @var \CodeIgniter\Parser\Parser
     */
    protected $parser;

    /**
     * Template constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->parser = Services::parser();

        // Set any provided configuration properties
        if (!empty($config)) {
            foreach ($config as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }
}
