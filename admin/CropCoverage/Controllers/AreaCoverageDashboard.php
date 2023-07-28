<?php
namespace Admin\CropCoverage\Controllers;

use Admin\Localisation\Models\BlockModel;
use App\Controllers\AdminController;

class AreaCoverageDashboard extends AdminController
{
    public function index()
    {
        $data = [];
        return $this->template->view('Admin\CropCoverage\Views\areacoverage_dashboard', $data);
    }
}