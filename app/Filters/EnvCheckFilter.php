<?php

namespace App\Filters;

use App\Libraries\TelegramBot;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class EnvCheckFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        //check env file for CI_ENVIRONMENT. if CI_ENVIRONMENT is development then log error
        $env = getenv('CI_ENVIRONMENT');

        if ($env === 'development' && base_url() == 'https://soe.milletsodisha.com/') {
            (new TelegramBot())->sendMessage(-1002046427606, 'Development mode is active in production.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No need to do anything after the request
    }

}
