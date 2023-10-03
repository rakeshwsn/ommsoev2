<?php
namespace Admin\CropCoverage\Controllers;

use Admin\CropCoverage\Models\AreaCoverageModel;
use App\Controllers\AdminController;
use Admin\CropCoverage\Models\TargetModel;
use Admin\CropCoverage\Models\CropsModel;
use Admin\CropCoverage\Models\PracticesModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;

class Approve extends AdminController
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
        $this->areacoveragemodel = new AreaCoverageModel();
    }

    public function index()
    {

        if ($this->user->block_id) {
            return $this->block();
        } else if ($this->user->district_id) {
            return $this->district();
        } else {
            return $this->state();
        }
    }

    public function district()
    {
        $this->template->set_meta_title(lang('Approve.heading_title'));

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => lang('Approve.heading_title'),
            'href' => admin_url('grampanchayat')
        );

        $this->template->add_package(array('datatable', 'select2'), true);

        $data['heading_title'] = lang('Area Coverage Approval');

        $data['text_list'] = lang('Approve.text_list');
        $data['text_no_results'] = lang('Approve.text_no_results');
        $data['text_confirm'] = lang('Approve.text_confirm');

        $data['button_add'] = lang('Add Target');
        $data['button_edit'] = lang('Edit Target');
        $data['button_delete'] = lang('Approve.button_delete');

        if (isset($this->error['warning'])) {
            $data['error'] = $this->error['warning'];
        }

        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $district_id = $this->request->getGet('district_id');
        } else {
            $data['district_id'] = $district_id = $this->user->district_id;
        }
        $data['currentDay'] = date('l');
        // $data['isActiveDay'] = in_array($data['currentDay'], array('Saturday', 'Sunday', 'Monday',));

        $week_dates = $this->areacoveragemodel->getWeekDate();

        if ($this->request->getGet('start_date')) {
            $data['start_date'] = $start_date = $this->request->getGet('start_date');
        } else {
            $data['start_date'] = $start_date = $week_dates['start_date'];
        }

        $acModel = new AreaCoverageModel();
        //update status
        if ($this->request->getMethod(1) == 'POST') {
            $status = [
                'status' => $this->request->getPost('status'),
                'remarks' => $this->request->getPost('remarks'),
            ];
            $acModel->where('district_id', $district_id)
                ->where('start_date', $start_date)->set($status)->update();
            return redirect()->to(admin_url('areacoverage/approve/district?district_id=' . $district_id . '&start_date=' . $start_date))
                ->with('message', 'Status has been updated.');
        }

        $filter = [
            'district_id' => $district_id,
            'year_id' => getCurrentYearId(),
            'season' => getCurrentSeason(),
            'start_date' => $data['start_date']
        ];

        $blocks = $this->areacoveragemodel->getAreaCoverage($filter);

        $total_farmers_covered = $total_nursery_raised = $total_balance_smi =
            $total_balance_lt = $total_ragi_smi = $total_ragi_lt = $total_ragi_ls =
            $total_little_millet_lt = $total_little_millet_ls = $total_foxtail_ls =
            $total_sorghum_ls = $total_kodo_ls = $total_barnyard_ls = $total_pearl_ls =
            $total_total_ragi = $total_total_non_ragi = $total_fc_area = $total_total_area = 0;
        $total_gps = 0;

        $data['blocks'] = [];
        $week_text = '';

        foreach ($blocks as $block) {
            $action = '';
            if ($block->start_date) {
                $href = admin_url('areacoverage/approve/block?block_id=' . $block->block_id . '&start_date=' . $block->start_date);
                $action = '<a href="' . $href . '" class="btn btn-sm btn-info" data-toggle="tooltip" data-title="View">
                                            <i class="fa fa-list"></i></a>';
                $week_text = date('d F', strtotime($block->start_date)) . '-' . date('d F', strtotime($block->end_date));
            }
            $status = $block->status;
            if (!isset($status)) {
                $status = 3;
            }
            $this->calcTotals($block);

            $data['blocks'][] = [
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
                'total_ragi' => $block->total_ragi,
                'total_non_ragi' => $block->total_non_ragi,
                'total_fc' => $block->fc_area,
                'total_area' => $block->total_area,
                'status' => $this->statuses[$status],
                'action' => $action,
            ];

            //calc total
            $total_gps += (int) $block->total_gps;
            $total_farmers_covered += (int) $block->farmers_covered;
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
            $total_total_ragi += $block->total_ragi;
            $total_total_non_ragi += $block->total_non_ragi;
            $total_fc_area += $block->fc_area;
            $total_total_area += $block->total_area;

        }

        $data['blocks'][] = [
            'block' => '<strong>Total</strong>',
            'gps' => $total_gps,
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
            'total_area' => $total_total_area,
            'status' => '',
            'action' => ''
        ];

        $data['crop_practices'] = $this->areacoveragemodel->getCropPractices();
        $crops = $this->cropsModel->findAll();

        $data['crops'] = [];
        foreach ($crops as $crop) {
            $data['crops'][$crop->id] = $crop->crops;
        }

        $weeks = $this->areacoveragemodel->getWeeks();

        $data['weeks'] = [];
        $week_start_date = '';
        foreach ($weeks as $week) {
            //dropdown weeks
            if (strtotime($week['start_date']) <= strtotime('today')) {
                $data['weeks'][$week['start_date']] = $week_start_date = $week['start_date'];
            }
            //show week text
            if (strtotime($week['start_date']) <= strtotime($data['start_date'])) {
                $week_text = date('d F', strtotime($week['start_date'])) . '-' . date('d F', strtotime($week['end_date']));
            }
        }

        $data['week_start_date'] = $data['start_date'];

        $data['week_text'] = $week_text;

        $data['districts'] = [];
        $districts = (new DistrictModel())->findAll();

        foreach ($districts as $district) {
            if ($district->id == $district_id) {
                $data['districts'][$district->id] = $district->name;
            }
        }

        $district_status = $acModel->where('district_id', $district_id)
            ->where('start_date', $start_date)->first();

        $data['status'] = '';
        $data['remarks'] = '';
        $data['status_color'] = '';
        if ($district_status) {
            $data['status'] = $this->statuses[$district_status->status];
            $data['status_color'] = $this->colors[$district_status->status];
            $data['remarks'] = $district_status->remarks;
        }

        $data['show_approval'] = false;
        if ($this->user->district_id != $district_id) {
            $data['show_approval'] = true;
            $data['reject_url'] = admin_url('areacoverage/approve/reject');
            $form_data = $this->getForm();
            $data['approve_form'] = view('\Admin\Transaction\Views\approve_form', $form_data);
        }

        return $this->template->view('Admin\CropCoverage\Views\approve_district', $data);
    }

    public function block()
    { //gpwise
        $data['heading_title'] = lang('Approve Area Coverage');

        $block_id = $this->request->getGet('block_id');

        $start_date = $this->request->getGet('start_date');

        $dates = $this->areacoveragemodel->getWeekDate($start_date);
        $data['currentDay'] = date('l');
        $data['isActiveDay'] = in_array($data['currentDay'], array('Saturday', 'Sunday', 'Monday'));

        if ($this->request->getMethod(1) == 'POST') {
            $filter = [
                'block_id' => $block_id,
                'start_date' => $dates['start_date']
            ];
            $status = (int) $this->request->getPost('status');
            $remarks = $this->request->getPost('remarks');
            $this->areacoveragemodel->setStatus($filter, $status, $remarks);

            $this->session->setFlashdata('message', 'The area coverage data has been approved');
            return redirect()->to(admin_url('areacoverage/approve'));
        }

        $acModel = new AreaCoverageModel();
        //update status
        if ($this->request->getMethod(1) == 'POST') {
            $status = [
                'status' => $this->request->getPost('status'),
                'remarks' => $this->request->getPost('remarks'),
            ];
            $acModel->where('block_id', $block_id)
                ->where('start_date', $start_date)->set($status)->update();
            return redirect()->to(admin_url('areacoverage/approve/block?block_id=' . $block_id . '&start_date=' . $start_date))
                ->with('message', 'Status has been updated.');
        }

        $filter = [
            'block_id' => $block_id,
            'start_date' => $dates['start_date']
        ];

        $blocks = $this->areacoveragemodel->getAreaCoverage($filter);

        $data['from_date'] = $dates['start_date'];
        $total_farmers_covered = $total_nursery_raised = $total_balance_smi =
            $total_balance_lt = $total_ragi_smi = $total_ragi_lt = $total_ragi_ls =
            $total_little_millet_lt = $total_little_millet_ls = $total_foxtail_ls =
            $total_sorghum_ls = $total_kodo_ls = $total_barnyard_ls = $total_pearl_ls =
            $total_total_ragi = $total_total_non_ragi = $total_fc_area = $total_total_area = 0;

        $data['blocks'] = [];
        $data['approved'] = false;
        $slno = 1;
        $week = '';
        foreach ($blocks as $block) {
            $action = '';
            $week = '';
            if ($block->start_date) {
                $href = admin_url('areacoverage/edit?id=' . $block->cc_id);
                $action .= '<a href="' . $href . '" class="btn btn-sm btn-info" data-toggle="tooltip" data-title="View">
                                            <i class="fa fa-list"></i></a>';

                $week = date('d F', strtotime($block->start_date)) . '-' . date('d F', strtotime($block->end_date));
            }
            $status = $block->status;
            if (!isset($status)) {
                $status = 3;
            }

            $this->calcTotals($block);

            $data['blocks'][] = [
                'slno' => $slno++,
                'cc_id' => $block->cc_id,
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
                'total_ragi' => $block->total_ragi,
                'total_non_ragi' => $block->total_non_ragi,
                'total_fc' => $block->fc_area,
                'total_area' => $block->total_area,
                'action' => $action,
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
            $total_total_ragi += $block->total_ragi;
            $total_total_non_ragi += $block->total_non_ragi;
            $total_fc_area += $block->fc_area;
            $total_total_area += $block->total_area;

            $data['approved'] = $block->status == 1;
        }

        $data['blocks'][] = [
            'slno' => '',
            'cc_id' => '',
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
            'total_area' => $total_total_area,
            'action' => ''
        ];

        $data['crop_practices'] = $this->areacoveragemodel->getCropPractices();
        $crops = $this->cropsModel->findAll();

        $data['crops'] = [];
        foreach ($crops as $crop) {
            $data['crops'][$crop->id] = $crop->crops;
        }
        $data['to_date'] = $dates['end_date'];
        $data['week'] = $week;

        $data['approval'] = true;
        $form_data = $this->getForm();
        $data['approve_form'] = view('\Admin\Transaction\Views\approve_form', $form_data);

        $blockModel = new BlockModel();
        $block = $blockModel->find($block_id);
        $blocks = $blockModel->where('district_id', $block->district_id)->findAll();

        $data['filter_blocks'] = [];
        foreach ($blocks as $block) {
            if ($block->id == $block_id) {
                $data['filter_blocks'][$block->id] = $block->name;
            }
        }
        $data['block_id'] = $block_id;

        $weeks = $this->areacoveragemodel->getWeeks();

        $data['weeks'] = [];
        $week_start_date = $start_date;
        foreach ($weeks as $week) {
            //dropdown weeks
            if (strtotime($week['start_date']) <= strtotime('today')) {
                $data['weeks'][$week['start_date']] = $week['start_date'];
            }
        }

        $data['week_start_date'] = $week_start_date;

        $district_status = $acModel->where('block_id', $block_id)
            ->where('start_date', $start_date)->first();

        $data['status'] = '';
        $data['remarks'] = '';
        $data['status_color'] = '';
        if ($district_status) {
            $data['status'] = $this->statuses[$district_status->status];
            $data['status_color'] = $this->colors[$district_status->status];
            $data['remarks'] = $district_status->remarks;
        }

        return $this->template->view('Admin\CropCoverage\Views\approve_block', $data);
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

    public function state()
    {
        $this->template->set_meta_title(lang('Approve.heading_title'));

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => lang('Approve.heading_title'),
            'href' => admin_url('grampanchayat')
        );

        $this->template->add_package(array('datatable', 'select2'), true);

        $data['heading_title'] = lang('Area Coverage Approval');

        $data['text_list'] = lang('Approve.text_list');
        $data['text_no_results'] = lang('Approve.text_no_results');
        $data['text_confirm'] = lang('Approve.text_confirm');

        $data['button_add'] = lang('Add Target');
        $data['button_edit'] = lang('Edit Target');
        $data['button_delete'] = lang('Approve.button_delete');

        if (isset($this->error['warning'])) {
            $data['error'] = $this->error['warning'];
        }

        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');
        } else {
            $data['district_id'] = '';
        }

        $weekDate = $this->areacoveragemodel->getWeekDate();
        if ($this->request->getGet('start_date')) {
            $data['start_date'] = $this->request->getGet('start_date');
        } else if($weekDate) {
            $data['start_date'] = $weekDate['start_date'];
        } else {
            $data['start_date'] = '';
        }

        $filter = [
            'year_id' => getCurrentYearId(),
            'season' => getCurrentSeason(),
            'start_date' => $data['start_date']
        ];

        $blocks = $this->areacoveragemodel->getAreaCoverage($filter);

        $total_farmers_covered = $total_nursery_raised = $total_balance_smi =
            $total_balance_lt = $total_ragi_smi = $total_ragi_lt = $total_ragi_ls =
            $total_little_millet_lt = $total_little_millet_ls = $total_foxtail_ls =
            $total_sorghum_ls = $total_kodo_ls = $total_barnyard_ls = $total_pearl_ls =
            $total_total_ragi = $total_total_non_ragi = $total_fc_area = $total_total_area = 0;
        $total_gps = 0;
        $total_blocks = 0;

        $week_text = '';
        $data['districts'] = [];
        foreach ($blocks as $block) {
            $action = '';
            if ($block->start_date) {
                $href = admin_url('areacoverage/approve/district?district_id=' . $block->district_id . '&start_date=' . $block->start_date);
                $action = '<a href="' . $href . '" class="btn btn-sm btn-info" data-toggle="tooltip" data-title="View">
                                            <i class="fa fa-list"></i></a>';

                $week_text = date('d F', strtotime($block->start_date)) . '-' . date('d F', strtotime($block->end_date));
            }
            $status = $block->status;
            if (!isset($status)) {
                $status = 3;
            }

            $this->calcTotals($block);

            $data['districts'][] = [
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
                'total_ragi' => $block->total_ragi,
                'total_non_ragi' => $block->total_non_ragi,
                'total_fc' => $block->fc_area,
                'total_area' => $block->total_area,
                'status' => $this->statuses[$status],
                'status_color' => $this->colors[$status],
                'action' => $action,
            ];

            //calc total
            $total_blocks += (int) $block->total_blocks;
            $total_gps += (int) $block->total_gps;
            $total_farmers_covered += (int) $block->farmers_covered;
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
            $total_total_ragi += $block->total_ragi;
            $total_total_non_ragi += $block->total_non_ragi;
            $total_fc_area += $block->fc_area;
            $total_total_area += $block->total_area;

        }

        $data['districts'][] = [
            'district' => '<strong>Total</strong>',
            'gps' => $total_gps,
            'blocks' => $total_blocks,
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
            'total_area' => $total_total_area,
            'status' => '',
            'status_color' => '',
            'action' => ''
        ];

        $data['crop_practices'] = $this->areacoveragemodel->getCropPractices();
        $crops = $this->cropsModel->findAll();

        $data['crops'] = [];
        foreach ($crops as $crop) {
            $data['crops'][$crop->id] = $crop->crops;
        }
        $weeks = $this->areacoveragemodel->getWeeks();

        $data['weeks'] = [];
        $week_start_date = '';
        foreach ($weeks as $week) {
            //dropdown weeks
            if (strtotime($week['start_date']) <= strtotime('today')) {
                $data['weeks'][$week['start_date']] = $week_start_date = $week['start_date'];
            }
            //show week text
            if (strtotime($week['start_date']) <= strtotime($data['start_date'])) {
                $week_text = date('d F', strtotime($week['start_date'])) . '-' . date('d F', strtotime($week['end_date']));
            }
        }

        $data['week_start_date'] = $data['start_date'];

        $data['week_text'] = $week_text;

        return $this->template->view('Admin\CropCoverage\Views\approve_state', $data);
    }

    public function reject()
    {
        $district_id = $this->request->getPost('district_id');
        $start_date = $this->request->getPost('start_date');

        $updated = (new AreaCoverageModel())->where('district_id', $district_id)
            ->where('start_date', $start_date)->set(['status' => 2])->update();
        $redirect = '';
        $status = false;
        if ($updated) {
            $status = true;
            $redirect = admin_url('areacoverage/approve?start_date=' . $start_date);
            redirect()->to($redirect)->with('message', 'District data rejected');
        }
        return $this->response->setJSON([
            'status' => $status,
            'redirect' => $redirect
        ]);
    }

    private function calcTotals(&$block)
    {
        $block->total_area = (double) $block->fc_area +
            (double) $block->ragi_smi +
            (double) $block->ragi_lt +
            (double) $block->ragi_ls +
            (double) $block->little_millet_lt +
            (double) $block->little_millet_ls +
            (double) $block->foxtail_ls +
            (double) $block->sorghum_ls +
            (double) $block->kodo_ls +
            (double) $block->barnyard_ls +
            (double) $block->pearl_ls;

        $block->total_ragi = (double) $block->ragi_smi +
            (double) $block->ragi_lt +
            (double) $block->ragi_ls;

        $block->fc_area = doubleval($block->fc_area);

        $block->total_non_ragi = bcsub(bcsub($block->total_area, $block->total_ragi, 2), $block->fc_area, 2);
    }
}

?>