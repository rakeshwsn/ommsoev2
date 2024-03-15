<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\URI;
use CodeIgniter\Log\LoggerInterface;
use CodeIgniter\Session\Session;
use CodeIgniter\Template\Template;
use CodeIgniter\View\View;
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

    protected array $statuses = [
        'Not Approved',
        'Approved',
        'Rejected',
        'Not Uploaded',
        'Not Required',
    ];

    protected array $colors = [
        'warning',
        'success',
        'secondary',
        'danger',
        'primary',
    ];

    protected ?Template $template;

    protected Settings $settings;

    protected \App\Models\UserModel $user;

    protected ?Session $session;

    protected ?URI $uri;

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

        $this->template = new Template();
        $this->template->set('home', false);
        $this->user = new \App\Models\UserModel();
        $this->session = Services::session();
        $this->uri = Services::uri();

        $this->template->setTheme('default');
    }

    /**
     * Magic method to handle method calls not found in the controller.
     *
     * @param string $method
     * @param array $params
     *
     * @return mixed
     */
    public function __call(string $method, array $params)
    {
        $router = Services::router();

        if (method_exists($this, $method)) {
            return $this->$method(...$params);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    /**
     * Magic method to handle calls to inaccessible methods or properties.
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->$name ?? null;
    }

    /**
     * Magic method to set a value to inaccessible properties.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }
}
