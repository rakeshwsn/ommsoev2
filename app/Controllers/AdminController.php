<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Session\Session;
use Config\Services;

/**
 * Class AdminController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
class AdminController extends Controller
{
    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['aio', 'form', 'date', 'general', 'number'];

    public $statuses = [
        'Not Approved',
        'Approved',
        'Rejected',
        'Not Uploaded',
        'Not Required'
    ];

    public $colors = [
        'warning',
        'success',
        'secondary',
        'danger',
        'primary',
    ];

    protected $template;

    protected $user;

    protected $session;

    /**
     * Constructor.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param LoggerInterface   $logger
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::__construct($request, $response, $logger);

        $this->settings = new \Config\Settings();
        $this->template = Services::template();
        $this->user = Services::user();
        $this->session = Services::session();
        $this->uri = Services::uri();

        $this->template->setTheme('admin');
        $this->template->set('header', true);
        $this->template->set('site_name', $this->settings->config_site_title);

        $ckfinderData = [
            'root' => WRITEPATH,
            'baseUrl' => base_url('writable/uploads')
        ];

        $_SESSION['ckfinder'] = $ckfinderData;
    }

    public function _remap($method, ...$params)
    {
        $router = Services::router();

        if (!$this->user->checkPermission()) {
            show_404();
        } else if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $params);
        } else {
            show_404();
        }
    }
}
