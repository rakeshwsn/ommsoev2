<?php

class ControllerExtensionDashboardCustomer extends Controller {

    /**
     * @var array
     */
    private $error = [];

    /**
     * Index action for the dashboard customer page.
     */
    public function index() {
        $this->load->language('extension/dashboard/customer');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            $this->model_setting_setting->editSetting('dashboard_customer', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true));
        }

        $this->loadErrorMessages();

        $data['breadcrumbs'] = [
            [
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
            ],
            [
                'text' => $this->language->get('text_extension'),
                'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true)
            ],
            [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/dashboard/customer', 'user_token=' . $this->session->data['user_token'], true)
            ],
        ];

        $data['action'] = $this->url->link('extension/dashboard/customer', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true);

        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['columns'] = [];
        for ($i = 3; $i <= 12; $i++) {
            $data['columns'][] = $i;
        }

        $this->loadSettings($data);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/dashboard/customer_form', $data));
    }

    /**
     * Load error messages if any.
     */
    private function loadErrorMessages() {
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
    }

    /**
     * Load settings for the form.
     *
     * @param array $data
     */
    private function loadSettings(&$data) {
        if (isset($this->request->post['dashboard_customer_width'])) {
            $data['dashboard_customer_width'] = $this->request->post['dashboard_customer_width'];
        } else {
            $data['dashboard_customer_width'] = $this->config->get('dashboard_customer_width');
        }

        if (isset($this->request->post['dashboard_customer_status'])) {
            $data['dashboard_customer_status'] = $this->request->post['dashboard_customer_status'];
        } else {
            $data['dashboard_customer_status'] = $this->config->get('dashboard_customer_status');
        }

        if (isset($this->request->post['dashboard_customer_sort_order'])) {
            $data['dashboard_customer_sort_order'] = $this->request->post['dashboard_customer_sort_order'];
        } else {
            $data['dashboard_customer_sort_order'] = $this->config->get('dashboard_customer_sort_order');
        }
    }

    /**
     * Validate the user's permission.
     *
     * @return bool
     */
    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/dashboard/customer')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    /**
     * Dashboard action for displaying customer information.
     */
    public function dashboard() {
        $this->load->language('extension/dashboard/customer');

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('customer/customer');

        $today = $this->model_customer_customer->getTotalCustomers(
            [
                'filter_date_added' => date('Y-m-d', strtotime('-1 day')),
            ]
        );

        $yesterday = $this->model_customer_customer->getTotalCustomers(
            [
                'filter_date_added' => date('Y-m-d', strtotime('-2 day')),
            ]
        );

        $difference = $today - $yesterday;

        if ($difference && $today) {
            $data['percentage'] = round(($difference / $today) * 100);
        } else {
            $data['percentage'] = 0;
        }

        // Added missing variable name
        $customer_data = $this->model_customer_customer->getTotalCustomers();
        $data['total
