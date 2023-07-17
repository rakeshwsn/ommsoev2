<?php
namespace Admin\CropCoverage\Controllers;

use App\Controllers\AdminController;

class AreaCoverageApproval extends AdminController
{
    private $error = array();
    public function index()
    {
        $this->template->set_meta_title(lang('Grampanchayat.heading_title'));
        return $this->getList();
    }
    protected function getList()
    {
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => lang('Grampanchayat.heading_title'),
            'href' => admin_url('grampanchayat')
        );
        $data['heading_title'] = lang('Area Coverage Approval');



        return $this->template->view('Admin\CropCoverage\Views\areacoverageapproval', $data);
    }
}
?>