<?php
namespace Admin\Common\Controllers;

use App\Controllers\AdminController;

class Leftbar extends AdminController
{

    public function __construct()
    {
        $this->user = service('user');
    }
    public function index()
    {

        $data = array();

        // Dashboard
        $data['menus'][] = array(
            'id' => 'menu-dashboard',
            'icon' => 'md-home',
            'name' => lang('Leftbar.text_dashboard'),
            'href' => admin_url('/'),
            'children' => array()
        );

        $data['menus'][] = array(
            'id' => 'menu-navigation',
            'icon' => '',
            'name' => lang('Leftbar.text_soe'),
            'heading' => 1,
            'children' => array()
        );


        // Component
        $components = array();

        if ($this->user->hasPermission('component')) {
            $components[] = array(
                'name' => lang('Leftbar.text_component'),
                'href' => admin_url('components'),
                'heading' => 0,
                'children' => array()
            );
        }
        if ($this->user->hasPermission('component/assign')) {
            $components[] = array(
                'name' => lang('Leftbar.text_component_assign'),
                'href' => admin_url('components/assign'),
                'heading' => 0,
                'children' => array()
            );
        }
        if ($this->user->hasPermission('component/agencyassign')) {
            $components[] = array(
                'name' => lang('Leftbar.text_component_agency_assign'),
                'href' => admin_url('component/agencyassign'),
                'heading' => 0,
                'children' => array()
            );
        }


        if ($components) {
            $data['menus'][] = array(
                'id' => 'menu-component',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_components'),
                'heading' => 0,
                'href' => '',
                'children' => $components
            );
        }

        $budgets = array();

        if ($this->user->hasPermission('budgets')) {
            $budgets[] = array(
                'name' => lang('Leftbar.text_budget'),
                'href' => admin_url('budgets'),
                'heading' => 0,
                'children' => array()
            );
        }

        if ($this->user->hasPermission('budgets/approval')) {
            $budgets[] = array(
                'name' => lang('Leftbar.text_budgets_approval'),
                'href' => admin_url('budgets/approval'),
                'heading' => 0,
                'children' => array()
            );
        }


        if ($budgets) {
            $data['menus'][] = array(
                'id' => 'menu-budgets',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_budgets'),
                'heading' => 0,
                'href' => '',
                'children' => $budgets
            );
        }

        if ($this->user->hasPermission('transaction')) {
            $data['menus'][] = array(
                'id' => 'menu-transaction',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_transactions'),
                'heading' => 0,
                'href' => admin_url('transaction'),
                'children' => []
            );
        }

        if ($this->user->hasPermission('otherreceipt')) {
            $data['menus'][] = array(
                'id' => 'menu-otherreceipt',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_otherreceipt'),
                'heading' => 0,
                'href' => admin_url('otherreceipt'),
                'children' => []
            );
        }

        if ($this->user->hasPermission('refundtoatma')) {
            $data['menus'][] = array(
                'id' => 'menu-refundtoatma',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_refundatma'),
                'heading' => 0,
                'href' => admin_url('refundtoatma'),
                'children' => []
            );
        }

        if ($this->user->hasPermission('closingbalance')) {
            $data['menus'][] = array(
                'id' => 'menu-closingbalance',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_closingbalance'),
                'heading' => 0,
                'href' => admin_url('closingbalance'),
                'children' => []
            );
        }

        if ($this->user->hasPermission('approve')) {
            $data['menus'][] = array(
                'id' => 'menu-approve',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_soe_approve'),
                'heading' => 0,
                'href' => admin_url('approve'),
                'children' => []
            );
        }


        $reports = array();

        if ($this->user->hasPermission('reports/mpr')) {
            $reports[] = array(
                'name' => lang('Leftbar.text_mpr'),
                'href' => admin_url('reports/mpr'),
                'heading' => 0,
                'children' => array()
            );
        }

        if ($this->user->hasPermission('reports/abstractmpr')) {
            $reports[] = array(
                'name' => lang('Leftbar.text_abstract_mpr'),
                'href' => admin_url('reports/abstractmpr'),
                'heading' => 0,
                'children' => array()
            );
        }

        if ($this->user->hasPermission('mpr/state')) {
            $reports[] = array(
                'name' => lang('Leftbar.text_state_mpr'),
                'href' => admin_url('mpr/state'),
                'heading' => 0,
                'children' => array()
            );
        }

        if ($this->user->hasPermission('reports/bankinterest')) {
            $reports[] = array(
                'name' => lang('Leftbar.text_bankinterest'),
                'href' => admin_url('reports/bankinterest'),
                'heading' => 0,
                'children' => array()
            );
        }

        if ($this->user->hasPermission('reports/sfp')) {
            $reports[] = array(
                'name' => lang('Leftbar.text_sfp'),
                'href' => admin_url('reports/sfp'),
                'heading' => 0,
                'children' => array()
            );
        }

        if ($this->user->hasPermission('reports/uploadstatus')) {
            $reports[] = array(
                'name' => lang('Leftbar.text_uploadstatus'),
                'href' => admin_url('reports/uploadstatus'),
                'heading' => 0,
                'children' => array()
            );
        }

        if ($this->user->hasPermission('reports/oldmpr')) {
            $reports[] = array(
                'name' => lang('Leftbar.text_last_mpr'),
                'href' => admin_url('reports/oldmpr'),
                'heading' => 0,
                'children' => array()
            );
        }

        if ($this->user->hasPermission('incentive/uploadstatus')) {
            $reports[] = array(
                'name' => lang('Leftbar.text_incentive_status'),
                'href' => admin_url('incentive/uploadstatus'),
                'heading' => 0,
                'children' => array()
            );
        }

        if ($reports) {
            $data['menus'][] = array(
                'id' => 'menu-reports',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_reports'),
                'heading' => 0,
                'href' => '',
                'children' => $reports
            );
        }


        if ($this->user->hasPermission('uc/allotment')) {
            $data['menus'][] = array(
                'id' => 'menu-allotment',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_uc_allotment'),
                'heading' => 0,
                'href' => admin_url('uc/allotment'),
                'children' => []
            );
        }

        if ($this->user->hasPermission('uc/submit')) {
            $data['menus'][] = array(
                'id' => 'menu-ucsubmit',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_uc_submit'),
                'heading' => 0,
                'href' => admin_url('uc/submit'),
                'children' => []
            );
        }

        if ($this->user->hasPermission('transaction/refund')) {
            $data['menus'][] = array(
                'id' => 'menu-ucsubmit',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_trefund'),
                'heading' => 0,
                'href' => admin_url('transaction/refund'),
                'children' => []
            );
        }

        if ($this->user->hasPermission('oldportallogin')) {
            $data['menus'][] = array(
                'id' => 'menu-ucsubmit',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_oldportal'),
                'heading' => 0,
                'href' => admin_url('oldportallogin'),
                'children' => []
            );
        }


        if ($this->user->hasPermission('mis')) {
            $data['menus'][] = array(
                'id' => 'menu-mis',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_mis'),
                'heading' => 0,
                'href' => admin_url('mis'),
                'children' => []
            );
        }

        if ($this->user->hasPermission('fpo')) {
            $data['menus'][] = array(
                'id' => 'menu-fpo',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_fpo'),
                'heading' => 0,
                'href' => admin_url('fpo'),
                'children' => []
            );
        }

        if ($this->user->hasPermission('incentive')) {
            $data['menus'][] = array(
                'id' => 'menu-incentive',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_incentive'),
                'heading' => 0,
                'href' => admin_url('incentive'),
                'children' => []
            );
        }

        if ($this->user->hasPermission('event')) {
            $data['menus'][] = array(
                'id' => 'menu-event',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_event'),
                'heading' => 0,
                'href' => admin_url('event'),
                'children' => []
            );
        }




        if ($this->user->hasPermission('pendingstatus')) {
            $data['menus'][] = array(
                'id' => 'menu-pendingstatus',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_pendingstatus'),
                'heading' => 0,
                'href' => admin_url('pendingstatus'),
                'children' => []
            );
        }


        $areacoverage = array();

        if ($this->user->hasPermission('areacoverage')) {
            $areacoverage[] = array(
                'name' => lang('Leftbar.text_areacoverage'),
                'href' => admin_url('areacoverage'),
                'heading' => 0,
                'children' => array()
            );
        }

        if ($this->user->hasPermission('areacoverage/grampanchayat')) {
            $areacoverage[] = array(
                'name' => lang('Leftbar.text_grampanchayat'),
                'href' => admin_url('areacoverage/grampanchayat'),
                'heading' => 0,
                'children' => array()
            );
        }
        if ($this->user->hasPermission('areacoverage/target')) {
            $areacoverage[] = array(
                'name' => lang('Target'),
                'href' => admin_url('areacoverage/target'),
                'heading' => 0,
                'children' => array()
            );
        }
        if ($this->user->hasPermission('areacoverage/approve')) {
            $areacoverage[] = array(
                'name' => lang('Approve'),
                'href' => admin_url('areacoverage/approve'),
                'heading' => 0,
                'children' => array()
            );
        }
        if ($this->user->hasPermission('areacoverage/dashboard')) {
            $areacoverage[] = array(
                'name' => lang('Dashboard'),
                'href' => admin_url('areacoverage/dashboard'),
                'heading' => 0,
                'children' => array()
            );
        }


        if ($this->user->hasPermission('cropcoverage/crops')) {
            $areacoverage[] = array(
                'name' => lang('Leftbar.text_crop_add'),
                'href' => admin_url('cropcoverage/crops'),
                'heading' => 0,
                'children' => array()
            );
        }

        if ($areacoverage) {
            $data['menus'][] = array(
                'id' => 'menu-areacoverage',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_areacoverages'),
                'heading' => 0,
                'href' => '',
                'children' => $areacoverage
            );
        }


        $mprcomponent = array();

        if ($this->user->hasPermission('physicalcomponents')) {
            $mprcomponent[] = array(
                'name' => 'Physical Component',
                'href' => admin_url('physicalcomponents'),
                'heading' => 0,
                'children' => array()
            );
        }

        if ($this->user->hasPermission('physicalcomponentstarget')) {
            $mprcomponent[] = array(
                'name' => 'MPR Target',
                'href' => admin_url('physicalcomponentstarget'),
                'heading' => 0,
                'children' => array()
            );
        }

        if ($this->user->hasPermission('physicalachievement')) {
            $mprcomponent[] = array(
                'name' => 'MPR Achievements',
                'href' => admin_url('physicalachievement'),
                'heading' => 0,
                'children' => array()
            );
        }

        if ($mprcomponent) {
            $data['menus'][] = array(
                'id' => 'menu-mprcomponent',
                'icon' => 'md-account-child',
                'name' => 'MPR Components',
                'heading' => 0,
                'href' => '',
                'children' => $mprcomponent
            );
        }




        if ($this->user->hasPermission('letter')) {
            $data['menus'][] = array(
                'id' => 'menu-letter',
                'icon' => 'md-account-child',
                'name' => lang('Leftbar.text_letters'),
                'heading' => 0,
                'href' => admin_url('letter'),
                'children' => []
            );
        }


        $data['menus'][] = array(
            'id' => 'menu-navigation',
            'icon' => '',
            'name' => lang('Leftbar.text_general'),
            'heading' => 1,
            'children' => array()
        );

    
        // localization

        $localisation = array();

        if ($this->user->hasPermission('district')) {
            $localisation[] = array(
                'name' => lang('Leftbar.text_district'),
                'href' => admin_url('district'),
                'children' => array()
            );
        }

        if ($this->user->hasPermission('block')) {
            $localisation[] = array(
                'name' => lang('Leftbar.text_block'),
                'href' => admin_url('block'),
                'children' => array()
            );
        }

        if ($this->user->hasPermission('grampanchayat')) {
            $localisation[] = array(
                'name' => lang('Leftbar.text_grampanchayat'),
                'href' => admin_url('grampanchayat'),
                'children' => array()
            );
        }


        if ($this->user->hasPermission('village')) {
            $localisation[] = array(
                'name' => lang('Leftbar.text_village'),
                'href' => admin_url('village'),
                'children' => array()
            );
        }

        if ($localisation) {
            $data['menus'][] = array(
                'id' => 'menu-localisations',
                'icon' => 'md-localisations',
                'name' => lang('Leftbar.text_localisation'),
                'href' => '',
                'children' => $localisation
            );

        }

        // users
        $user = array();

        if ($this->user->hasPermission('user')) {
            $user[] = array(
                'name' => lang('Leftbar.text_user'),
                'href' => admin_url('users'),
                'children' => array()
            );
        }

        if ($this->user->hasPermission('users/members')) {
            $user[] = array(
                'name' => lang('Leftbar.text_member'),
                'href' => admin_url('members'),
                'children' => array()
            );
        }

        if ($this->user->hasPermission('users/usergroup')) {
            $user[] = array(
                'name' => lang('Leftbar.text_role'),
                'href' => admin_url('usergroup'),
                'children' => array()
            );
        }

        if ($this->user->hasPermission('permission')) {
            $user[] = array(
                'name' => lang('Leftbar.text_permission'),
                'href' => admin_url('permission'),
                'children' => array()
            );
        }


        if ($user) {
            $data['menus'][] = array(
                'id' => 'menu-user',
                'icon' => 'md-users',
                'name' => lang('Leftbar.text_users'),
                'href' => '',
                'children' => $user
            );
        }


        // System
        $system = array();

        if ($this->user->hasPermission('setting')) {
            $system[] = array(
                'name' => lang('Leftbar.text_setting'),
                'href' => admin_url('setting'),
                'children' => array()
            );
        }


        if ($this->user->hasPermission('setting/serverinfo')) {
            $system[] = array(
                'name' => lang('Leftbar.text_serverinfo'),
                'href' => admin_url('setting/serverinfo'),
                'children' => array()
            );
        }


        if ($system) {
            $data['menus'][] = array(
                'id' => 'menu-system',
                'icon' => 'md-settings',
                'name' => lang('Leftbar.text_system'),
                'href' => '',
                'children' => $system
            );
        }

        return view('Admin\Common\Views\leftbar', $data);
    }
}

/* End of file templates.php */
/* Location: ./application/modules/templates/controllers/templates.php */