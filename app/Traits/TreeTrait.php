<?php

namespace App\Traits;


use Admin\Component\Models\ComponentModel;

trait TreeTrait {

    public function getTree($filter=[]) {
        $compModel = new ComponentModel();

        $comps = $compModel->getAll($filter);

        $tree = $this->buildTree($comps);

        return $this->nestedHTMLTree($tree);

    }

    public function buildTree(array $flatList, $parent_col = 'parent',$id_col='id')
    {
        if(!$flatList){
            return [];
        }
        $grouped = [];
        foreach ($flatList as $node){
            $grouped[$node[$parent_col]][] = $node;
        }

        $fnBuilder = function($siblings) use (&$fnBuilder, $grouped,$id_col) {
            foreach ($siblings as $k => $sibling) {
                $id = $sibling[$id_col];
                if(isset($grouped[$id])) {
                    $sibling['children'] = $fnBuilder($grouped[$id]);
                }
                $siblings[$k] = $sibling;
            }
            return $siblings;
        };

        return $fnBuilder($grouped[0]);
    }

    public function nestedHTMLTree($list, $depth = 1) {
        $nav = '<ul class="dd-list">';

        foreach($list as $Item){
            $nav .=	'<li class="dd-item" data-id="'.$Item['id'].'">';

            $nav .=		'<div class="dd-handle">';

            if($Item['row_type']=='heading'){
                $nav .=			'<strong class="h5">['.($Item['number'].'] '.$Item['description']?:"&nbsp;").'</strong>' ;
            } else {
                $nav .=			'['.($Item['number'].'] '.$Item['description']?:"&nbsp;");
            }
            $nav .=			'&nbsp;&nbsp;&nbsp; <a href="#" class="btn-remove text-danger">Remove</a>';
            $nav .=		'</div>';

            if ( ! empty($Item['children'])){
                $nav .= $this->nestedHTMLTree($Item['children'], $depth + 1);
            }
            $nav .= '</li>';
        }
        $nav .= '</ul>';
        return $nav;
    }

    public function getTreeArray()
    {
        $compModel = new ComponentModel();

        $comps = $compModel->getAll();

        return $this->buildTree($comps);
    }

    protected function getTable($array,$txn_type,$action) {
        $this->tot_ob_phy = $this->tot_ob_fin = $this->tot_upto_phy = $this->tot_upto_fin = 0;
        $this->tot_mon_phy = $this->tot_mon_fin = $this->tot_cum_phy = $this->tot_cum_fin = 0;

        $html = $this->generateTable($array,$txn_type,$action);

        //grand total
        $html .= '<tr class="subtotal bg-yellow">
                    <td colspan="2">Grand Total</td>
                    <td>'.$this->tot_ob_phy.'</td>
                    <td>'.$this->tot_ob_fin.'</td>
                    <td>'.$this->tot_upto_phy.'</td>
                    <td>'.$this->tot_upto_fin.'</td>
                    <td id="gt_mon_phy">'.$this->tot_mon_phy.'</td>
                    <td id="gt_mon_fin">'.$this->tot_mon_fin.'</td>
                    <td id="gt_cum_phy">'.$this->tot_cum_phy.'</td>
                    <td id="gt_cum_fin">'.$this->tot_cum_fin.'</td>
                    </tr>
                ';

        return $html;

    }

    protected function generateTable($array,$txn_type,$action='view') {
        $html = '';
        $this->ob_phy = $this->ob_fin = $this->upto_phy = $this->upto_fin = 0;
        $this->mon_phy = $this->mon_fin = $this->cum_phy = $this->cum_fin = 0;

        foreach ($array as $item) {
            //exclude heading without children
            if ($item['row_type']=='heading' && !isset($item['children'])){
                continue;
            }

            if($item['row_type']=='heading') {
                $html .= '<tr class="heading">
                    <th>' . $item['number'] . '</th>
                    <th>' . $item['description'] . '</th>
                    <th colspan="8"></th>
                    </tr>
                ';
            } else {
                $html .= '<tr data-parent="'.$item['parent'].'">
                    <td>' . $item['number'] . ' </td>
                    <td>' . $item['description'] . ' </td>
                    <td>' . $item['ob_phy'] . ' </td>
                    <td>' . $item['ob_fin'] . ' </td>';
                if($txn_type=='expense') {
                    $html .= '<td class="upto_phy">' . $item['exp_upto_phy'] . ' </td>
                              <td class="upto_fin">' . $item['exp_upto_fin'] . ' </td>';
                    if($action=='edit'){
                        $html .= '<td class="mon_phy"><input class="w-50p" name="'.$item['component_id'].'[phy]" type="text" value="' . $item['exp_mon_phy'] . '"> </td>
                                  <td class="mon_fin"><input type="text" name="'.$item['component_id'].'[fin]" value="' . $item['exp_mon_fin'] . '"> </td>';
                    } else {
                        $html .= '<td class="mon_phy">' . $item['exp_mon_phy'] . ' </td>
                                  <td class="mon_fin">' . $item['exp_mon_fin'] . ' </td>';
                    }
                    $html .= '<td class="cum_phy">' . $item['exp_cum_phy'] . ' </td>
                              <td class="cum_fin">' . $item['exp_cum_fin'] . ' </td>';
                } else {
                    $html .= '<td class="upto_phy">' . $item['fr_upto_phy'] . ' </td>
                              <td class="upto_fin">' . $item['fr_upto_fin'] . ' </td>';
                    if($action=='edit'){
                        $html .= '<td class="mon_phy"><input class="w-50p" name="'.$item['component_id'].'[phy]" type="text" value="' . $item['fr_mon_phy'] . '"> </td>
                                  <td class="mon_fin"><input type="text" name="'.$item['component_id'].'[fin]" value="' . $item['fr_mon_fin'] . '"> </td>';
                    } else {
                        $html .= '<td class="mon_phy">' . $item['fr_mon_phy'] . ' </td>
                                  <td class="mon_fin">' . $item['fr_mon_fin'] . ' </td>';
                    }
                    $html .= '<td class="cum_phy">' . $item['fr_cum_phy'] . ' </td>
                              <td class="cum_fin">' . $item['fr_cum_fin'] . ' </td>';
                }
                $html .= '</tr>';

                $component = $item;
                //sub total
                $this->ob_phy += $component['ob_phy'];
                $this->ob_fin += $component['ob_fin'];
                if($txn_type=='expense') {
                    $this->upto_phy += (int)$component['exp_upto_phy'];
                    $this->upto_fin += (float)$component['exp_upto_fin'];
                    $this->mon_phy += (int)$component['exp_mon_phy'];
                    $this->mon_fin += (float)$component['exp_mon_fin'];
                    $this->cum_phy += (int)$component['exp_cum_phy'];
                    $this->cum_fin += (float)$component['exp_cum_fin'];
                } else {
                    $this->upto_phy += (int)$component['fr_upto_phy'];
                    $this->upto_fin += (float)$component['fr_upto_fin'];
                    $this->mon_phy += (int)$component['fr_mon_phy'];
                    $this->mon_fin += (float)$component['fr_mon_fin'];
                    $this->cum_phy += (int)$component['fr_cum_phy'];
                    $this->cum_fin += (float)$component['fr_cum_fin'];
                }

                //total
                $this->tot_ob_phy += $component['ob_phy'];
                $this->tot_ob_fin += $component['ob_fin'];
                if($txn_type=='expense') {
                    $this->tot_upto_phy += (int)$component['exp_upto_phy'];
                    $this->tot_upto_fin += (float)$component['exp_upto_fin'];
                    $this->tot_mon_phy += (int)$component['exp_mon_phy'];
                    $this->tot_mon_fin += (float)$component['exp_mon_fin'];
                    $this->tot_cum_phy += (int)$component['exp_cum_phy'];
                    $this->tot_cum_fin += (float)$component['exp_cum_fin'];
                } else {
                    $this->tot_upto_phy += (int)$component['fr_upto_phy'];
                    $this->tot_upto_fin += (float)$component['fr_upto_fin'];
                    $this->tot_mon_phy += (int)$component['fr_mon_phy'];
                    $this->tot_mon_fin += (float)$component['fr_mon_fin'];
                    $this->tot_cum_phy += (int)$component['fr_cum_phy'];
                    $this->tot_cum_fin += (float)$component['fr_cum_fin'];
                }
            }
            if (!empty($item['children'])){
                $html .= $this->generateTable($item['children'],$txn_type,$action);
                $html .= '<tr class="subtotal" data-parent="'.$item['component_id'].'">
                    <td colspan="2">Sub Total</td>
                    <td>'.$this->ob_phy.'</td>
                    <td>'.$this->ob_fin.'</td>
                    <td class="sub_upto_phy">'.$this->upto_phy.'</td>
                    <td class="sub_upto_fin">'.$this->upto_fin.'</td>
                    <td class="sub_mon_phy">'.$this->mon_phy.'</td>
                    <td class="sub_mon_fin">'.$this->mon_fin.'</td>
                    <td class="sub_cum_phy">'.$this->cum_phy.'</td>
                    <td class="sub_cum_fin">'.$this->cum_fin.'</td>
                    </tr>
                ';
            }
        }

        return $html;

    }
	
	protected function getCTable($array,$action) {
        $this->grand_phy = $this->grand_fin  = 0;
        $html = $this->generateCTable($array,0);

        //grand total
        $html .= '<tr class="subtotal bg-yellow">
                    <td>&nbsp;</td>
                    <td colspan="4">Grand Total</td>
                    <td>'.$this->grand_phy.'</td>
                    <td>'.in_lakh($this->grand_fin).'</td>
                    </tr>
                ';

        return $html;

    }

    protected function generateCTable($array,$parent,$depth = 1) {
        $html = '';
        $this->total_phy = $this->total_fin = 0;
        $sub="sub".$parent;
        //echo $sub."<br>";
        //$$sub=0;
        $$sub=&$$sub;
        foreach ($array as $item) {
            //$$sub=&$$sub;
            //exclude heading without children
            if ($item['row_type']=='heading' && !isset($item['children'])){
                continue;
            }

            if($item['row_type']=='heading') {
                $html .= '<tr class="heading" data-id="'.$item['id'].'">
                    <th>' . $item['number'] . '</th>
                    <th>' . $item['description'] . '</th>
                    <th colspan="6"></th>
                    </tr>
                ';
            } else {
                $html .= '<tr data-id="'.$item['id'].'">
                    <td>' . $item['number'] . ' </td>
                    <td>' . $item['description'] . ' </td>
                    <td>' . $item['agency_type_id'] . ' </td>
                    <td>' . $item['units'] . ' </td>
                    <td>' . $item['unit_cost'] . ' </td>
                    <td>' . $item['physical'] . ' </td>
                    <td>' . in_lakh($item['financial']) . ' </td>
                    ';
                $html .= '</tr>';

                $component = $item;
                //sub total
                $this->total_phy += (int)$component['physical'];
                $this->total_fin += (float)$component['financial'];
                ;

                //total
                $this->grand_phy += (int)$component['physical'];
                $this->grand_fin += (float)$component['financial'];

            }


            if (!empty($item['children'])) {
                $html .= $this->generateCTable($item['children'], $item['parent'], $depth+1);

                $html .= '<tr class="subtotal" data-parent="' . $item['parent'] . '" data-ref="'.$item['id'].'">
                <td>&nbsp</td>
                <td colspan="4">Sub Total</td>
                <td >' . $this->total_phy . '</td>
                <td>' . in_lakh($this->total_fin) . '</td>
                </tr>
            ';





            }
            //echo $$sub."<br>";

        }

        return $html;

    }

}