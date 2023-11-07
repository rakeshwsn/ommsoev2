<?php

namespace Admin\Common\Controllers;

use App\Controllers\AdminController;

class Leftbar extends AdminController
{
    public function __construct()
    {
        $this->user = service("user");
    }
    public function index()
    {
        $data = [];

        // Dashboard
        $data["menus"][] = [
            "id" => "menu-dashboard",
            "icon" => "md-home",
            "name" => lang("Leftbar.text_dashboard"),
            "href" => admin_url("/"),
            "children" => [],
        ];

        $data["menus"][] = [
            "id" => "menu-navigation",
            "icon" => "",
            "name" => lang("Leftbar.text_soe"),
            "heading" => 1,
            "children" => [],
        ];

        // Component
        $components = [];

        if ($this->user->hasPermission("components")) {
            $components[] = [
                "name" => lang("Leftbar.text_component"),
                "href" => admin_url("components"),
                "heading" => 0,
                "children" => [],
            ];
        }
        if ($this->user->hasPermission("components/assign")) {
            $components[] = [
                "name" => lang("Leftbar.text_component_assign"),
                "href" => admin_url("components/assign"),
                "heading" => 0,
                "children" => [],
            ];
        }
        if ($this->user->hasPermission("components/agencyassign")) {
            $components[] = [
                "name" => lang("Leftbar.text_component_agency_assign"),
                "href" => admin_url("components/agencyassign"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($components) {
            $data["menus"][] = [
                "id" => "menu-component",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_components"),
                "heading" => 0,
                "href" => "",
                "children" => $components,
            ];
        }

        $budgets = [];

        if ($this->user->hasPermission("budgets")) {
            $budgets[] = [
                "name" => lang("Leftbar.text_budget"),
                "href" => admin_url("budgets"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("budgets/approval")) {
            $budgets[] = [
                "name" => lang("Leftbar.text_budgets_approval"),
                "href" => admin_url("budgets/approval"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($budgets) {
            $data["menus"][] = [
                "id" => "menu-budgets",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_budgets"),
                "heading" => 0,
                "href" => "",
                "children" => $budgets,
            ];
        }

        if ($this->user->hasPermission("transaction")) {
            $data["menus"][] = [
                "id" => "menu-transaction",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_transactions"),
                "heading" => 0,
                "href" => admin_url("transaction"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("otherreceipt")) {
            $data["menus"][] = [
                "id" => "menu-otherreceipt",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_otherreceipt"),
                "heading" => 0,
                "href" => admin_url("otherreceipt"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("refundtoatma")) {
            $data["menus"][] = [
                "id" => "menu-refundtoatma",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_refundatma"),
                "heading" => 0,
                "href" => admin_url("refundtoatma"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("closingbalance")) {
            $data["menus"][] = [
                "id" => "menu-closingbalance",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_closingbalance"),
                "heading" => 0,
                "href" => admin_url("closingbalance"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("correction")) {
            $data["menus"][] = [
                "id" => "menu-correction",
                "icon" => "si si-folder-alt",
                "name" => 'FA Correction',
                "heading" => 0,
                "href" => admin_url("correction"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("approve")) {
            $data["menus"][] = [
                "id" => "menu-approve",
                "icon" => "si si-check",
                "name" => lang("Leftbar.text_soe_approve"),
                "heading" => 0,
                "href" => admin_url("approve"),
                "children" => [],
            ];
        }

        $reports = [];

        if ($this->user->hasPermission("reports/mpr")) {
            $reports[] = [
                "name" => lang("Leftbar.text_mpr"),
                "href" => admin_url("reports/mpr"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("reports/abstractmpr")) {
            $reports[] = [
                "name" => lang("Leftbar.text_abstract_mpr"),
                "href" => admin_url("reports/abstractmpr"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("mpr/state")) {
            $reports[] = [
                "name" => lang("Leftbar.text_state_mpr"),
                "href" => admin_url("mpr/state"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("reports/bankinterest")) {
            $reports[] = [
                "name" => lang("Leftbar.text_bankinterest"),
                "href" => admin_url("reports/bankinterest"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("reports/sfp")) {
            $reports[] = [
                "name" => lang("Leftbar.text_sfp"),
                "href" => admin_url("reports/sfp"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("reports/uploadstatus")) {
            $reports[] = [
                "name" => lang("Leftbar.text_uploadstatus"),
                "href" => admin_url("reports/uploadstatus"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission('reports/mpr/status')) {
            $reports[] = array(
                'name' => "MPR Upload Status",
                'href' => admin_url('reports/mpr/status'),
                'heading' => 0,
                'children' => array()
            );
        }

        if ($this->user->hasPermission('reports/mprupload')) {
            $reports[] = array(
                'name' => 'MPR Upload',
                'href' => admin_url('reports/mprupload'),
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

        if ($this->user->hasPermission("incentive/uploadstatus")) {
            $reports[] = [
                "name" => lang("Leftbar.text_incentive_status"),
                "href" => admin_url("incentive/uploadstatus"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($reports) {
            $data["menus"][] = [
                "id" => "menu-reports",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_reports"),
                "heading" => 0,
                "href" => "",
                "children" => $reports,
            ];
        }

        //dashboards
        $dashboards = [];

        if ($this->user->hasPermission("dashboard/areacoverage")) {
            $dashboards[] = [
                "name" => 'Area Coverage',
                "href" => admin_url("dashboard/areacoverage"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("dashboard/procurement")) {
            $dashboards[] = [
                "name" => 'Procurement',
                "href" => admin_url("dashboard/procurement"),
                "heading" => 0,
                "children" => [],
            ];
        }
        if ($this->user->hasPermission("dashboard/establishment")) {
            $dashboards[] = [
                "name" => 'Establishment',
                "href" => admin_url("dashboard/establishment"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("dashboard/pds")) {
            $dashboards[] = [
                "name" => 'PDS',
                "href" => admin_url("dashboard/pds"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("dashboard/enterprise")) {
            $dashboards[] = [
                "name" => 'Enterprises',
                "href" => admin_url("dashboard/enterprise"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("dashboard/odishamap/chart")) {
            $dashboards[] = [
                "name" => 'Odisha Map',
                "href" => admin_url("dashboard/odishamap/chart"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("dashboard/map")) {
            $dashboards[] = [
                "name" => 'Map',
                "href" => admin_url("dashboard/map"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("dashboard/chartnew")) {
            $dashboards[] = [
                "name" => 'Chart',
                "href" => admin_url("dashboard/chartnew"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($dashboards) {
            $data["menus"][] = [
                "id" => "menu-dashboard",
                "icon" => "md-dashboard",
                "name" => 'Dashboards',
                "heading" => 0,
                "href" => "",
                "children" => $dashboards,
            ];
        }

        if ($this->user->hasPermission("uc/allotment")) {
            $data["menus"][] = [
                "id" => "menu-allotment",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_uc_allotment"),
                "heading" => 0,
                "href" => admin_url("uc/allotment"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("uc/submit")) {
            $data["menus"][] = [
                "id" => "menu-ucsubmit",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_uc_submit"),
                "heading" => 0,
                "href" => admin_url("uc/submit"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("transaction/refund")) {
            $data["menus"][] = [
                "id" => "menu-refund",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_trefund"),
                "heading" => 0,
                "href" => admin_url("transaction/refund"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("oldportallogin")) {
            $data["menus"][] = [
                "id" => "menu-old-portal-login",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_oldportal"),
                "heading" => 0,
                "href" => admin_url("oldportallogin"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("mis")) {
            $data["menus"][] = [
                "id" => "menu-mis",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_mis"),
                "heading" => 0,
                "href" => admin_url("mis"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("fpo")) {
            $data["menus"][] = [
                "id" => "menu-fpo",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_fpo"),
                "heading" => 0,
                "href" => admin_url("fpo"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("incentive")) {
            $data["menus"][] = [
                "id" => "menu-incentive",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_incentive"),
                "heading" => 0,
                "href" => admin_url("incentive"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("event")) {
            $data["menus"][] = [
                "id" => "menu-event",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_event"),
                "heading" => 0,
                "href" => admin_url("event"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("pendingstatus")) {
            $data["menus"][] = [
                "id" => "menu-pendingstatus",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_pendingstatus"),
                "heading" => 0,
                "href" => admin_url("pendingstatus"),
                "children" => [],
            ];
        }

        $enterprises = [];

        if ($this->user->hasPermission("enterprises")) {
            $enterprises[] = [
                "name" => 'Enterprises Transaction',
                "href" => admin_url("enterprises/transaction"),
                "heading" => 0,
                "children" => [],
            ];
        }
        if ($this->user->hasPermission("enterprises")) {
            $enterprises[] = [
                "name" => 'Enterprises Units',
                "href" => admin_url("enterpriseunit"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("enterprises")) {
            $enterprises[] = [
                "name" => 'Enterprises',
                "href" => admin_url("enterprises"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($enterprises) {
            $data["menus"][] = [
                "id" => "menu-enterprises",
                "icon" => "md-account-child",
                "name" => 'Enterprises',
                "heading" => 0,
                "href" => "",
                "children" => $enterprises,
            ];
        }



        $areacoverage = [];

        if ($this->user->hasPermission("areacoverage")) {
            $areacoverage[] = [
                "name" => lang("Leftbar.text_areacoverage"),
                "href" => admin_url("areacoverage"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("areacoverage/grampanchayat")) {
            $areacoverage[] = [
                "name" => lang("Leftbar.text_grampanchayat"),
                "href" => admin_url("areacoverage/grampanchayat"),
                "heading" => 0,
                "children" => [],
            ];
        }
        if ($this->user->hasPermission("areacoverage/target")) {
            $areacoverage[] = [
                "name" => lang("Target"),
                "href" => admin_url("areacoverage/target"),
                "heading" => 0,
                "children" => [],
            ];
        }
        if ($this->user->hasPermission("areacoverage/approve")) {
            $areacoverage[] = [
                "name" => lang("Approve"),
                "href" => admin_url("areacoverage/approve"),
                "heading" => 0,
                "children" => [],
            ];
        }
        if ($this->user->hasPermission("areacoverage/dashboard")) {
            $areacoverage[] = [
                "name" => lang("Dashboard"),
                "href" => admin_url("areacoverage/dashboard"),
                "heading" => 0,
                "children" => [],
            ];
        }
        if ($this->user->hasPermission("reports/areacoverage")) {
            $areacoverage[] = [
                "name" => "Area Coverage Report",
                "href" => admin_url("reports/areacoverage"),
                "heading" => 0,
                "children" => [],
            ];
        }
        if ($this->user->hasPermission("reports/areacoverage/allblocks")) {
            $areacoverage[] = [
                "name" => "Area Coverage Blockwise Report",
                "href" => admin_url("reports/areacoverage/allblocks"),
                "heading" => 0,
                "children" => [],
            ];
        }
        if (
            $this->user->hasPermission("reports/areacoverage/getUploadStatus")
        ) {
            $areacoverage[] = [
                "name" => "Area Coverage Upload Status",
                "href" => admin_url("reports/areacoverage/getUploadStatus"),
                "heading" => 0,
                "children" => [],
            ];
        }
        if (
            $this->user->hasPermission("reports/areacoverage/blockWiseGetUploadStatus")
        ) {
            $areacoverage[] = [
                "name" => "Block Wise Area Coverage Upload Status",
                "href" => admin_url("reports/areacoverage/blockWiseGetUploadStatus"),
                "heading" => 0,
                "children" => [],
            ];
        }
        if (
            $this->user->hasPermission("areacoverage/targetVsAchievement")
        ) {
            $areacoverage[] = [
                "name" => "Target Vs Achievement",
                "href" => admin_url("areacoverage/targetVsAchievement"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("cropcoverage/crops")) {
            $areacoverage[] = [
                "name" => lang("Leftbar.text_crop_add"),
                "href" => admin_url("cropcoverage/crops"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("areacoverage/delete")) {
            $areacoverage[] = [
                "name" => 'Delete Area Coverage',
                "href" => admin_url("areacoverage/delete"),
                "heading" => 0,
                "children" => [],
            ];
        }
        if ($this->user->hasPermission("areacoverage/finaldata")) {
            $areacoverage[] = [
                "name" => 'Final Data',
                "href" => admin_url("areacoverage/finaldata"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($areacoverage) {
            $data["menus"][] = [
                "id" => "menu-areacoverage",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_areacoverages"),
                "heading" => 0,
                "href" => "",
                "children" => $areacoverage,
            ];
        }

        $mprcomponent = [];

        if ($this->user->hasPermission("physicalcomponents")) {
            $mprcomponent[] = [
                "name" => "Physical Component",
                "href" => admin_url("physicalcomponents"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("physicalcomponentstarget")) {
            $mprcomponent[] = [
                "name" => "MPR Target",
                "href" => admin_url("physicalcomponentstarget"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("phyachtraining")) {
            $mprcomponent[] = [
                "name" => "MPR Ach Training Report",
                "href" => admin_url("phyachtraining"),
                "heading" => 0,
                "children" => [],
            ];
        }
        if ($this->user->hasPermission("phyachenterprise")) {
            $mprcomponent[] = [
                "name" => "MPR Ach Enterprises Report",
                "href" => admin_url("phyachenterprise"),
                "heading" => 0,
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("physicalachievement/report")) {
            $mprcomponent[] = [
                "name" => "Upload Status",
                "href" => admin_url("physicalachievement/report"),
                "heading" => 0,
                "children" => [],
            ];
        }
        if ($mprcomponent) {
            $data["menus"][] = [
                "id" => "menu-mprcomponent",
                "icon" => "md-account-child",
                "name" => "MPR Components",
                "heading" => 0,
                "href" => "",
                "children" => $mprcomponent,
            ];
        }

        if ($this->user->hasPermission("letters")) {
            $data["menus"][] = [
                "id" => "menu-letter",
                "icon" => "md-account-child",
                "name" => lang("Leftbar.text_letters"),
                "heading" => 0,
                "href" => admin_url("letters"),
                "children" => [],
            ];
        }

        $data["menus"][] = [
            "id" => "menu-navigation",
            "icon" => "",
            "name" => lang("Leftbar.text_general"),
            "heading" => 1,
            "children" => [],
        ];

        // localization

        $localisation = [];

        if ($this->user->hasPermission("district")) {
            $localisation[] = [
                "name" => lang("Leftbar.text_district"),
                "href" => admin_url("district"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("block")) {
            $localisation[] = [
                "name" => lang("Leftbar.text_block"),
                "href" => admin_url("block"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("grampanchayat")) {
            $localisation[] = [
                "name" => lang("Leftbar.text_grampanchayat"),
                "href" => admin_url("grampanchayat"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("village")) {
            $localisation[] = [
                "name" => lang("Leftbar.text_village"),
                "href" => admin_url("village"),
                "children" => [],
            ];
        }

        if ($localisation) {
            $data["menus"][] = [
                "id" => "menu-localisations",
                "icon" => "md-localisations",
                "name" => lang("Leftbar.text_localisation"),
                "href" => "",
                "children" => $localisation,
            ];
        }

        // users
        $user = [];

        if ($this->user->hasPermission("user")) {
            $user[] = [
                "name" => lang("Leftbar.text_user"),
                "href" => admin_url("users"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("users/members")) {
            $user[] = [
                "name" => lang("Leftbar.text_member"),
                "href" => admin_url("members"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("users/usergroup")) {
            $user[] = [
                "name" => lang("Leftbar.text_role"),
                "href" => admin_url("usergroup"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("permission")) {
            $user[] = [
                "name" => lang("Leftbar.text_permission"),
                "href" => admin_url("permission"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("users/allowupload")) {
            $user[] = [
                "name" => lang("Leftbar.text_allowupload"),
                "href" => admin_url("users/allowupload"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("users/uploadstatus")) {
            $user[] = [
                "name" => lang("Leftbar.text_uploadstatus"),
                "href" => admin_url("users/uploadstatus"),
                "children" => [],
            ];
        }

        if ($user) {
            $data["menus"][] = [
                "id" => "menu-user",
                "icon" => "md-users",
                "name" => lang("Leftbar.text_users"),
                "href" => "",
                "children" => $user,
            ];
        }

        // System
        $system = [];

        if ($this->user->hasPermission("setting")) {
            $system[] = [
                "name" => lang("Leftbar.text_setting"),
                "href" => admin_url("setting"),
                "children" => [],
            ];
        }

        if ($this->user->hasPermission("setting/serverinfo")) {
            $system[] = [
                "name" => lang("Leftbar.text_serverinfo"),
                "href" => admin_url("setting/serverinfo"),
                "children" => [],
            ];
        }

        if ($system) {
            $data["menus"][] = [
                "id" => "menu-system",
                "icon" => "md-settings",
                "name" => lang("Leftbar.text_system"),
                "href" => "",
                "children" => $system,
            ];
        }

        return view("Admin\Common\Views\leftbar", $data);
    }
}

/* End of file templates.php */
/* Location: ./application/modules/templates/controllers/templates.php */