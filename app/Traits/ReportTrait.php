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
            $html .= '<tr class="subtotal bg-yellow">
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
                $html .= '<tr class="heading">
                    <th>' . $item['number'] . '</th>
                    <th>' . $item['description'] . '</th>
                    <th colspan="16"></th>
                    </tr>
                ';
            } else {
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

                $component = $item;
                //sub total
                $this->calcTotals($component);

            }

            if (!empty($item['children'])){
                $html .= $this->generateTable($item['children'],$action);
                $html .= '<tr class="subtotal" data-parent="'.$item['component_id'].'">
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
        }

        return $html;

    }

    protected function fillExcel(&$activeSheet,&$row,$components) {
        $this->ob_phy = $this->ob_fin = $this->bud_phy = $this->bud_fin = $this->fr_upto_phy = $this->fr_upto_fin = 0;
        $this->fr_mon_phy = $this->fr_mon_fin = $this->fr_cum_phy = $this->fr_cum_fin = 0;

        $this->cb_phy = $this->cb_fin = $this->exp_upto_phy = $this->exp_upto_fin = 0;
        $this->exp_mon_phy = $this->exp_mon_fin = $this->exp_cum_phy = $this->exp_cum_fin = 0;


        foreach ($components as $component) {
            //exclude heading without children
            if ($component['row_type']=='heading' && !isset($component['children'])){
                continue;
            }
            $row++;
            if ($component['row_type'] == 'heading') {
                $activeSheet->getStyle("A$row:R$row")->applyFromArray(ExcelStyles::fill_blue());
                $activeSheet->mergeCells("B$row:R$row");
                $activeSheet->getStyle("A$row:B$row")->applyFromArray(ExcelStyles::heading2());

            } else {

                $activeSheet->setCellValue("C$row", $component['ob_phy']);
                $activeSheet->setCellValue("D$row", $component['ob_fin']);
                $activeSheet->setCellValue("E$row", $component['bud_phy']);
                $activeSheet->setCellValue("F$row", $component['bud_fin']);
                $activeSheet->setCellValue("G$row", $component['fr_upto_phy']);
                $activeSheet->setCellValue("H$row", $component['fr_upto_fin']);
                $activeSheet->setCellValue("I$row", $component['fr_mon_phy']);
                $activeSheet->setCellValue("J$row", $component['fr_mon_fin']);
                $activeSheet->setCellValue("K$row", $component['fr_cum_phy']);
                $activeSheet->setCellValue("L$row", $component['fr_cum_fin']);
                $activeSheet->setCellValue("M$row", $component['exp_mon_phy']);
                $activeSheet->setCellValue("N$row", $component['exp_mon_fin']);
                $activeSheet->setCellValue("O$row", $component['exp_cum_phy']);
                $activeSheet->setCellValue("P$row", $component['exp_cum_fin']);
                $activeSheet->setCellValue("Q$row", $component['cb_phy']);
                $activeSheet->setCellValue("R$row", $component['cb_fin']);

                //sub total
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

            $activeSheet->setCellValue("A$row", $component['number']);
            $activeSheet->setCellValue("B$row", substr($component['description'],0,150));

            if (!empty($component['children'])) {
                $this->fillExcel($activeSheet, $row, $component['children']);

                $row++;

                $activeSheet->getStyle("A$row:R$row")->applyFromArray(ExcelStyles::fill_grey());
                $activeSheet->getStyle("A$row:B$row")->applyFromArray(ExcelStyles::heading2());
                $activeSheet->setCellValue("B$row", 'Sub Total');

                $activeSheet->setCellValue("C$row", $this->ob_phy);
                $activeSheet->setCellValue("D$row", $this->ob_fin);
                $activeSheet->setCellValue("E$row", $this->bud_phy);
                $activeSheet->setCellValue("F$row", $this->bud_fin);
                $activeSheet->setCellValue("G$row", $this->fr_upto_phy);
                $activeSheet->setCellValue("H$row", $this->fr_upto_fin);
                $activeSheet->setCellValue("I$row", $this->fr_mon_phy);
                $activeSheet->setCellValue("J$row", $this->fr_mon_fin);
                $activeSheet->setCellValue("K$row", $this->fr_cum_phy);
                $activeSheet->setCellValue("L$row", $this->fr_cum_fin);
                $activeSheet->setCellValue("M$row", $this->exp_mon_phy);
                $activeSheet->setCellValue("N$row", $this->exp_mon_fin);
                $activeSheet->setCellValue("O$row", $this->exp_cum_phy);
                $activeSheet->setCellValue("P$row", $this->exp_cum_fin);
                $activeSheet->setCellValue("Q$row", $this->cb_phy);
                $activeSheet->setCellValue("R$row", $this->cb_fin);
            }
        }
    }

    protected function fillComponents($components,&$row,&$activesheet){

        $this->ob_phy = $this->ob_fin = $this->upto_phy = $this->upto_fin = 0;

        //row start
        $row_start = $row;
        foreach ($components as $component) {
            //exclude heading without children
            if ($component['row_type']=='heading' && !isset($component['children'])){
                continue;
            }

            if($component['row_type']=='heading'){
                $activesheet->setCellValue("B$row", $component['number']);
                $activesheet->setCellValue("C$row", $component['description']);

                $activesheet->getStyle("B$row:C$row")
                    ->applyFromArray(ExcelStyles::heading2());
            } else {
                $activesheet->setCellValue("A$row", $component['component_id']);
                $activesheet->setCellValue("B$row", $component['number']);
                $activesheet->setCellValue("C$row", $component['description']);
                $activesheet->setCellValue("D$row", $component['agency_type']);
                $activesheet->setCellValue("E$row", $component['ob_phy']);
                $activesheet->setCellValue("F$row", $component['ob_fin']);
                $activesheet->setCellValue("G$row", $component['exp_upto_phy']);
                $activesheet->setCellValue("H$row", $component['exp_upto_fin']);
                $activesheet->setCellValue("I$row", '');
                $activesheet->setCellValue("J$row", '');
                $activesheet->setCellValue("K$row", "=G$row+I$row");
                $activesheet->setCellValue("L$row", "=H$row+J$row");

                //sub total
                $this->ob_phy += $component['ob_phy'];
                $this->ob_fin += $component['ob_fin'];
                if($this->request->getGet('txn_type')=='expense') {
                    $this->upto_phy += $component['exp_upto_phy'];
                    $this->upto_fin += $component['exp_upto_fin'];
                } else {
                    $this->upto_phy += $component['fr_upto_phy'];
                    $this->upto_fin += $component['fr_upto_fin'];
                }
                //total
                $this->tot_ob_phy += $component['ob_phy'];
                $this->tot_ob_fin += $component['ob_fin'];

                if($this->request->getGet('txn_type')=='expense') {
                    $this->tot_upto_phy += $component['exp_upto_phy'];
                    $this->tot_upto_fin += $component['exp_upto_fin'];
                } else {
                    $this->tot_upto_phy += $component['fr_upto_phy'];
                    $this->tot_upto_fin += $component['fr_upto_fin'];
                }

                $this->i_cells[] = 'I'.$row;
                $this->j_cells[] = 'J'.$row;
                $this->k_cells[] = 'K'.$row;
                $this->l_cells[] = 'L'.$row;
            }

            $row++;
            if (!empty($component['children'])){
                $this->fillComponents($component['children'],$row,$activesheet);

                $activesheet->setCellValue("B$row", 'Sub Total');
                $activesheet->setCellValue("E$row", $this->ob_phy);
                $activesheet->setCellValue("F$row", $this->ob_fin);
                $activesheet->setCellValue("G$row", $this->upto_phy);
                $activesheet->setCellValue("H$row", $this->upto_fin);
                $last_row = $row-1;
                $activesheet->setCellValue("I$row", "=SUM(I$row_start:I$last_row)");
                $activesheet->setCellValue("J$row", "=SUM(J$row_start:J$last_row)");
                $activesheet->setCellValue("K$row", "=G$row+I$row");
                $activesheet->setCellValue("L$row", "=H$row+J$row");

                $activesheet->getStyle("B$row:L$row")
                    ->applyFromArray(ExcelStyles::heading2())
                    ->applyFromArray(ExcelStyles::fill_yellow());

                $row++;
                $row_start = $row;
            }
        }
    }

    protected function getUploadStatus($data=[]) {

        $data['year_id'] = getCurrentYearId();
        if($this->request->getGet('year')){
            $data['year_id'] = $this->request->getGet('year');
        }
        $data['month_id'] = getCurrentMonthId();
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

        foreach ($blocks as $block) {

            $fr_sts = 3;
            if($block->fch_status==null || $block->fch_status==0){
                $fr_sts = 4;
            }
            if($block->fr_status != null){
                $fr_sts = $block->fr_status;
            }

            $ex_sts = $block->ex_status!==null?$block->ex_status:3;
            $or_sts = $block->or_status!==null?$block->or_status:3;
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

    public function createExcelFromHTML($html,$filename){

        $reader = new Html();

        $html = preg_replace("/&(?!\S+;)/", "&amp;", $html);

        $spreadsheet = $reader->loadFromString($html);
        $spreadsheet->setActiveSheetIndex(0);
        $worksheet = $spreadsheet->getActiveSheet();

        $cellStyle = [
            'heading1'=>[
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color'=>['rgb' => 'ffffff'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => '2C3B49'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ],
            'heading2'=>[
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'C0C0C0'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ],
            'heading3'=>[
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'C0C0C0'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]
        ];

        // Load HTML content into a DOM object
        $table = new \DOMDocument();
        $table->loadHTML($html);

        $rows = $table->getElementsByTagName('tr');

        foreach ($worksheet->getRowIterator() as $row) {
            // Find the corresponding row element in the HTML table
            $rowIndex = $row->getRowIndex();

            $rowElement = $rows->item($rowIndex - 1); // -1 because row indices start at 1 in PhpSpreadsheet

            // Get the class name of the row element
            $className = $rowElement->getAttribute('class');

            // Check if the class name matches a highlight class from the HTML table
            if (preg_match('/highlight-(\w+)/', $className, $matches)) {
                $highlightClass = $matches[1];

                // Set the fill color based on the highlight class
                $fillColor = null;
                switch ($highlightClass) {
                    case 'heading1':
                        $fillColor = $cellStyle['heading1'];
                        break;
                    case 'heading2':
                        $fillColor = $cellStyle['heading2'];
                        break;
                    case 'heading3':
                        $fillColor = $cellStyle['heading3'];
                        break;
                }

                if ($fillColor) {
                    $lastColumnIndex = $worksheet->getHighestColumn();
                    $range = 'A' . $rowIndex . ':' . $lastColumnIndex . $rowIndex;
                    $worksheet->getStyle($range)->applyFromArray($fillColor);

                }
            }
        }

        // Set auto-size column widths for all columns
        foreach ($spreadsheet->getActiveSheet()->getColumnIterator() as $column) {
            $spreadsheet->getActiveSheet()->getColumnDimension($column->getColumnIndex())->setAutoSize(false);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $filename .'"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

}