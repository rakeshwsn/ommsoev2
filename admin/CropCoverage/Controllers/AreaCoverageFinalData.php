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
        $data['heading_title'] = lang('Area Coverage Final Data');
        $data['years'] = getAllYears();
        $data['year_id'] = getCurrentYearId();
        $data['seasons'] = $this->acModel->getSeasons();
        $data['current_season'] = "kharif";
        $data['get_blocks'] = Url::getBlocks;

        $data['district_id'] = '';
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');
        } elseif ($this->user->district_id) {
            $data['district_id'] = $this->user->district_id;
        } else {
            $data['district_id'] = 0;
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

        $params = 'year_id=' . $data['year_id'];
        $params .= '&season=' . $data['current_season'];
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

        $data['add'] = admin_url('areacoverage/finaldata/add');

        //for area coverage report
        if ($this->user->block_id) {
            $data['block_id'] = $filter['block_id'] = $this->user->block_id;
            $data['districts'] = ($this->districtModel)->where('id', $this->user->district_id)->asArray()->find();
        } else if ($this->user->district_id) {
            $data['district_id'] = $filter['district_id'] = $this->user->district_id;
            $data['districts'] = ($this->districtModel)->where('id', $this->user->district_id)->asArray()->find();
        } else {
            $data['districts'] = ($this->districtModel)->orderBy('name')->asArray()->find();
        }
        $filter = [
            'year_id' => $data['year_id'],
            'season' => $data['current_season'],
            'district_id' => $data['district_id'],

            'block_id' => $data['block_id'],

        ];


        $blocks = $this->fdModel->getAreaCoverageFinalReport($filter);
        $data['blocksfd'] = $blocks;
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
        $data = [];
        $data['seasons'] = $this->acModel->getSeasons();
        $data['current_season'] = "kharif";
        $data['years'] = getAllYears();
        $data['year_id'] = getCurrentYearId();
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
        $data['year_id'] = $this->request->getGet('year_id', getCurrentYearId());
        $data['current_season'] = getCurrentSeason();

        $filter = [
            'year_id' => $data['year_id'],
            'season' => $data['current_season'],
            'block_id' => $data['block_id']
        ];

        $data['gpsfinaldata'] = $this->fdModel->getGpsFinalData($filter);

        // printr($data['gpsfinaldata']);
        // exit;
        foreach ($data['gpsfinaldata'] as &$gpsfinal) {
            $final_data_id = $gpsfinal['id'];

            // Fetch GPS Demonstration Data
            $gps_demon_data = $this->fdModel->getGpsDemonData($final_data_id);

            // Loop through the GPS Demonstration Data
            foreach ($gps_demon_data as &$crop) {
                // Check if the crop ID exists in the crop practices array
                $practice = $data['crop_practices'][$crop['id']];

                // Remove fields not in the practice array
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
        // printr($gpsfinal['crops_data']);
        // exit;

        if ($this->request->getMethod(1) === 'POST') {
            $block_id = $this->request->getGet('block_id');
            $data['block_id'] = $block_id;

            $gpMasterData = [];

            foreach ($data['gpsfinaldata'] as $gpdata) {

                $gpMasterData[] = [
                    "year_id" => getCurrentYearId(),
                    "season" => getCurrentSeason(),
                    "district_id" => $this->user->district_id,
                    "block_id" => $block_id,


                    "gp_id" => $gpdata['gp_id'],
                    "no_of_village" => $this->request->getPost('no_of_village[' . $gpdata['gp_id'] . ']'),
                    "farmers_covered_under_demonstration" => $this->request->getPost('farmers_covered_under_demonstration[' . $gpdata['gp_id'] . ']'),
                    "farmers_covered_under_followup" => $this->request->getPost('farmers_covered_under_followup[' . $gpdata['gp_id'] . ']'),
                    "fup_ragi" => $this->request->getPost('fup_ragi[' . $gpdata['gp_id'] . ']'),
                    "fup_lm" => $this->request->getPost('fup_lm[' . $gpdata['gp_id'] . ']'),
                    "fup_fm" => $this->request->getPost('fup_fm[' . $gpdata['gp_id'] . ']'),
                    "fup_sorghum" => $this->request->getPost('fup_sorghum[' . $gpdata['gp_id'] . ']'),
                    "fup_km" => $this->request->getPost('fup_km[' . $gpdata['gp_id'] . ']'),
                    "fup_bm" => $this->request->getPost('fup_bm[' . $gpdata['gp_id'] . ']'),
                    "fup_pm" => $this->request->getPost('fup_pm[' . $gpdata['gp_id'] . ']'),
                ];

            }

            $final_data_ids = [];
            foreach ($gpMasterData as $data) {
                $master = $this->fdModel
                    ->where('year_id', $data['year_id'])
                    ->where('season', $data['season'])
                    ->where('district_id', $data['district_id'])
                    ->where('block_id', $data['block_id'])
                    ->where('gp_id', $data['gp_id'])
                    ->first();


                if ($master) {
                    $final_data_id = $master->id;

                    // Store both final_data_id and gp_id in the final_data_ids array
                    $final_data_ids[] = ['final_data_id' => $final_data_id, 'gp_id' => $data['gp_id']];
                } else {
                    $final_data_id = $this->fdModel->insert($data);

                    // Store both final_data_id and gp_id in the final_data_ids array
                    $final_data_ids[] = ['final_data_id' => $final_data_id, 'gp_id' => $data['gp_id']];
                }




            }


            // printr($final_data_ids);
            // exit;

            $crop_data = $this->request->getPost('crop_data'); // Assuming crop data is an associative array.

            $mergedData = [];

            foreach ($final_data_ids as $finalData) {
                $final_data_id = $finalData['final_data_id'];
                $gp_id = $finalData['gp_id'];

                if (isset($crop_data[$gp_id])) {
                    foreach ($crop_data[$gp_id] as $crop_id => $data) {
                        $smi = isset($data['smi']) ? $data['smi'] : 0;
                        $lt = isset($data['lt']) ? $data['lt'] : 0;
                        $ls = isset($data['ls']) ? $data['ls'] : 0;

                        $mergedData[] = [
                            'final_data_id' => $final_data_id,
                            'crop_id' => $crop_id,
                            'smi' => $smi,
                            'lt' => $lt,
                            'ls' => $ls,
                        ];
                    }
                }
            }
            // printr($mergedData);
            // exit;

            $this->fdModel->addGpCropsData($mergedData);
            // Your existing code continues here




            $this->session->setFlashdata('message', 'Target Updated Successfully.');

            return redirect()->to(base_url('admin/areacoverage/finaldata/add'));

            // echo $final_data_id;
            // exit;
            // printr($gpMasterData);
            // exit;
        }

        echo $this->template->view('Admin\CropCoverage\Views\areacoverage_final_data_form', $data);
    }

}
?>