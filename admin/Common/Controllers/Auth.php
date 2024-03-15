<?php

namespace Admin\Common\Controllers;

use App\Controllers\AdminController;
use App\Libraries\User;
use CodeIgniter\HTTP\CURLRequest;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Session\Session;

class Auth extends AdminController
{
    /**
     * Auth constructor.
     */
    public function __construct()
    {
        $this->session = service('session');
        $this->user = new User();
    }

    /**
     * Login method for user authentication.
     *
     * @return RedirectResponse|void
     */
    public function login(): void
    {
        $data['login_error'] = '';

        if ($this->user->isLogged()) {
            return redirect()->to('/admin');
        }

        if ($this->request->getMethod('post')) {
            $logged_in = $this->user->login(
                $this->request->getPost('username'),
                $this->request->getPost('password')
            );

            if ($logged_in) {
                $redirect = $this->request->getPost('redirect') ?? '';

                if ($redirect && strpos(uri_string(), admin_url()) === 0) {
                    return redirect()->to($redirect);
                } else {
                    return redirect()->to(site_url('admin'));
                }
            } else {
                $data['login_error'] = $this->session->getFlashdata('error') ?? '';
            }
        }

        $data['redirect'] = uri_string();

        echo $this->template->view('Admin\Common\Views\login', $data);
    }

    /**
     * Logout method for user logout.
     *
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        $this->user->logout();
        $this->session->remove('redirect');

        return redirect()->to('/admin');
    }

    /**
     * ReLogin method for re-login functionality.
     *
     * @return RedirectResponse
     */
    public function reLogin(): RedirectResponse
    {
        $user = $this->session->get('temp_user');

        if ($user) {
            $this->user->assignUserAttr($user);
            $this->session->set('user', $user);
            $this->session->remove('temp_user');

            return redirect()->to(site_url('admin'));
        }

        return redirect()->to('/admin');
    }

    /**
     * OldPortalLogin method for old portal login.
     *
     * @return RedirectResponse
     */
    public function oldPortalLogin(): RedirectResponse
    {
        $client = service('curlrequest');

        $response = $client->request('POST', 'https://soe1.milletsodisha.com/api/user/username', [
            'headers' => [
                'x-api-key' => "4o8c0ow0wooss4kswgwwcs4444swk0oc44gwc8gs",
            ],
            'form_params' => [
                'username' => $this->user->getUserName(),
            ]
        ]);

        $body = $response->getBody();

        if (strpos($response->getHeaderLine('content-type'), 'application/json') !== false) {
            $body = json_decode($body);
        }

        if (isset($body->status) && $body->status) {
            $id = $body->user->id;
            $password
