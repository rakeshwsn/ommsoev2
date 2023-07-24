<?php
namespace Admin\CropCoverage\Controllers;

use App\Controllers\AdminController;
use Admin\CropCoverage\Models\TargetModel;
use Admin\CropCoverage\Models\CropsModel;
use Admin\CropCoverage\Models\PracticesModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;

class CropCoverageReports extends AdminController
{
    private $error = array();
    private $targetModel;
    private $blockModel;
    private $districtModel;
    private $cropsModel;
    private $practicesModel;
    function __construct()
    {
        $this->targetModel = new TargetModel();
        $this->blockModel = new BlockModel();
        $this->districtModel = new DistrictModel();
        $this->cropsModel = new CropsModel;
        $this->practicesModel = new PracticesModel;
    }
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

        $this->template->add_package(array('datatable', 'select2'), true);

        // $data['add'] = admin_url('areacoverage/target/add');
        $data['edit'] = admin_url('areacoverage/target/edit');
        $data['delete'] = admin_url('grampanchayat/delete');
        $data['datatable_url'] = admin_url('grampanchayat/search');

        $data['heading_title'] = lang('Area Coverage Approval');

        $data['text_list'] = lang('Grampanchayat.text_list');
        $data['text_no_results'] = lang('Grampanchayat.text_no_results');
        $data['text_confirm'] = lang('Grampanchayat.text_confirm');

        $data['button_add'] = lang('Add Target');
        $data['button_edit'] = lang('Edit Target');
        $data['button_delete'] = lang('Grampanchayat.button_delete');

        if (isset($this->error['warning'])) {
            $data['error'] = $this->error['warning'];
        }

        if ($this->request->getGet('district_id')) {
            $data['district_id'] = (array) $this->request->getGet('district_id');
        } else {
            $data['district_id'] = $this->user->district_id;
        }

        $croppractices = $this->targetModel->getPractices();
        // printr($croppractices);
        // exit;
        $practicedata = $this->targetModel->getAll([
            'district_id' => $data['district_id']
        ]);

        $data['practicedata'] = $practicedata;


        $data['year_id'] = date('Y');
        $currentMonth = date('n');
        if ($currentMonth >= 6 && $currentMonth <= 10) {
            $season = 'Kharif';
        } elseif ($currentMonth >= 11 && $currentMonth <= 4) {
            $season = 'Rabi';
        }
        $data['season'] = $season;

        //for heading
        $crops = [];
        foreach ($croppractices as $cp) {
            $_crops = $cp['crops'];

            if (!isset($crops[$_crops])) {
                $crops[$_crops] = array();
            }

            $crops[$_crops][] = $cp['practice'];
        }

        $data['heading'] = $crops;


        return $this->template->view('Admin\CropCoverage\Views\cropcoverage_reports', $data);
    }
}
?>