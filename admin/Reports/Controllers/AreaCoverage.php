<?php 
namespace Admin\Reports\Controllers;

use Admin\Common\Models\CommonModel;
use Admin\CropCoverage\Models\AreaCoverageModel;
use Admin\CropCoverage\Models\CropsModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Reports\Models\ReportsModel;
use App\Controllers\AdminController;
use App\Traits\ReportTrait;
use Config\Url;

class AreaCoverage extends AdminController {

    public function index() {
        $data = [];

        $acModel = new AreaCoverageModel();
        $cropsModel = new CropsModel();
        $data['years'] = getAllYears();
        $data['seasons'] = $acModel->getSeasons();

        $data['current_season'] = strtolower(getCurrentSeason());
        $data['year_id'] = getCurrentYearId();

        if($this->request->getGet('year_id')){
            $data['year_id'] = $this->request->getGet('year_id');
        }

        if($this->request->getGet('season')){
            $data['current_season'] = $this->request->getGet('season');
        }

        $data['district_id'] = '';
        if($this->request->getGet('district_id')){
            $data['district_id'] = $this->request->getGet('district_id');
        }

        $data['block_id'] = '';
        if($this->request->getGet('block_id')){
            $data['block_id'] = $this->request->getGet('block_id');
        }

        $filter = [
            'year_id' => $data['year_id'],
            'season' => $data['current_season']
        ];
        if($data['block_id']){
            $filter['block_id'] = $data['block_id'];
        } else if($data['district_id']) {
            $filter['district_id'] = $data['district_id'];
        }

        $blocks = $acModel->getAreaCoverageReport($filter);

        if($data['block_id']){
            $this->gps($blocks,$data);
        } else if($data['district_id']) {
            $this->blocks($blocks,$data);
        } else {
            $this->districts($blocks,$data);
        }

        $data['crop_practices'] = $acModel->getCropPractices();
        $crops = $cropsModel->findAll();

        $data['crops'] = [];
        foreach ($crops as $crop) {
            $data['crops'][$crop->id] = $crop->crops;
        }

        $data['districts'] = (new DistrictModel())->asArray()->find();

        $data['blocks'] = [];
        if($data['district_id']){
            $data['blocks'] = (new BlockModel())->where('district_id',$data['district_id'])
                ->asArray()->findAll();
        }

        $data['filter_panel'] = view('Admin\Reports\Views\areacoverage_filter', $data);
        $data['download_url'] = admin_url('reports/areacoverage/download');
        $data['get_blocks'] = Url::getBlocks;

        return $this->template->view('Admin\Reports\Views\areacoverage', $data);
    }

    private function gps($blocks,&$data){

        $total_farmers_covered = $total_nursery_raised = $total_balance_smi =
        $total_balance_lt = $total_ragi_smi = $total_ragi_lt = $total_ragi_ls =
        $total_little_millet_lt = $total_little_millet_ls = $total_foxtail_ls =
        $total_sorghum_ls = $total_kodo_ls = $total_barnyard_ls = $total_pearl_ls =
        $total_total_ragi = $total_total_non_ragi = $total_fc_area = $total_total_area = 0;

        $data['rows'] = [];
        foreach ($blocks as $block) {
            $total_area = $block->fc_area +
                $block->ragi_smi +
                $block->ragi_lt +
                $block->ragi_ls +
                $block->little_millet_lt +
                $block->little_millet_ls +
                $block->foxtail_ls +
                $block->sorghum_ls +
                $block->kodo_ls +
                $block->barnyard_ls +
                $block->pearl_ls;
            $total_ragi = $block->ragi_smi +
                $block->ragi_lt +
                $block->ragi_ls;
            $total_non_ragi = $total_area-$total_ragi-$block->fc_area;

            $data['rows'][] = [
                'gp' => $block->gp,
                'farmers_covered' => $block->farmers_covered,
                'nursery_raised' => $block->nursery_raised,
                'balance_smi' => $block->balance_smi,
                'balance_lt' => $block->balance_lt,
                'ragi_smi' => $block->ragi_smi,
                'ragi_lt' => $block->ragi_lt,
                'ragi_ls' => $block->ragi_ls,
                'little_millet_lt' => $block->little_millet_lt,
                'little_millet_ls' => $block->little_millet_ls,
                'foxtail_ls' => $block->foxtail_ls,
                'sorghum_ls' => $block->sorghum_ls,
                'kodo_ls' => $block->kodo_ls,
                'barnyard_ls' => $block->barnyard_ls,
                'pearl_ls' => $block->pearl_ls,
                'total_ragi' => $total_ragi,
                'total_non_ragi' => $total_non_ragi,
                'total_fc' => $block->fc_area,
                'total_area' => $total_area
            ];

            //calc total
            $total_farmers_covered += $block->farmers_covered;
            $total_nursery_raised += $block->nursery_raised;
            $total_balance_smi += $block->balance_smi;
            $total_balance_lt += $block->balance_lt;
            $total_ragi_smi += $block->ragi_smi;
            $total_ragi_lt += $block->ragi_lt;
            $total_ragi_ls += $block->ragi_ls;
            $total_little_millet_lt += $block->little_millet_lt;
            $total_little_millet_ls += $block->little_millet_ls;
            $total_foxtail_ls += $block->foxtail_ls;
            $total_sorghum_ls += $block->sorghum_ls;
            $total_kodo_ls += $block->kodo_ls;
            $total_barnyard_ls += $block->barnyard_ls;
            $total_pearl_ls += $block->pearl_ls;
            $total_total_ragi += $total_ragi;
            $total_total_non_ragi += $total_non_ragi;
            $total_fc_area += $block->fc_area;
            $total_total_area += $total_area;

        }

        $data['rows'][] = [
            'gp' => '<strong>Total</strong>',
            'farmers_covered' => $total_farmers_covered,
            'nursery_raised' => $total_nursery_raised,
            'balance_smi' => $total_balance_smi,
            'balance_lt' => $total_balance_lt,
            'ragi_smi' => $total_ragi_smi,
            'ragi_lt' => $total_ragi_lt,
            'ragi_ls' => $total_ragi_ls,
            'little_millet_lt' => $total_little_millet_lt,
            'little_millet_ls' => $total_little_millet_ls,
            'foxtail_ls' => $total_foxtail_ls,
            'sorghum_ls' => $total_sorghum_ls,
            'kodo_ls' => $total_kodo_ls,
            'barnyard_ls' => $total_barnyard_ls,
            'pearl_ls' => $total_pearl_ls,
            'total_ragi' => $total_total_ragi,
            'total_non_ragi' => $total_total_non_ragi,
            'total_fc' => $total_fc_area,
            'total_area' => $total_total_area
        ];
    }

    private function blocks($blocks,&$data){

        $total_farmers_covered = $total_nursery_raised = $total_balance_smi =
        $total_balance_lt = $total_ragi_smi = $total_ragi_lt = $total_ragi_ls =
        $total_little_millet_lt = $total_little_millet_ls = $total_foxtail_ls =
        $total_sorghum_ls = $total_kodo_ls = $total_barnyard_ls = $total_pearl_ls =
        $total_total_ragi = $total_total_non_ragi = $total_fc_area = $total_total_area = 0;

        $data['rows'] = [];
        $gps = 0;
        foreach ($blocks as $block) {
            $total_area = $block->fc_area +
                $block->ragi_smi +
                $block->ragi_lt +
                $block->ragi_ls +
                $block->little_millet_lt +
                $block->little_millet_ls +
                $block->foxtail_ls +
                $block->sorghum_ls +
                $block->kodo_ls +
                $block->barnyard_ls +
                $block->pearl_ls;
            $total_ragi = $block->ragi_smi +
                $block->ragi_lt +
                $block->ragi_ls;
            $total_non_ragi = $total_area-$total_ragi-$block->fc_area;

            $data['rows'][] = [
                'block' => $block->block,
                'gps' => $block->total_gps,
                'farmers_covered' => $block->farmers_covered,
                'nursery_raised' => $block->nursery_raised,
                'balance_smi' => $block->balance_smi,
                'balance_lt' => $block->balance_lt,
                'ragi_smi' => $block->ragi_smi,
                'ragi_lt' => $block->ragi_lt,
                'ragi_ls' => $block->ragi_ls,
                'little_millet_lt' => $block->little_millet_lt,
                'little_millet_ls' => $block->little_millet_ls,
                'foxtail_ls' => $block->foxtail_ls,
                'sorghum_ls' => $block->sorghum_ls,
                'kodo_ls' => $block->kodo_ls,
                'barnyard_ls' => $block->barnyard_ls,
                'pearl_ls' => $block->pearl_ls,
                'total_ragi' => $total_ragi,
                'total_non_ragi' => $total_non_ragi,
                'total_fc' => $block->fc_area,
                'total_area' => $total_area
            ];

            //calc total
            $total_farmers_covered += $block->farmers_covered;
            $total_nursery_raised += $block->nursery_raised;
            $total_balance_smi += $block->balance_smi;
            $total_balance_lt += $block->balance_lt;
            $total_ragi_smi += $block->ragi_smi;
            $total_ragi_lt += $block->ragi_lt;
            $total_ragi_ls += $block->ragi_ls;
            $total_little_millet_lt += $block->little_millet_lt;
            $total_little_millet_ls += $block->little_millet_ls;
            $total_foxtail_ls += $block->foxtail_ls;
            $total_sorghum_ls += $block->sorghum_ls;
            $total_kodo_ls += $block->kodo_ls;
            $total_barnyard_ls += $block->barnyard_ls;
            $total_pearl_ls += $block->pearl_ls;
            $total_total_ragi += $total_ragi;
            $total_total_non_ragi += $total_non_ragi;
            $total_fc_area += $block->fc_area;
            $total_total_area += $total_area;

            $gps += $block->total_gps;

        }

        $data['rows'][] = [
            'block' => '<strong>Total</strong>',
            'gps' => $gps,
            'farmers_covered' => $total_farmers_covered,
            'nursery_raised' => $total_nursery_raised,
            'balance_smi' => $total_balance_smi,
            'balance_lt' => $total_balance_lt,
            'ragi_smi' => $total_ragi_smi,
            'ragi_lt' => $total_ragi_lt,
            'ragi_ls' => $total_ragi_ls,
            'little_millet_lt' => $total_little_millet_lt,
            'little_millet_ls' => $total_little_millet_ls,
            'foxtail_ls' => $total_foxtail_ls,
            'sorghum_ls' => $total_sorghum_ls,
            'kodo_ls' => $total_kodo_ls,
            'barnyard_ls' => $total_barnyard_ls,
            'pearl_ls' => $total_pearl_ls,
            'total_ragi' => $total_total_ragi,
            'total_non_ragi' => $total_total_non_ragi,
            'total_fc' => $total_fc_area,
            'total_area' => $total_total_area
        ];
    }

    private function districts($blocks,&$data){

        $total_farmers_covered = $total_nursery_raised = $total_balance_smi =
        $total_balance_lt = $total_ragi_smi = $total_ragi_lt = $total_ragi_ls =
        $total_little_millet_lt = $total_little_millet_ls = $total_foxtail_ls =
        $total_sorghum_ls = $total_kodo_ls = $total_barnyard_ls = $total_pearl_ls =
        $total_total_ragi = $total_total_non_ragi = $total_fc_area = $total_total_area = 0;

        $data['rows'] = [];
        $gps = $tblocks = 0;
        foreach ($blocks as $block) {
            $total_area = $block->fc_area +
                $block->ragi_smi +
                $block->ragi_lt +
                $block->ragi_ls +
                $block->little_millet_lt +
                $block->little_millet_ls +
                $block->foxtail_ls +
                $block->sorghum_ls +
                $block->kodo_ls +
                $block->barnyard_ls +
                $block->pearl_ls;
            $total_ragi = $block->ragi_smi +
                $block->ragi_lt +
                $block->ragi_ls;
            $total_non_ragi = $total_area-$total_ragi-$block->fc_area;

            $data['rows'][] = [
                'district' => $block->district,
                'blocks' => $block->total_blocks,
                'gps' => $block->total_gps,
                'farmers_covered' => $block->farmers_covered,
                'nursery_raised' => $block->nursery_raised,
                'balance_smi' => $block->balance_smi,
                'balance_lt' => $block->balance_lt,
                'ragi_smi' => $block->ragi_smi,
                'ragi_lt' => $block->ragi_lt,
                'ragi_ls' => $block->ragi_ls,
                'little_millet_lt' => $block->little_millet_lt,
                'little_millet_ls' => $block->little_millet_ls,
                'foxtail_ls' => $block->foxtail_ls,
                'sorghum_ls' => $block->sorghum_ls,
                'kodo_ls' => $block->kodo_ls,
                'barnyard_ls' => $block->barnyard_ls,
                'pearl_ls' => $block->pearl_ls,
                'total_ragi' => $total_ragi,
                'total_non_ragi' => $total_non_ragi,
                'total_fc' => $block->fc_area,
                'total_area' => $total_area
            ];

            //calc total
            $total_farmers_covered += $block->farmers_covered;
            $total_nursery_raised += $block->nursery_raised;
            $total_balance_smi += $block->balance_smi;
            $total_balance_lt += $block->balance_lt;
            $total_ragi_smi += $block->ragi_smi;
            $total_ragi_lt += $block->ragi_lt;
            $total_ragi_ls += $block->ragi_ls;
            $total_little_millet_lt += $block->little_millet_lt;
            $total_little_millet_ls += $block->little_millet_ls;
            $total_foxtail_ls += $block->foxtail_ls;
            $total_sorghum_ls += $block->sorghum_ls;
            $total_kodo_ls += $block->kodo_ls;
            $total_barnyard_ls += $block->barnyard_ls;
            $total_pearl_ls += $block->pearl_ls;
            $total_total_ragi += $total_ragi;
            $total_total_non_ragi += $total_non_ragi;
            $total_fc_area += $block->fc_area;
            $total_total_area += $total_area;

            $gps += $block->total_gps;
            $tblocks += $block->total_blocks;

        }

        $data['rows'][] = [
            'district' => '<strong>Total</strong>',
            'blocks' => $tblocks,
            'gps' => $gps,
            'farmers_covered' => $total_farmers_covered,
            'nursery_raised' => $total_nursery_raised,
            'balance_smi' => $total_balance_smi,
            'balance_lt' => $total_balance_lt,
            'ragi_smi' => $total_ragi_smi,
            'ragi_lt' => $total_ragi_lt,
            'ragi_ls' => $total_ragi_ls,
            'little_millet_lt' => $total_little_millet_lt,
            'little_millet_ls' => $total_little_millet_ls,
            'foxtail_ls' => $total_foxtail_ls,
            'sorghum_ls' => $total_sorghum_ls,
            'kodo_ls' => $total_kodo_ls,
            'barnyard_ls' => $total_barnyard_ls,
            'pearl_ls' => $total_pearl_ls,
            'total_ragi' => $total_total_ragi,
            'total_non_ragi' => $total_total_non_ragi,
            'total_fc' => $total_fc_area,
            'total_area' => $total_total_area
        ];
    }
}
