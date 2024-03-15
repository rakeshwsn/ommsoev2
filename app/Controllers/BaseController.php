<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\URI;
use CodeIgniter\Log\LoggerInterface;
use Config\Services;
use Config\Settings;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
class BaseController extends Controller
{
    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected array $helpers = ['aio', 'form', 'shortcode', 'function', 'number'];

    public array $statuses = [
        'Not Approved',
        'Approved',
        'Rejected',
        'Not Uploaded',
        'Not Required',
    ];

    public array $colors = [
        'warning',
        'success',
        'secondary',
        'danger',
        'primary',
    ];

    protected \CodeIgniter\Template\Template $template;

    protected Settings $settings;

    protected \App\Models\UserModel $user;

    protected \CodeIgniter\Session\Session $session;

    protected URI $uri;

    /**
     * Constructor.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param LoggerInterface   $logger
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->settings = new Settings();

        $this->template = new \CodeIgniter\Template\Template();
        $this->template->set('home', false);
        $this->user = new \App\Models\UserModel();
        $this->session = Services::session();
        $this->uri = Services::uri();

        $this->template->setTheme('default');
    }

    public function _remap($method, ...$params)
    {
        $router = Services::router();

        if (method_exists($this, $method)) {
            return $this->$method(...$params);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
}
