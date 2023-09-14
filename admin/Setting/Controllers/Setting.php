<?php
namespace Admin\Setting\Controllers;

use Admin\Banner\Models\BannerModel;
use Admin\Pages\Models\PagesModel;
use Admin\Setting\Models\SettingModel;
use Admin\Users\Models\UserGroupModel;
use Admin\Users\Models\UserModel;
use App\Controllers\AdminController;

class Setting extends AdminController
{
	private $error = array();
	private $settingModel, $userModel;
	function __construct()
	{
		$this->settingModel = new SettingModel();
		$this->userModel = new UserModel();
		$this->settings = config('settings');
	}

	public function index()
	{
		// Init
		$data = array();
		$this->template->set_meta_title(lang('Setting.heading_title'));
		$this->template->add_package(array('ckfinder', 'colorbox', 'select2'), true);

		$data['heading_title'] = lang('Setting.heading_title');
		$data['button_save'] = lang('Setting.button_save');
		$data['button_cancel'] = lang('Setting.button_cancel');


		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('banner.heading_title'),
			'href' => admin_url('banner')
		);


		if ($this->request->getMethod(1) === 'POST' && $this->validateSetting()) {

			$this->settingModel->editSetting('config', $this->request->getPost());
			$this->session->setFlashdata('message', 'Settings Saved Successfully.');
			return redirect()->to(current_url());

		}


		$data['action'] = admin_url('setting');
		$data['cancel'] = admin_url('setting');

		if (isset($this->error['warning'])) {
			$data['error'] = $this->error['warning'];
		}

		if ($this->request->getMethod(1) != 'POST') {
			$user_info = $this->userModel->find(1);
		}

		$data['config_background_position'] = '';
		$data['config_background_repeat'] = '';
		$data['config_background_attachment'] = '';
		$data['config_ftp_enable'] = '';
		$data['config_ssl'] = '';
		$data['config_date_format'] = '';
		$data['config_time_format'] = '';
		$data['config_seo_url'] = '';
		$data['config_maintenance_mode'] = '';
		$data['config_display_error'] = '';
		$data['config_log_error'] = '';
		foreach ($this->settingModel->where('module', 'config')->findAll() as $row) {
			$field = $row->key;
			$value = $row->value;

			if ($this->request->getPost($field)) {
				$data[$field] = $this->request->getPost($field);
			} else if (isset($this->settings->{$field})) {
				$data[$field] = $this->settings->{$field};
			} else {
				$data[$field] = '';
			}
		}

		if ($this->request->getPost('config_site_logo') && is_file(DIR_UPLOAD . $this->request->getPost('config_site_logo'))) {
			$data['thumb_logo'] = resize($this->request->getPost('config_site_logo'), 100, 100);
		} elseif ($this->settings->config_site_logo && is_file(DIR_UPLOAD . $this->settings->config_site_logo)) {
			$data['thumb_logo'] = resize($this->settings->config_site_logo, 100, 100);
		} else {
			$data['thumb_logo'] = resize('no_image.png', 100, 100);
		}

		if ($this->request->getPost('config_site_icon') && is_file(DIR_UPLOAD . $this->request->getPost('config_site_icon'))) {
			$data['thumb_icon'] = resize($this->request->getPost('config_site_icon'), 100, 100);
		} elseif ($this->settings->config_site_icon && is_file(DIR_UPLOAD . $this->settings->config_site_icon)) {
			$data['thumb_icon'] = resize($this->settings->config_site_icon, 100, 100);
		} else {
			$data['thumb_icon'] = resize('no_image.png', 100, 100);
		}

		$data['no_image'] = resize('no_image.png', 100, 100);


		$data['front_themes'] = $this->template->get_themes();

		$front_theme = $this->settings->config_front_theme ? $this->settings->config_front_theme : 'default';

		$data['front_templates'] = $this->template->get_theme_layouts($front_theme);

		if ($this->request->getPost('config_header_image') && is_file(DIR_UPLOAD . $this->request->getPost('config_header_image'))) {
			$data['thumb_header_image'] = resize($this->request->getPost('config_header_image'), 100, 100);
		} elseif ($this->settings->config_header_image && is_file(DIR_UPLOAD . $this->settings->config_header_image)) {
			$data['thumb_header_image'] = resize($this->settings->config_header_image, 100, 100);
		} else {
			$data['thumb_header_image'] = resize('no_image.png', 100, 100);
		}


		if ($this->request->getPost('background_image') && is_file(DIR_UPLOAD . $this->request->getPost('background_image'))) {
			$data['thumb_background_image'] = resize($this->request->getPost('background_image'), 100, 100);
		} elseif ($this->settings->config_background_image && is_file(DIR_UPLOAD . $this->settings->config_background_image)) {
			$data['thumb_background_image'] = resize($this->settings->config_background_image, 100, 100);
		} else {
			$data['thumb_background_image'] = resize('no_image.png', 100, 100);
		}



		//$this->load->helper('date');
		//printr(tz_list());
		$data['timezone'] = tz_list();
		//printr($data['timezone']);
		echo $this->template->view('Admin\Setting\Views\setting', $data);

	}

	public function serverinfo()
	{

		$data = array();
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => "Server Info",
			'href' => admin_url('setting/serverinfo')
		);
		$this->template->add_stylesheet(theme_url('assets/css/serverinfo.css'));
		$data['heading_title'] = "Server Info";
		ob_start();
		phpinfo();
		$pinfo = ob_get_contents();
		ob_end_clean();
		$data['phpinfo'] = preg_replace('%^.<body>(.)</body>.*$%ms', '$1', $pinfo);

		echo $this->template->view('Admin\Setting\Views\serverinfo', $data);
	}

	public function dashboard_old()
	{
		$data = array();
		$this->template->set_meta_title(lang('Setting.dashboard_title'));

		$data['heading_title'] = lang('Setting.dashboard_title');

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Setting.dashboard_title'),
			'href' => admin_url('Setting/dashboard')
		);

		$data['dreports'] = $this->settingModel->getDashboardReports(1);
		$data['dmreports'] = $this->settingModel->getDashboardReports(2);
		//printr($data['dreports']);

		$usergroupModel = new UserGroupModel();
		$data['roles'] = $usergroupModel->getAll();
		// printr($data['roles']);
		echo $this->template->view('Admin\Setting\Views\dashboard', $data);

	}

	public function dashboard()
	{
		$data = array();
		$this->template->set_meta_title(lang('Setting.dashboard_title'));

		$data['heading_title'] = lang('Setting.dashboard_title');

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Setting.dashboard_title'),
			'href' => admin_url('Setting/dashboard')
		);

		$data['reports'] = $this->settingModel->getDashboardReports();
		//printr($data['dreports']);

		//$usergroupModel=new UserGroupModel();
		//$data['roles']=$usergroupModel->getAll();
		// printr($data['roles']);
		echo $this->template->view('Admin\Setting\Views\dashboard', $data);

	}

	public function save_dashboard($d_id = null, $flag = null)
	{
		if ($this->request->isAJAX()) {

			$report_menu_id = json_decode($this->request->getPost('report_menu'));
			if (!empty($d_id)) {
				if ($flag == 'f') {
					$action = array('dstatus' => $this->request->getPost('status'));
				} else if ($flag == 'b') {
					$action = array('status' => $this->request->getPost('status'));
				} else if ($flag == 'p') {
					//$action = array('for_staff' => $ex[1]);
					$role_ids = $this->request->getPost('role_ids');
					$role_permission = [];
					foreach ($role_ids as $role_id) {
						$role_permission[$role_id] = 1;
					}
					$action = array('permission' => json_encode($role_permission));
				} else {
					$action = array('col' => $this->request->getPost('col'));
				}
				$this->settingModel->saveDashboard($d_id, $action);
			}
			$type = "success";
			$message = "Dashboard Setting Successfully";
			echo json_encode(array('status' => $type, 'message' => $message));
			exit();
		} else {
			return redirect()->to(current_url());
		}
	}
	public function validateSetting()
	{
		$validation = \Config\Services::validation();

		$rules = $this->settingModel->validationRules;

		if ($this->validate($rules)) {
			return true;
		} else {
			//printr($validation->getErrors());
			$this->error['warning'] = "Warning: Please check the form carefully for errors!";
			return false;
		}
		return !$this->error;
	}

}
/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */