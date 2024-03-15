<?php

// ControllerExtensionDashboardOrder.php
class ControllerExtensionDashboardOrder extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/dashboard/order');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('dashboard_order', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true));
        }

        $this->loadSettings();

        $data['action'] = $this->url->link('extension/dashboard/order', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/dashboard/order_form', $data));
    }

    private function loadSettings() {
        if (isset($this->request->post['dashboard_order_width'])) {
            $this->data['dashboard_order_width'] = $this->request->post['dashboard_order_width'];
        } else {
            $this->data['dashboard_order_width'] = $this->config->get('dashboard_order_width');
        }

        $this->data['columns'] = array();

        for ($i = 3; $i <= 12; $i++) {
            $this->data['columns'][] = $i;
        }

        if (isset($this->request->post['dashboard_order_status'])) {
            $this->data['dashboard_order_status'] = $this->request->post['dashboard_order_status'];
        } else {
            $this->data['dashboard_order_status'] = $this->config->get('dashboard_order_status');
        }

        if (isset($this->request->post['dashboard_order_sort_order'])) {
            $this->data['dashboard_order_sort_order'] = $this->request->post['dashboard_order_sort_order'];
        } else {
            $this->data['dashboard_order_sort_order'] = $this->config->get('dashboard_order_sort_order');
        }
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/dashboard/order')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}

// ControllerExtensionDashboard.php
class ControllerExtensionDashboard extends Controller {
    public function order() {
        $this->load->language('extension/dashboard/order');

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('sale/order');

        $data['total'] = $this->getOrderTotal();

        $data['order'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true);

        return $this->load->view('extension/dashboard/order_info', $data);
    }

    private function getOrderTotal() {
        $order_total = $this->model_sale_order->getTotalOrders();

        if ($order_total > 1000000000000) {
            return round($order_total / 1000000000000, 1) . 'T';
        } elseif ($order_total > 1000000000) {
            return round($order_total / 1000000000, 1) . 'B';
        } elseif ($order_total > 1000000) {
            return round($order_total / 1000000, 1) . 'M';
        } elseif ($order_total > 1000) {
            return round($order_total / 1000, 1) . 'K';
        } else {
            return $order_total;
        }
    }
}
