<?php

namespace App\Libraries;

use Config\Services;

class Template
{

    /**
     * @var string
     */
    public $theme_path = 'themes';

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
     * @var null
     */
    private $_theme = NULL;

    /**
     * @var null
     */
    private $_theme_path = NULL;

    /**
     * @var bool
     */
    private $_layout = FALSE;

    /**
     * @var string
     */
    private $_layout_subdir = '';

    /**
     * @var string
     */
    private $_title = '';

    /**
     * @var string
     */
    private $_meta_title;

    /**
     * @var string
     */
    private $_meta_description;

    /**
     * @var string
     */
    private $_meta_keywords;

    /**
     * @var string
     */
    private $_meta_icon;

    /**
     * @var array
     */
    private $_metadata = array();

    /**
     * @var array
     */
    private $_partials = array();

    /**
     * @var array
     */
    private $_breadcrumbs = array();

    /**
     * @var string
     */
    private $_title_separator = ' | ';

    /**
     * @var bool
     */
    private $_parser_enabled = false;

    /**
     * @var bool
     */
    private $_parser_body_enabled = TRUE;

    /**
     * @var array
     */
    private $_theme_locations = array();

    /**
     * @var bool
     */
    private $_is_mobile = FALSE;

    /**
     * @var int
     */
    private $_cache_lifetime = 0;

    /**
     * @var object
     */
    private $_ci;

    /**
     * @var object
     */
    private $renderer;

    /**
     * @var array
     */
    private $_data = array();

    /**
     * @var bool
     */
    private $_headers_sent = FALSE;

    /**
     * @var string
     */
    private $_page_head = '';

    /**
     * @var array
     */
    private $_template_data = array();

    /**
     * @var array
     */
    private $_javascripts = array();

    /**
     * @var array
     */
    private $_scripts = array();

    /**
     * @var array
     */
    private $_stylesheets = array();

    /**
     * @var array
     */
    private $_css = array();

    /**
     * @var array
     */
    private $_header_js_order = array();

    /**
     * @var array
     */
    private $_footer_js_order = array();

    /**
     * @var array
     */
    private $_css_order = array();

    /**
     * Template constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct();

        $this->parser = Services::parser();

