<?php

namespace App\Controllers\Admin\Transaction;

use App\Controllers\AdminController;
use App\Models\Admin\Common\Models\AllowuploadModel;
use App\Models\Admin\Common\Models\CommonModel;
use App\Models\Admin\Localisation\Models\BlockModel;
use App\Models\Admin\Transaction\Models\ClosingbalanceModel;
use App\Models\Admin\Transaction\Models\MisctransactionModel;
use App\Models\Admin\Transaction\Models\MisctxnamtModel;
use App\Models\Admin\Users\Models\UserGroupModel;

class OtherReceipt extends AdminController
{
    private $txnModel, $cbModel;

    public function __construct()
    {
        $this->txnModel = new MisctransactionModel();
        $this->cbModel = new ClosingbalanceModel();
    }

    public function index()
    {
        helper('text');

        $this->template->addPackage(['datatable', 'uploader', 'jquery_loading'], true);

        $data['months'] = getMonths();
        $data['years'] = getAllYears();
        $data['year'] = getCurrentYearId();
        $data['month'] = getCurrentMonthId();

        $data['datatable_url'] = url_to('admin/transaction/other-receipt/search');
        $data['add_url'] = url_to('admin/transaction/other-receipt/add');

        $data['message'] = session('message');

        $data['agency_type_id'] = $this->user->agency_type_id;
        $data['agency_types'] = [];

        if ($this->user->agency_type_id == $this->settings->block_user) {
            $data['agency_types'] = (new UserGroupModel())->getBlockUsers();
        }

        return $this->template->view('Admin\Transaction\Views\otherreceipt', $data);
    }

    public function search()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $requestData = $this->request->getGet();

        $totalData = $this->txnModel->getTotal(['user_id' => $this->user->user_id]);
        $totalFiltered = $totalData;

        $filter_search = $requestData['search']['value'];

        $order_columns = array(
            'month', 'year', 'date_added'
        );

        $filter_data = array(
            'user_id' => $this->user->user_id,
            'filter_search' => $filter_search,
            'order' => $requestData['order'][0]['dir'],
            'sort' => $order_columns[$requestData['order'][0]['column']],
            'start' => $requestData['start'],
            'limit' => $requestData['length']
        );

        $totalFiltered = $this->txnModel->getTotal($filter_data);

        $filteredData = $this->txnModel->getAll($filter_data);

        $datatable = [];

        foreach ($filteredData as $result) {

            $action = '<div class="btn-group">';
            $action .= '<a class="btn btn-sm btn-primary btn-edit" href="' . url_to('admin/transaction/other-receipt/edit/' . $result->id) . '"><i class="fa fa-pencil"></i></a>';
            $action .= '</div>';

            $datatable[] = [
                $result->month,
                $result->year,
                $result->agency_type,
                $result->fund_agency,
                date('d-m-Y', strtotime($result->created_at)),
                $result->credit,
                $result->debit,
                '<label class="badge badge-' . $this->colors[$result->status] . '">' . $this->statuses[$result->status] . '</label>',
                $action
            ];

        }

        $json_data = [
            "draw" => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $datatable
        ];

        return $this->response->setJSON($json_data);

    }

    public function add()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $this->error = "";
        $json_data = [
            'status' => false,
        ];

        $month = $this->request->getGet('month');
        $year = getCurrentYearId();
        if ($this->request->getGet('year')) {
            $year = $this->request->getGet('year');
        }

        $agency_type_id = $this->user->agency_type_id;
        if ($this->request->getGet('agency_type_id')) {
            $agency_type_id = $this->request->getGet('agency_type_id');
        }

        $fund_agency_id = $this->user->fund_agency_id;

        $condition = [
            'month' => $month,
            'year' => $year,
            'agency_type_id' => $agency_type_id,
            'block_id' => $this->user->block_id,
            'district_id' => $this->user->district_id,
            'fund_agency_id' => $fund_agency_id,
            'user_id' => $this->user->user_id,
        ];

        $this->error = $this->_validate($condition);

        if (!$this->error)
