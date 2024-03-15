<?php

namespace Admin\Transaction\Controllers;

use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Transaction\Models\ClosingbalanceModel;
use App\Controllers\AdminController;
use Config\Settings;
use Config\Url;

class OpeningBalance extends AdminController
{
    public function index()
    {
        $obModel = new ClosingbalanceModel();

        // Get the fund agency ID based on the user's agency type
        $fund_agency_id = $this->getFundAgencyId();

        // If the request method is POST
        if ($this->request->getMethod() === 'post') {
            // Validate the request data
            $this->validate([
                'advance' => 'required',
                'bank' => 'required',
                'cash' => 'required'
            ]);

            // If the validation passes, insert the opening balance data
            if ($this->validator->withRequest($this->request)->passes()) {
                $ob = [
                    'month' => 0,
                    'year' => 0,
                    'block_id' => $this->user->block_id,
                    'district_id' => $this->user->district_id,
                    'user_id' => $this->user->user_id,
                    'status' => 1,
                    'advance' => $this->request->getPost('advance'),
                    'bank' => $this->request->getPost('bank'),
                    'cash' => $this->request->getPost('cash'),
                    'agency_type_id' => $this->user->agency_type_id,
                    'fund_agency_id' => $fund_agency_id,
                ];

                $obModel->insert($ob);

                // If the user is a block user, insert the opening balance data for CBO
                if ($this->user->agency_type_id === $this->settings->block_user) {
                    $ob = [
                        'month' => 0,
                        'year' => 0,
                        'block_id' => $this->user->block_id,
                        'district_id' => $this->user->district_id,
                        'user_id' => $this->user->user_id,
                        'status' => 1,
                        'advance' => $this->request->getPost('advance'),
                        'bank' => $this->request->getPost('bank'),
                        'cash' => $this->request->getPost('cash'),
                        'agency_type_id' => $this->settings->cbo_user,
                        'fund_agency_id' => $fund_agency_id,
                    ];
                    $obModel->insert($ob);
                }

                // Redirect to the dashboard
                return redirect()->to(Url::dashboard);
            }
        }

        // Set the message and helper
        $data['message'] = 'Please enter opening balance to continue. This is one time setup.';
        helper('form');

        // Set the date and view data
        $month_num = date('m');
        $date = date('d/m', mktime(0, 0, 0, $month_num, 1)) . '/' . getCurrentYear();
        $data['date'] = $date;

        // Render the view
        return $this->template->view('Admin\Transaction\Views\opening_balance', $data);
    }

    /**
     * Get the fund agency ID based on the user's agency type
     *
     * @return int
     */
    private function getFundAgencyId()
    {
        $fund_agency_id = 0;

        if ($this->user->agency_type_id === $this->settings->block_user) {
            $blockModel = new BlockModel();
            $fund_agency_id = $blockModel->find($this->user->block_id)->fund_agency_id;
        }

        if ($this->user->agency_type_id === $this->settings->district_user) {
