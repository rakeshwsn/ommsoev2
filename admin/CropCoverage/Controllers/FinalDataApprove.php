<?php
namespace Admin\CropCoverage\Controllers;

use App\Controllers\AdminController;
use Admin\CropCoverage\Models\AreaCoverageModel;
use Admin\Common\Models\YearModel;
use App\Libraries\Export;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Admin\CropCoverage\Models\CropsModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\GrampanchayatModel;
use Admin\CropCoverage\Models\FinalDataModel;
use Admin\CropCoverage\Models\FinalDataApproveModel;
use Admin\CropCoverage\Models\PracticesModel;
use Admin\CropCoverage\Models\TargetModel;
use Config\Url;

class FinalDataApprove extends AdminController
{
    public $statuses = ['Uploaded', 'Approved', 'Rejected', 'Not Uploaded', 'Not Required'];
    public $colors_ac = [
        'warning',
        'success',
        'danger',
        'secondary',
        'primary',
    ];
    private $acModel;
    private $data;
    private $error = array();
    private $targetModel;
    private $blockModel;
    private $districtModel;
    private $cropsModel;
    private $practicesModel;
    private $gpModel;
    private $fdModel;
    private $fdApproveModel;
    function __construct()
    {
        $this->acModel = new AreaCoverageModel();
        $this->blockModel = new BlockModel();
        $this->cropsModel = new CropsModel();
        $this->districtModel = new DistrictModel();
        $this->gpModel = new GrampanchayatModel();
        $this->fdModel = new FinalDataModel();
        $this->fdApproveModel = new FinalDataApproveModel();
        $this->practicesModel = new PracticesModel();
        $this->targetModel = new TargetModel();
    }
    public function Index()
    {
        if ($this->user->block_id) {
            return $this->block();
        } else if ($this->user->district_id || $this->request->getGet('district_id')) {
            return $this->district();
        } else {
            return $this->state();
        }
    }
    public function state()
    {
        $this->template->set_meta_title(lang('Crop Coverage|Final Data'));
        $data['heading_title'] = lang('Area Coverage Final Data Approval');
        $data['years'] = getAllYears();
        // $data['year_id'] = getCurrentYearId();
        $data['seasons'] = $this->acModel->getSeasons();
        $data['aftcurrent_season'] = strtolower(getAftCurrentSeason());
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');
        } elseif ($this->user->district_id) {
            $data['district_id'] = $this->user->district_id;
        } else {
            $data['district_id'] = 0;
        }
        $data['districts'] = ($this->districtModel)->orderBy('name')->asArray()->find();
        if ($data['district_id']) {
            $data['blocks'] = ($this->blockModel)->where('district_id', $data['district_id'])
                ->orderBy('name')->asArray()->findAll();
        }
        if ($this->request->getGet('year_id')) {
            $data['year_id'] = $this->request->getGet('year_id');
        } else {
            $data['year_id'] = getCurrentYearId();
        }
        $data['crop_practices'] = $this->acModel->getCropPractices();
        $crops = $this->cropsModel->findAll();
        $data['crops'] = [];
        foreach ($crops as $crop) {
            $data['crops'][$crop->id] = $crop->crops;
        }
        $data['block_id'] = '';
        if ($this->request->getGet('block_id')) {
            $data['block_id'] = $this->request->getGet('block_id');
        } elseif ($this->user->block_id) {
            $data['block_id'] = $this->user->block_id;
        } else {
            $data['block_id'] = 0;
        }
        $data['blocks'] = [];
        $filter = [
            'season' => $this->request->getGet('season') ?? getAftCurrentSeason(),
            'district_id' => $data['district_id'],
            'block_id' => $data['block_id'],
            'year_id' => $data['year_id']

        ];
        $data['get_blocks'] = Url::getBlocks;
        $blocks = $this->fdApproveModel->getAreaCoverageFinalReport($filter);
        // printr($blocks);
        // exit;
        foreach ($blocks as $key => $block) {
            $action = '';
            if ($block) {
                $href = admin_url('areacoverage/finaldata/approve/district?district_id=' . $block->id . '&season=' . $block->season);
                $action = '<a href="' . $href . '" class="btn btn-sm btn-info" data-toggle="tooltip" data-title="View">
                                            <i class="fa fa-list"></i></a>';

            }
            $blocks[$key]->action = $action;
            $status = ($block->status === '' || $block->status === null) ? 3 : $block->status;

            if (isset($this->statuses[$status])) {
                $blocks[$key]->status = $this->statuses[$status];
                $blocks[$key]->status_color = isset($this->colors[$status]) ? $this->colors[$status] : 'default_color';
            }
        }
        // printr($blocks);
        // exit;
        $data['blocksfd'] = $blocks;
        return $this->template->view('Admin\CropCoverage\Views\finaldata_approve_state', $data);
    }
    public function district()
    {
        $this->template->set_meta_title(lang('Crop Coverage|Final Data'));
        $data['heading_title'] = lang('Area Coverage Final Data Approval');
        $districts = (new DistrictModel())->findAll();
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $district_id = $this->request->getGet('district_id');
        } else {
            $data['district_id'] = $district_id = $this->user->district_id;
        }
        foreach ($districts as $district) {
            if ($district->id == $district_id) {
                $data['districts'][$district->id] = $district->name;
            }
        }
        $data['crop_practices'] = $this->acModel->getCropPractices();
        $crops = $this->cropsModel->findAll();
        $data['crops'] = [];
        foreach ($crops as $crop) {
            $data['crops'][$crop->id] = $crop->crops;
        }
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');
        } elseif ($this->user->district_id) {
            $data['district_id'] = $this->user->district_id;
        } else {
            $data['district_id'] = 0;
        }
        if ($this->request->getGet('season')) {
            $data['season'] = $season = $this->request->getGet('season');
        } else {
            $data['season'] = $season = getAftCurrentSeason();
        }
        $filter = [
            'season' => $data['season'],
            'district_id' => $data['district_id'],

        ];
        if ($this->request->getMethod(1) == 'POST') {
            // echo "<pre>";
            // print_r($_POST);
            // echo "</pre>";
            // exit;
            $status = [
                'status' => $this->request->getPost('status'),
                'remarks' => $this->request->getPost('remarks'),
            ];
            $this->fdModel->where('district_id', $district_id)
                ->where('season', $season)->set($status)->update();

            return redirect()->to(admin_url('areacoverage/finaldata/approve/district?district_id=' . $district_id . '&season=' . $season))
                ->with('message', 'Status has been updated.');
        }
        $blocks = $this->fdApproveModel->getAreaCoverageFinalReport($filter);
        // printr($blocks);
        // exit;
        foreach ($blocks as $key => $block) {
            $status = ($block->status === '' || $block->status === null) ? 3 : $block->status;

            if (isset($this->statuses[$status])) {
                $blocks[$key]->status = $this->statuses[$status];
                $blocks[$key]->status_color = isset($this->colors[$status]) ? $this->colors[$status] : 'default_color';
            }
        }
        $data['show_approval'] = false;
        if ($this->user->district_id != $district_id) {

            $data['show_approval'] = true;
            $data['reject_url'] = admin_url('areacoverage/finaldata/approve/reject');

            $form_data = $this->getForm();

            $data['approve_form'] = view('\Admin\Transaction\Views\approve_form', $form_data);

        }
        // printr($blocks);
        // exit;
        $data['blocksfd'] = $blocks;
        $district_status = $this->fdModel->where('district_id', $district_id)->first();


        $data['status'] = '';
        $data['remarks'] = '';
        $data['status_color'] = '';
        if ($district_status) {
            $data['status'] = $this->statuses[$district_status->status];
            $data['status_color'] = $this->colors[$district_status->status];
            $data['remarks'] = $district_status->remarks;
        }

        return $this->template->view('Admin\CropCoverage\Views\finaldata_approve_district', $data);
    }
    protected function getForm()
    {
        $data['statuses'] = [
            [
                'id' => 1,
                'name' => 'Approved',
            ],
            [
                'id' => 2,
                'name' => 'Rejected',
            ],
        ];
        $data['title'] = 'Approve area coverage';
        $data['status_id'] = 0;
        $data['remarks'] = '';
        return $data;
    }
}
?>