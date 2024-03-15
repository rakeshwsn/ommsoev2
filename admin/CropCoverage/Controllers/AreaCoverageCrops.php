<?php
namespace Admin\CropCoverage\Controllers;

use App\Controllers\AdminController;
use Admin\CropCoverage\Models\CropsModel;

class AreaCoverageCrops extends AdminController
{
    private $data;
    private $error = array();
    private $cropsmodel;
    function __construct()
    {
        $this->cropsmodel = new CropsModel();
    }
    public function Index()
    {

        if ($this->request->getMethod(1) === 'POST') {

            $this->cropsmodel->AddCrops($this->request->getPost());
            $this->session->setFlashdata('message', 'Crops Added Successfully');

        }

        $data['heading_title'] = lang('Add Crops');

        $data['crops'] = $this->cropsmodel->GetCrops();

        return $this->template->view('Admin\CropCoverage\Views\crops', $data);
    }

}
?>