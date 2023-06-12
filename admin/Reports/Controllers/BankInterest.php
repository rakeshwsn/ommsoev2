<?php 
namespace Admin\Reports\Controllers;
use Admin\Reports\Models\ReportsModel;
use Admin\Users\Models\UserGroupModel;
use App\Controllers\AdminController;

class BankInterest extends AdminController
{
    public function index() {
        $data = [];

        $data['year_id'] = getCurrentYearId();
        if($this->request->getGet('year')){
            $data['year_id'] = $this->request->getGet('year');
        }

        $data['month_id'] = getMonthIdByMonth(date('m'));
        if($this->request->getGet('month')){
            $data['month_id'] = $this->request->getGet('month');
        }

        $data['agency_type_id'] = '';
        if($this->request->getGet('agency_type_id')){
            $data['agency_type_id'] = $this->request->getGet('agency_type_id');
        }

        $data['years'] = getAllYears();
        $data['months'] = getAllMonths();

        $data['agency_types'] = [];
        foreach ($this->settings->user_can_access as $user_group => $user_can_access_grp) {
            if($this->user->agency_type_id==$user_group){
                $data['agency_types'] = (new UserGroupModel())->whereIn('id',
                    $user_can_access_grp)->orderBy('name')->asArray()->findAll();
            }
        }

        $reportModel = new ReportsModel();
        $filter = [
            'year_id' => $data['year_id'],
            'month_id' => $data['month_id'],
            'district_id' => $this->user->district_id,
            'agency_type_id' => [5,6,7],
            'fund_agency_id'=> $this->user->fund_agency_id,
        ];
        if($data['agency_type_id']){
            $filter['agency_type_id'] = $data['agency_type_id'];
        }

        $agencies = $reportModel->getInterestReport($filter);

        $data['agencies'] = [];
        $int_upto = 0;
        $int_mon = 0;
        $int_total = 0;
        $int_ref_block = 0;
        $balance = 0;
        foreach ($agencies as $agency) {
            $data['agencies'][] = [
                'agency' => $agency->block,
                'int_upto' => in_rupees($agency->tot_int_upto),
                'int_mon' => in_rupees($agency->tot_int_mon),
                'int_total' => in_rupees($agency->tot_int_upto+$agency->tot_int_mon),
                'int_ref_block' => in_rupees($agency->tot_ref),
                'balance' => in_rupees($agency->tot_int_upto+$agency->tot_int_mon-$agency->tot_ref),
            ];

            $int_upto += $agency->tot_int_upto;
            $int_mon += $agency->tot_int_mon;
            $int_total += $agency->tot_int_upto+$agency->tot_int_mon;
            $int_ref_block += $agency->tot_ref;
            $balance += $agency->tot_int_upto+$agency->tot_int_mon-$agency->tot_ref;
        }

        $data['total'] = [
            'agency' => 'Total',
            'int_upto' => in_rupees($int_upto),
            'int_mon' => in_rupees($int_mon),
            'int_total' => in_rupees($int_total),
            'int_ref_block' => in_rupees($int_ref_block),
            'balance' => in_rupees($balance)
        ];

        return $this->template->view('Admin\Reports\Views\bank_interest', $data);
    }
}
