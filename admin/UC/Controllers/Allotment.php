<?php

namespace App\Controllers\Admin\UC;

use App\Controllers\AdminController;
use App\Models\Admin\Localisation\BlockModel;
use App\Models\Admin\UC\UCAllotmentModel;
use App\Models\Admin\UC\UCSubmitModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Config\Url;

class Allotment extends AdminController
{
    protected $allotmentModel;
    protected $ucModel;

    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($request, $response);
        $this->allotmentModel = new UCAllotmentModel();
        $this->ucModel = new UCSubmitModel();
    }

    public function index()
    {
        $this->template->addPackage(['jquery_loading', 'datepicker'], true);

        $data = [];
        $data['years'] = getAllYears(false);
        $data['year_id'] = getCurrentYearId();

        $year = $this->request->getGet('year');
        if ($year) {
            $data['year_id'] = $year;
        }

        $fund_agency_id = $this->request->getGet('fund_agency_id');
        if ($fund_agency_id) {
            $data['fund_agency_id'] = $fund_agency_id;
        }

        $data['fund_agencies'] = (new BlockModel())->getFundAgencies(['asObject' => true]);

        $data['year'] = getYear($data['year_id']);

        $data['add_url'] = site_url(Url::allotmentAdd) . '?year=' . $data['year_id'] . '&fund_agency_id=' . $data['fund_agency_id'];

        $filter = [
            'year' => $data['year_id'],
            'fund_agency_id' => $data['fund_agency_id']
        ];

        $recipients = $this->allotmentModel->getUCReport($filter);

        $data['recipients'] = [];
        $total_allotment = $uc_amount = $balance = $total_balance = 0;
        foreach ($recipients as $recipient) {
            $data['recipients'][] = [
                'recipient' => $recipient->recipient,
                'date_submit' => ymdToDmy($recipient->date_submit),
                'letter_no' => $recipient->letter_no,
                'total_allotment' => in_lakh($recipient->total_allotment),
                'uc_amount' => in_lakh($recipient->uc_amount),
                'balance' => in_lakh($recipient->total_allotment - $recipient->uc_amount),
                'total_balance' => in_lakh($recipient->total_allotment_upto - $recipient->total_submitted_upto),
                'action' => '<a href="' . site_url(Url::allotmentInfo) . '?year=' . $data['year_id'] . '&recipient_id=' . $recipient->recipient_id . '" class="btn btn-info">Details</a>'
            ];
            $total_allotment += $recipient->total_allotment;
            $uc_amount += $recipient->uc_amount;
            $balance += ($recipient->total_allotment - $recipient->uc_amount);
            $total_balance += ($recipient->total_allotment_upto - $recipient->total_submitted_upto);
        }
        $data['recipients'][] = [
            'recipient' => '<strong>Total</strong>',
            'date_submit' => '',
            'letter_no' => '',
            'total_allotment' => '<strong>' . in_lakh($total_allotment) . '</strong>',
            'uc_amount' => '<strong>' . in_lakh($uc_amount) . '</strong>',
            'balance' => '<strong>' . in_lakh($balance) . '</strong>',
            'total_balance' => '<strong>' . in_lakh($total_balance) . '</strong>',
            'action' => ''
        ];

        return $this->template->view('Admin\UC\Views\allotment', $data);
    }

    public function add()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'year' => 'required|integer',
                'recipient_id' => 'required|integer',
                'allotment_date' => 'required|valid_date[Y-m-d]',
                'amount' => 'required|decimal|max_length[15]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => false,
                    'errors' => $this->validator->getErrors()
                ]);
            }

            $data = [
                'year' => $this->request->getPost('year'),
                'recipient_id' => $this->request->getPost('recipient_id'),
                'allotment_date' => dmyToYmd($this->request->getPost('allotment_date')),
                'amount' => $this->request->get
