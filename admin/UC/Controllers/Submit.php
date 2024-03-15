<?php

declare(strict_types=1);

namespace Admin\UC\Controllers;

use Admin\UC\Models\UCAllotmentModel;
use Admin\UC\Models\UCSubmitModel;
use App\Controllers\AdminController;
use Config\Url;

class Submit extends AdminController
{
    /**
     * @var UCSubmitModel
     */
    private $ucSubmitModel;

    /**
     * @var UCAllotmentModel
     */
    private $ucAllotmentModel;

    public function __construct()
    {
        $this->ucSubmitModel = new UCSubmitModel();
        $this->ucAllotmentModel = new UCAllotmentModel();
    }

    public function index(): string
    {
        $this->template->addPackage(['jquery_loading', 'datepicker'], true);

        $filter = $this->getFilter();
        $allotments = $this->ucAllotmentModel->getUCReport($filter);

        $data = $this->prepareAllotmentData($allotments);

        $data['dmf'] = $this->canAddAllotment();
        $data['addUrl'] = $data['dmf'] ? site_url(Url::allotmentAdd) : '';

        return $this->template->view('Admin\UC\Views\submit', $data);
    }

    public function info(): string
    {
        $this->template->addPackage(['jquery_loading', 'datepicker', 'uploader'], true);

        $year = $this->getYear();
        $recipientId = $this->ucSubmitModel->getRecipientId($this->user->district_id, $this->user->fund_agency_id);

        $allotments = $this->ucAllotmentModel->getAllotments(['year' => $year, 'recipient_id' => $recipientId]);
        $submissions = $this->ucSubmitModel->getSubmissions(['year' => $year, 'recipient_id' => $recipientId]);

        $data = $this->prepareSubmissionData($allotments, $submissions, $year);

        $data['uploadUrl'] = site_url(Url::ucSubmitUpload);

        return $this->template->view('Admin\UC\Views\submit_info', $data);
    }

    public function add(): string
    {
        $jsonData = [
            'status' => false,
        ];

        if ($this->request->getMethod() === 'POST') {
            $uc = $this->getUCData();

            $this->ucSubmitModel->insert($uc);

            $jsonData = [
                'status' => true,
            ];
            $this->session->setFlashData('message', 'UC added.');
        } else {
            $jsonData = $this->getFormData('Add UC Submission');
        }

        return $this->response->setJSON($jsonData);
    }

    public function edit(): string
    {
        $jsonData = [
            'status' => false,
        ];

        $id = $this->request->getGet('id');

        if ($this->request->getMethod() === 'POST') {
            $uc = $this->getUCData();

            $this->ucSubmitModel->update($id, $uc);

            $jsonData = [
                'status' => true,
            ];
            $this->session->setFlashData('message', 'UC updated.');
        } else {
            $jsonData = $this->getFormData('Edit UC Submission');
        }

        return $this->response->setJSON($jsonData);
    }

    protected function getForm(): string
    {
        $data = [
            'dateSubmit' => '',
            'amount' => '',
            'letterNo' => '',
            'pageNo' => '',
            'document' => '',
            'documentName' => '',
            'documentUrl' => '',
        ];

        if ($this->request->getGet('id')) {
            $submission = $this->ucSubmitModel->find($this->request->getGet('id'));

            $data = [
                'dateSubmit' => ymdToDmy($submission->date_submit),
                'amount' => $submission->amount,
                'letterNo' => $submission->letter_no,
                'pageNo' => $submission->page_no,
                'document' => $submission->document,
            ];

            if ($submission->document) {
                $file = DIR_UPLOAD . $submission->document;
                $fileinfo = pathinfo($file);
                $filename = $fileinfo['basename'];
                $data['documentName'] = $filename;
                $data['documentUrl'] = '<a href="' . site_url($submission->document) . '">' . $filename . '</a>';
            }
        }

        return view('\Admin\UC\Views\submit_form', $data);
    }

    public function upload(): string
    {
        $validation = $this->validate([
            'document' => [
                'uploaded[document]',
                'mime_in[document,application/pdf]',
                'max_size[document,12048]',
                'ext_in[document,pdf,PDF]',
            ],
        ]);

        if (!$validation) {
            $data = [
                'status' => false,
                'message' => 'Invalid file',
                'errors' => $this->validator->getErrors(),
            ];
        } else {
            $file = $this->request->getFile
