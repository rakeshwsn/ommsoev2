<?php
namespace Admin\CropCoverage\Controllers;

use Admin\Common\Models\YearModel;
use App\Libraries\Export;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Admin\CropCoverage\Models\AreaCoverageModel;
use App\Controllers\AdminController;
use Admin\CropCoverage\Models\CropsModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\GrampanchayatModel;
use Admin\CropCoverage\Models\FinalDataModel;
use Admin\CropCoverage\Models\FinalDataDocModel;
use Admin\CropCoverage\Models\PracticesModel;
use Admin\CropCoverage\Models\TargetModel;
use Config\Url;

class AreaCoverageFinalData extends AdminController
{
    private $data;
    private $error = array();
    private $targetModel;
    private $blockModel;
    private $districtModel;
    private $cropsModel;
    private $practicesModel;
    private $acModel;
    private $fdDocModel;

    private $gpModel;
    private $fdModel;

    function __construct()
    {
        $this->acModel = new AreaCoverageModel();
        $this->blockModel = new BlockModel();
        $this->cropsModel = new CropsModel();
        $this->districtModel = new DistrictModel();
        $this->gpModel = new GrampanchayatModel();
        $this->fdModel = new FinalDataModel();
        $this->fdDocModel = new FinalDataDocModel();
        $this->practicesModel = new PracticesModel();
        $this->targetModel = new TargetModel();

    }
    public function Index()
    {
        $this->template->set_meta_title(lang('Crop Coverage|Final Data'));

        return $this->getList();
    }
    protected function getList($action = '')
    {
        $data['permission_add'] = $this->user->agency_type_id;
        $data['heading_title'] = lang('Area Coverage Final Data');
        $data['years'] = getAllYears();
        // $data['year_id'] = getCurrentYearId();
        $data['seasons'] = $this->acModel->getSeasons();
        $data['aftcurrent_season'] = strtolower(getAftCurrentSeason());
        // printr($data['current_season']);
        // exit;
        // $data['uncurrent_season'] = strtolower(getUnCurrentSeason());
        // printr($data['uncurrent_season']);
        // exit;
        $data['get_blocks'] = Url::getBlocks;

        $data['district_id'] = '';
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $district_id = $this->request->getGet('district_id');
        } elseif ($this->user->district_id) {
            $data['district_id'] = $district_id = $this->user->district_id;
        } else {
            $data['district_id'] = $district_id = 0;
        }
        $data['blocks'] = [];

        if ($data['district_id']) {
            $data['blocks'] = ($this->blockModel)->where('district_id', $data['district_id'])
                ->orderBy('name')->asArray()->findAll();
        }
        $data['block_id'] = '';
        if ($this->request->getGet('block_id')) {
            $data['block_id'] = $this->request->getGet('block_id');
        } elseif ($this->user->block_id) {
            $data['block_id'] = $this->user->block_id;
        } else {
            $data['block_id'] = 0;
        }
        if ($this->request->getGet('year_id')) {
            $data['year_id'] = $this->request->getGet('year_id');
        } else {
            $data['year_id'] = getCurrentYearId();
        }
        $params = 'year_id=' . $this->request->getGet('yaer_id') ?? $data['year_id'];
        $params .= '&season=' . $data['aftcurrent_season'];
        $params .= '&district_id=' . $data['district_id'];
        $params .= '&block_id=' . $data['block_id'];
        $data['download_url'] = admin_url('areacoverage/finaldata/download?' . $params);
        $data['crop_practices'] = $this->acModel->getCropPractices();
        $crops = $this->cropsModel->findAll();
        $data['crops'] = [];
        foreach ($crops as $crop) {
            $data['crops'][$crop->id] = $crop->crops;
        }
        $data['districts'] = ($this->districtModel)->orderBy('name')->asArray()->find();
        // printr($data['districts']);
        // exit;
        $data['add'] = admin_url('areacoverage/finaldata/add');

        //for area coverage report
        // if ($this->user->block_id) {
        //     $data['block_id'] = $filter['block_id'] = $this->user->block_id;
        //     $data['districts'] = ($this->districtModel)->where('id', $this->user->district_id)->asArray()->find();
        if ($this->user->district_id) {
            $data['district_id'] = $filter['district_id'] = $this->user->district_id;
            $data['districts'] = ($this->districtModel)->where('id', $this->user->district_id)->asArray()->find();
        } else {
            $data['districts'] = ($this->districtModel)->orderBy('name')->asArray()->find();
        }
        $filter = [
            'season' => $this->request->getGet('season') ?? getAftCurrentSeason(),
            'district_id' => $data['district_id'],
            'block_id' => $data['block_id'],
            'year_id' => $data['year_id']

        ];
        // printr($filter);
        // exit;


        $blocks = $this->fdModel->getAreaCoverageFinalReport($filter);
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
        // printr($data['blocksfd']);
        // exit;

        $data['blocks'] = [];
        if ($data['district_id']) {
            $data['blocks'] = (new BlockModel())->where('district_id', $data['district_id'])
                ->orderBy('name')->asArray()->findAll();
        }
        // echo $data['district_id'];
        // exit;

        if ($action == 'download') {
            $data['fin_year'] = (new YearModel())->find($data['year_id'])->name;
            $data['table'] = view('Admin\CropCoverage\Views\finaldata_table', $data);
            $filename = 'AreaCoverageFinalReport_' . $data['current_season'] . '_' . $data['fin_year'] . '_' . date('Y-m-d His') . '.xlsx';

            $spreadsheet = Export::createExcelFromHTML($data['table'], $filename, true);
            if ($spreadsheet) {
                $worksheet = $spreadsheet->getActiveSheet();
                $wordWrapCols = [
                    'A1',
                    'D1',
                    'E1',
                    'F1',
                    'G1',
                    'M2',
                    'O2',
                    'P2',
                    'Q2',
                    'R1',
                    'S1',
                    'T1',
                    'U1'
                ];
                foreach ($wordWrapCols as $col) {
                    $cell = $worksheet->getCell($col);
                    $cell->getStyle()->getAlignment()->setWrapText(true);
                }

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $filename . '"');
                header('Cache-Control: max-age=0');

                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }

            exit;
        }

        return $this->template->view('Admin\CropCoverage\Views\areacoverage_final_data', $data);
    }


    protected function getForm()
    {
        $this->template->add_package(array('datatable', 'select2', 'uploader', 'jquery_loading'), true);

        $data = [];
        $data['seasons'] = $this->acModel->getSeasons();
        // $data['aftcurrentseason'] = getAftCurrentSeason();
        // printr($data['aftcurrentseason']);
        // exit;
        $data['years'] = getAllYears();
        $data['year_id'] = getCurrentYearId();
        $block_id = $this->request->getGet('block_id');
        $year_id = getCurrentYearId();
        $season = getAftCurrentSeason();

        $crops = $this->cropsModel->findAll();
        $data['crops'] = $crops;
        foreach ($crops as $crop) {
            $data['crops'][$crop->id] = $crop->crops;
        }
        $data['crop_practices'] = $this->acModel->getCropPractices();
        $data['district_id'] = $this->user->district_id;
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = (array) $this->request->getGet('district_id');
        }
        $data['blocks'] = [];
        if ($data['district_id']) {
            $data['blocks'] = ($this->blockModel)->where('district_id', $data['district_id'])
                ->orderBy('name')->asArray()->findAll();
        }
        $data['block_id'] = $this->request->getGet('block_id', null);
        $data['year_id'] = getCurrentYearId();
        $data['aftcurrent_season'] = getAftCurrentSeason();
        $filter = [
            'year_id' => $data['year_id'],
            'season' => $data['aftcurrent_season'],
            'block_id' => $data['block_id']
        ];


        $data['gpsfinaldata'] = $this->fdModel->getGpsFinalData($filter);

        foreach ($data['gpsfinaldata'] as &$gpsfinal) {
            $final_data_id = $gpsfinal['id'];

            // printr($final_data_id);
            // exit;
            $gps_demon_data = $this->fdModel->getGpsDemonData($final_data_id);

            // printr($gps_demon_data);
            // exit;
            foreach ($gps_demon_data as &$crop) {

                $practice = $data['crop_practices'][$crop['id']];


                if (!in_array('smi', $practice)) {
                    unset($crop['smi']);
                }
                if (!in_array('lt', $practice)) {
                    unset($crop['lt']);
                }
                if (!in_array('ls', $practice)) {
                    unset($crop['ls']);
                }
            }
            $gpsfinal['crops_data'] = $gps_demon_data;
        }

        // printr($data['filename']);
        // exit;



        if ($this->request->getMethod(1) === 'POST') {
            $data['block_id'] = $block_id;
            $gpMasterData = [];
            // printr($_POST);
            // exit;
            $gpsfinaldata = $this->request->getPost('area');
            // printr($gpsfinaldata);
            // exit;
            foreach ($gpsfinaldata as &$gpdata) {
                // printr($gpdata);
                // exit;
                $gpMasterData = [
                    "year_id" => getCurrentYearId(),
                    "season" => getAftCurrentSeason(),
                    "district_id" => $this->user->district_id,
                    "block_id" => $block_id,
                    "gp_id" => $gpdata['gp_id'],
                    "no_of_village" => $gpdata['no_of_village'],
                    "farmers_covered_under_demonstration" => $gpdata['farmers_covered_under_demonstration'],
                    "farmers_covered_under_followup" => $gpdata['farmers_covered_under_followup'],
                    "fup_ragi" => $gpdata['fup_ragi'],
                    "fup_lm" => $gpdata['fup_lm'],
                    "fup_fm" => $gpdata['fup_fm'],
                    "fup_sorghum" => $gpdata['fup_sorghum'],
                    "fup_km" => $gpdata['fup_km'],
                    "fup_bm" => $gpdata['fup_bm'],
                    "fup_pm" => $gpdata['fup_pm'],
                ];

                if ($gpdata['master_id']) {
                    $this->fdModel->update($gpdata['master_id'], $gpMasterData);
                    $master_id = $gpdata['master_id'];
                } else {
                    $this->fdModel->insert($gpMasterData);
                    $master_id = $this->fdModel->insertID;
                }

                $crop_data = $gpdata['crop_data'];
                $mergedData = [];
                foreach ($crop_data as $crop_id => $data) {
                    $smi = isset($data['smi']) ? $data['smi'] : 0;
                    $lt = isset($data['lt']) ? $data['lt'] : 0;
                    $ls = isset($data['ls']) ? $data['ls'] : 0;

                    $mergedData[] = [
                        'final_data_id' => $master_id,
                        'crop_id' => $crop_id,
                        'smi' => $smi,
                        'lt' => $lt,
                        'ls' => $ls,
                    ];
                }

                $this->fdModel->addGpCropsData($mergedData);
            }

            $this->session->setFlashdata('message', 'Final Data Updated Successfully.');
            return redirect()->to(base_url('admin/areacoverage/finaldata'));
        }
        $data['upload_url'] = Url::areaCoverageFinalDataDocUpload . '?block_id=' . $block_id . '&season=' . $season . '&year_id=' . $year_id;
        $data['filename'] = $this->fdDocModel->getFileName($filter);
        // echo $data['filename'];
        // exit;


        echo $this->template->view('Admin\CropCoverage\Views\areacoverage_final_data_form', $data);
    }
    public function upload()
    {
        $block_id = $this->request->getGet('block_id');
        $season = $this->request->getGet('season');
        $year_id = $this->request->getGet('year_id');

        $input = $this->validate([
            'file' => [
                'uploaded[file]',
                'mime_in[file,application/pdf]',
                'max_size[file,1024]',
                'ext_in[file,pdf]',
            ]
        ]);

        if (!$input) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid file',
                'errors' => $this->validator->getErrors()
            ]);
        } else {

            $file = $this->request->getFile('file');
            $data['report'] = $file->getName();
            if ($file->isValid()) {
                $file->move(DIR_UPLOAD . 'finaldatadoc');
                ;
            }

            $filedata = [
                'block_id' => $block_id,
                'season' => $season,
                'year_id' => $year_id,
                'filename' => $file->getName()

            ];

            $this->fdDocModel->addFileData($filedata);

            // printr($filename);
            // exit;
        }
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Upload successful.',
            'url' => admin_url('areacoverage/finaldata/add?block_id=' . $block_id)
        ]);

    }

}
?>