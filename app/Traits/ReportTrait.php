<?php

namespace App\Traits;

use Admin\Reports\Models\ReportsModel;
use Config\ExcelStyles;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

trait ReportTrait {

    protected function getTable($array,$action) {
        $this->tot_bud_phy = $this->tot_bud_fin = $this->tot_ob_phy = $this->tot_ob_fin = $this->tot_fr_upto_phy = $this->tot_fr_upto_fin = 0;
        $this->tot_fr_mon_phy = $this->tot_fr_mon_fin = $this->tot_fr_cum_phy = $this->tot_fr_cum_fin = 0;

        $this->tot_cb_phy = $this->tot_cb_fin = $this->tot_exp_upto_phy = $this->tot_exp_upto_fin = 0;
        $this->tot_exp_mon_phy = $this->tot_exp_mon_fin = $this->tot_exp_cum_phy = $this->tot_exp_cum_fin = 0;

        if($action=='view'){
            $html = $this->generateTable($array,$action);
            //grand total
            $html .= '<tr class="subtotal bg-yellow highlight-heading2">
                    <td colspan="2">Grand Total</td>
                    <td>'.$this->tot_ob_phy.'</td>
                    <td>'.in_lakh($this->tot_ob_fin).'</td>
                    <td>'.$this->tot_bud_phy.'</td>
                    <td>'.in_lakh($this->tot_bud_fin).'</td>
                    <td>'.$this->tot_fr_upto_phy.'</td>
                    <td>'.in_lakh($this->tot_fr_upto_fin).'</td>
                    <td>'.$this->tot_fr_mon_phy.'</td>
                    <td>'.in_lakh($this->tot_fr_mon_fin).'</td>
                    <td>'.$this->tot_fr_cum_phy.'</td>
                    <td>'.in_lakh($this->tot_fr_cum_fin).'</td>
                    <td>'.$this->tot_exp_mon_phy.'</td>
                    <td>'.in_lakh($this->tot_exp_mon_fin).'</td>
                    <td>'.$this->tot_exp_cum_phy.'</td>
                    <td>'.in_lakh($this->tot_exp_cum_fin).'</td>
                    <td>'.$this->tot_cb_phy.'</td>
                    <td>'.in_lakh($this->tot_cb_fin).'</td>
                    </tr>';

            return $html;
        }
        if($action=='download'){ //show in rupees when downloaded
            $html = $this->generateTable($array,$action);
            //grand total
            $html .= '<tr class="subtotal bg-yellow highlight-heading2">
                    <td colspan="2">Grand Total</td>
                    <td>'.$this->tot_ob_phy.'</td>
                    <td>'.$this->tot_ob_fin.'</td>
                    <td>'.$this->tot_bud_phy.'</td>
                    <td>'.$this->tot_bud_fin.'</td>
                    <td>'.$this->tot_fr_upto_phy.'</td>
                    <td>'.$this->tot_fr_upto_fin.'</td>
                    <td>'.$this->tot_fr_mon_phy.'</td>
                    <td>'.$this->tot_fr_mon_fin.'</td>
                    <td>'.$this->tot_fr_cum_phy.'</td>
                    <td>'.$this->tot_fr_cum_fin.'</td>
                    <td>'.$this->tot_exp_mon_phy.'</td>
                    <td>'.$this->tot_exp_mon_fin.'</td>
                    <td>'.$this->tot_exp_cum_phy.'</td>
                    <td>'.$this->tot_exp_cum_fin.'</td>
                    <td>'.$this->tot_cb_phy.'</td>
                    <td>'.$this->tot_cb_fin.'</td>
                    </tr>';

            return $html;
        }
        if($action=='array'){
            $this->addSubtotal($array);
            //grand total

            return $array;
        }
        
    }

    protected function addSubtotal(array &$components,$has_child=false){
        $this->initVars();
        foreach ($components as $key => &$component) {
            if ($component['row_type']=='heading' && !isset($component['children'])){
                continue;
            }
            if($component['row_type']!='heading') {
                $this->calcTotals($component);
            }
            if (isset($component['children']) && $component['children']){
                $this->addSubtotal($component['children'],true);
            }
            if(!$has_child){
                $component['subtotal'] = [
                    'component_id' => rand(999,9999),
                    'number' => '',
                    'description' => 'Sub Total',
                    'agency_type' => '',
                    'parent' => $component['component_id'],
                    'sort_order' => '',
                    'row_type' => 'subtotal',
                    'ob_phy' => $this->ob_phy,
                    'ob_fin' => $this->ob_fin,
                    'bud_phy' => $this->bud_phy,
                    'bud_fin' => $this->bud_fin,
                    'fr_upto_phy' => $this->fr_upto_phy,
                    'fr_upto_fin' => $this->fr_upto_fin,
                    'fr_mon_phy' => $this->fr_mon_phy,
                    'fr_mon_fin' => $this->fr_mon_fin,
                    'fr_cum_phy' => $this->fr_cum_phy,
                    'fr_cum_fin' => $this->fr_cum_fin,
                    'exp_upto_phy' => $this->exp_upto_phy,
                    'exp_upto_fin' => $this->exp_upto_fin,
                    'exp_mon_phy' => $this->exp_mon_phy,
                    'exp_mon_fin' => $this->exp_mon_fin,
                    'exp_cum_phy' => $this->exp_cum_phy,
                    'exp_cum_fin' => $this->exp_cum_fin,
                    'cb_phy' => $this->cb_phy,
                    'cb_fin' => $this->cb_fin
                ];
            }
        }
    }

    protected function initVars(){
        $this->ob_phy = $this->ob_fin = $this->bud_phy = $this->bud_fin = $this->fr_upto_phy = $this->fr_upto_fin = 0;
        $this->fr_mon_phy = $this->fr_mon_fin = $this->fr_cum_phy = $this->fr_cum_fin = 0;

        $this->cb_phy = $this->cb_fin = $this->exp_upto_phy = $this->exp_upto_fin = 0;
        $this->exp_mon_phy = $this->exp_mon_fin = $this->exp_cum_phy = $this->exp_cum_fin = 0;
    }

    protected function calcTotals($component){
        $this->ob_phy += (int)$component['ob_phy'];
        $this->ob_fin += (float)$component['ob_fin'];
        $this->bud_phy += (int)$component['bud_phy'];
        $this->bud_fin += (float)$component['bud_fin'];
        $this->exp_mon_phy += (int)$component['exp_mon_phy'];
        $this->exp_mon_fin += (float)$component['exp_mon_fin'];
        $this->exp_cum_phy += (int)$component['exp_cum_phy'];
        $this->exp_cum_fin += (float)$component['exp_cum_fin'];
        $this->fr_upto_phy += (int)$component['fr_upto_phy'];
        $this->fr_upto_fin += (float)$component['fr_upto_fin'];
        $this->fr_mon_phy += (int)$component['fr_mon_phy'];
        $this->fr_mon_fin += (float)$component['fr_mon_fin'];
        $this->fr_cum_phy += (int)$component['fr_cum_phy'];
        $this->fr_cum_fin += (float)$component['fr_cum_fin'];
        $this->cb_phy += $component['cb_phy'];
        $this->cb_fin += $component['cb_fin'];

        //total
        $this->tot_ob_phy += (int)$component['ob_phy'];
        $this->tot_ob_fin += (float)$component['ob_fin'];
        $this->tot_bud_phy += (int)$component['bud_phy'];
        $this->tot_bud_fin += (float)$component['bud_fin'];
        $this->tot_exp_mon_phy += (int)$component['exp_mon_phy'];
        $this->tot_exp_mon_fin += (float)$component['exp_mon_fin'];
        $this->tot_exp_cum_phy += (int)$component['exp_cum_phy'];
        $this->tot_exp_cum_fin += (float)$component['exp_cum_fin'];
        $this->tot_fr_upto_phy += (int)$component['fr_upto_phy'];
        $this->tot_fr_upto_fin += (float)$component['fr_upto_fin'];
        $this->tot_fr_mon_phy += (int)$component['fr_mon_phy'];
        $this->tot_fr_mon_fin += (float)$component['fr_mon_fin'];
        $this->tot_fr_cum_phy += (int)$component['fr_cum_phy'];
        $this->tot_fr_cum_fin += (float)$component['fr_cum_fin'];
        $this->tot_cb_phy += $component['cb_phy'];
        $this->tot_cb_fin += $component['cb_fin'];
    }

    protected function generateTable($array,$action='view') {
        $html = '';

        $this->initVars();

        foreach ($array as $item) {

            //exclude heading without children
            if ($item['row_type']=='heading' && !isset($item['children'])){
                continue;
            }

            if($item['row_type']=='heading') {
                $html .= '<tr class="heading highlight-heading1">
                    <th>' . $item['number'] . '</th>
                    <th colspan="17">' . $item['description'] . '</th>
                    </tr>
                ';
            } else {
                if($action=='view'){ //if view, return in lakh
                    $html .= '<tr class="child" data-parent="'.$item['parent'].'">
                    <td>' . $item['number'] . ' </td>
                    <td>' . $item['description'] . ' </td>
                    <td>' . $item['ob_phy'] . ' </td>
                    <td>' . in_lakh($item['ob_fin']) . ' </td>
                    <td>' . $item['bud_phy'] . ' </td>
                    <td>' . in_lakh($item['bud_fin']) . ' </td>
                    <td class="upto_phy">' . $item['fr_upto_phy'] . ' </td>
                    <td class="upto_fin">' . in_lakh($item['fr_upto_fin']) . ' </td>
                    <td class="mon_phy">' . $item['fr_mon_phy'] . ' </td>
                    <td class="mon_fin">' . in_lakh($item['fr_mon_fin']) . ' </td>
                    <td class="cum_phy">' . $item['fr_cum_phy'] . ' </td>
                    <td class="cum_fin">' . in_lakh($item['fr_cum_fin']) . ' </td>
                    <td class="mon_phy">' . $item['exp_mon_phy'] . ' </td>
                    <td class="mon_fin">' . in_lakh($item['exp_mon_fin']) . ' </td>
                    <td class="cum_phy">' . $item['exp_cum_phy'] . ' </td>
                    <td class="cum_fin">' . in_lakh($item['exp_cum_fin']) . ' </td>
                    <td>' . $item['cb_phy'] . ' </td>
                    <td>' . in_lakh($item['cb_fin']) . ' </td>
                    ';
                    $html .= '</tr>';
                }
                if($action=='download') { //if download, return in rupees
                    $html .= '<tr class="child" data-parent="'.$item['parent'].'">
                    <td>' . $item['number'] . ' </td>
                    <td>' . $item['description'] . ' </td>
                    <td>' . $item['ob_phy'] . ' </td>
                    <td>' . $item['ob_fin'] . ' </td>
                    <td>' . $item['bud_phy'] . ' </td>
                    <td>' . $item['bud_fin'] . ' </td>
                    <td class="upto_phy">' . $item['fr_upto_phy'] . ' </td>
                    <td class="upto_fin">' . $item['fr_upto_fin'] . ' </td>
                    <td class="mon_phy">' . $item['fr_mon_phy'] . ' </td>
                    <td class="mon_fin">' . $item['fr_mon_fin'] . ' </td>
                    <td class="cum_phy">' . $item['fr_cum_phy'] . ' </td>
                    <td class="cum_fin">' . $item['fr_cum_fin'] . ' </td>
                    <td class="mon_phy">' . $item['exp_mon_phy'] . ' </td>
                    <td class="mon_fin">' . $item['exp_mon_fin'] . ' </td>
                    <td class="cum_phy">' . $item['exp_cum_phy'] . ' </td>
                    <td class="cum_fin">' . $item['exp_cum_fin'] . ' </td>
                    <td>' . $item['cb_phy'] . ' </td>
                    <td>' . $item['cb_fin'] . ' </td>
                    ';
                    $html .= '</tr>';
                }

                $component = $item;
                //sub total
                $this->calcTotals($component);

            }

            if (!empty($item['children'])){
                $html .= $this->generateTable($item['children'],$action);
                if($action=='view') { //if view, return in lakh
                    $html .= '<tr class="subtotal highlight-heading4" data-parent="'.$item['component_id'].'">
                        <td colspan="2">Sub Total</td>
                        <td>'.$this->ob_phy.'</td>
                        <td>'.in_lakh($this->ob_fin).'</td>
                        <td>'.$this->bud_phy.'</td>
                        <td>'.in_lakh($this->bud_fin).'</td>
                        <td class="sub_upto_phy">'.$this->fr_upto_phy.'</td>
                        <td class="sub_upto_fin">'.in_lakh($this->fr_upto_fin).'</td>
                        <td class="sub_mon_phy">'.$this->fr_mon_phy.'</td>
                        <td class="sub_mon_fin">'.in_lakh($this->fr_mon_fin).'</td>
                        <td class="sub_cum_phy">'.$this->fr_cum_phy.'</td>
                        <td class="sub_cum_fin">'.in_lakh($this->fr_cum_fin).'</td>
                        <td class="sub_mon_phy">'.$this->exp_mon_phy.'</td>
                        <td class="sub_mon_fin">'.in_lakh($this->exp_mon_fin).'</td>
                        <td class="sub_cum_phy">'.$this->exp_cum_phy.'</td>
                        <td class="sub_cum_fin">'.in_lakh($this->exp_cum_fin).'</td>
                        <td>'.$this->cb_phy.'</td>
                        <td>'.in_lakh($this->cb_fin).'</td>
                        </tr>
                    ';
                }
                if($action=='download') { //if download, return in rupees
                    $html .= '<tr class="subtotal highlight-heading4" data-parent="'.$item['component_id'].'">
                        <td colspan="2">Sub Total</td>
                        <td>'.$this->ob_phy.'</td>
                        <td>'.$this->ob_fin.'</td>
                        <td>'.$this->bud_phy.'</td>
                        <td>'.$this->bud_fin.'</td>
                        <td class="sub_upto_phy">'.$this->fr_upto_phy.'</td>
                        <td class="sub_upto_fin">'.$this->fr_upto_fin.'</td>
                        <td class="sub_mon_phy">'.$this->fr_mon_phy.'</td>
                        <td class="sub_mon_fin">'.$this->fr_mon_fin.'</td>
                        <td class="sub_cum_phy">'.$this->fr_cum_phy.'</td>
                        <td class="sub_cum_fin">'.$this->fr_cum_fin.'</td>
                        <td class="sub_mon_phy">'.$this->exp_mon_phy.'</td>
                        <td class="sub_mon_fin">'.$this->exp_mon_fin.'</td>
                        <td class="sub_cum_phy">'.$this->exp_cum_phy.'</td>
                        <td class="sub_cum_fin">'.$this->exp_cum_fin.'</td>
                        <td>'.$this->cb_phy.'</td>
                        <td>'.$this->cb_fin.'</td>
                        </tr>
                    ';
                }

            }
        }

        return $html;

    }

    protected function getUploadStatus($data=[]) {

        $data['year_id'] = getCurrentYearId();
        if($this->request->getGet('year')){
            $data['year_id'] = $this->request->getGet('year');
        }
        $data['month_id'] = getMonthIdByMonth(date('m'));
        if($this->request->getGet('month')){
            $data['month_id'] = $this->request->getGet('month');
        }
        if($this->user->agency_type_id==$this->settings->district_user){
            $data['fund_agency_id'] = $this->user->fund_agency_id;
        }
        if($this->request->getGet('fund_agency_id')){
            $data['fund_agency_id'] = $this->request->getGet('fund_agency_id');
        }
        $data['agency_types'] = [];
        $data['fund_agencies'] = [];

        $data['blocks'] = [];
        $reportModel = new ReportsModel();
        $filter = [
            'month' => $data['month_id'],
            'year' => $data['year_id'],
            'district_id' => 0
        ];
        if(isset($data['district_id'])){
            $filter['district_id'] = $data['district_id'];
        }
        if(isset($data['fund_agency_id'])){
            $filter['fund_agency_id'] = $data['fund_agency_id'];
        }

        $blocks = $reportModel->getUploadStatus($filter);
        //dd($blocks);
        foreach ($blocks as $block) {

            $fr_sts = $or_sts =3;
            if($block->frc_status==null || $block->frc_status==0){
                $fr_sts = 4;
            }
            if($block->fr_status != null){
                $fr_sts = $block->fr_status;
            }

            if($block->orc_status==null || $block->orc_status==0){
                $or_sts = 4;
            }

            if($block->or_status != null){
                $or_sts = $block->or_status;
            }

            $ex_sts = $block->ex_status!==null?$block->ex_status:3;
            //$or_sts = $block->or_status!==null?$block->or_status:3;
            $cb_sts = $block->cb_status!==null?$block->cb_status:3;
            $mis_sts = $block->mis_status!==null?$block->mis_status:3;
            $data['blocks'][] = [
                'district' => $block->district,
                'block' => $block->block,
                'mis_status' => $this->statuses[$mis_sts],
                'mis_color' => $this->colors[$mis_sts],
                'fr_status' => $this->statuses[$fr_sts],
                'fr_color' => $this->colors[$fr_sts],
                'ex_status' => $this->statuses[$ex_sts],
                'ex_color' => $this->colors[$ex_sts],
                'or_status' => $this->statuses[$or_sts],
                'or_color' => $this->colors[$or_sts],
                'cb_status' => $this->statuses[$cb_sts],
                'cb_color' => $this->colors[$cb_sts]
            ];
        }

        return $data;
    }

}