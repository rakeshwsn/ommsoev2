<?php
global $template;

$template = service('template');

if (!function_exists('buildFPOTree')) {
    function buildFPOTree(array $elements, $parentId = "") {
        $branch = array();

        foreach ($elements as $element) {
            if ((string)$element['parent_id']  === (string)$parentId) {
                $children = buildFPOTree($elements, $element['name']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}

if (!function_exists('generateTextHtml')) {

    function generateTextHtml($element)
    {
        if($element['calculation']){
            $readonly="readonly";
            $calculation=$element['calculation'];
        }else{
            $readonly="";
            $calculation="";
        }



        $html='<div class="form-group row">';
        $html.='<label class="col-12" for="all-date">'.$element['label'].'</label>';
        $html.='<div class="col-lg-12">';
        $html.='<div class="input-group">';
        $html.='<input type="text" class="form-control" id="'.$element['name'].'" name="'.$element['id'].'" value="'.$element['value'].'" required="'.$element['required'].'" data-calculation="'.$calculation.'"'. $readonly.'>';
        $html.='</div>';
        $html.='</div>';
        $html.='</div>';
        return $html;
    }
}
if (!function_exists('generateEmailHtml')) {

    function generateEmailHtml($element)
    {
        $html='<div class="form-group row">
                <label class="col-12" for="all-date">'.$element['label'].'</label>
                <div class="col-lg-12">
                    <div class="input-group">
                        <input type="email" class="form-control" id="'.$element['name'].'" name="'.$element['id'].'" value="'.$element['value'].'" required="'.$element['required'].'">
                    </div>
                </div>
            </div>';
        return $html;
    }
}
if (!function_exists('generateDateHtml')) {

    function generateDateHtml($element)
    {
        $html='<div class="form-group row">
                <label class="col-12" for="all-date">'.$element['label'].'</label>
                <div class="col-lg-12">
                    <div class="input-group">
                        <input type="text" class="form-control js-flatpickr" placeholder="d-m-Y" id="'.$element['name'].'" name="'.$element['id'].'" value="'.$element['value'].'" required="'.$element['required'].'" data-date-format="d-m-Y">
                    </div>
                </div>
            </div>';
        return $html;
    }
}
if (!function_exists('generateSelectHtml')) {

    function generateSelectHtml($element)
    {

        $html='<div class="form-group row">
                <label class="col-12" for="all-date">'.$element['label'].'</label>
                <div class="col-lg-12">
                    <div class="input-group">
                        <select class="form-control" name="'.$element['id'].'" id="'.$element['name'].'">';
                            $html .= '<option value="" >Select One</option>';

                            foreach(json_decode($element['values']) as $value) {
                                if($element['value']==$value) {
                                    $html .= '<option value="' . $value . '" selected>' . $value . '</option>';
                                }else{
                                    $html .= '<option value="' . $value . '" >' . $value . '</option>';
                                }
                            }
                        $html.='</select>
                    </div>
                </div>
            </div>';
        return $html;
    }
}
if (!function_exists('generateFileHtml')) {

    function generateFileHtml($element)
    {
        $html='<div class="form-group row">
                <label class="col-12" for="all-date">'.$element['label'].'</label>
                <div class="col-lg-12">
                    <div class="input-group">
                        <div class="custom-file">
                           <input type="file" class="custom-file-input" id="'.$element['name'].'" name="fpodoc['.$element['id'].']" data-toggle="custom-file-input">
                           <label class="custom-file-label" for="'.$element['name'].'">Choose file</label>
                        </div>
                    </div>
                </div>
            </div>';
        return $html;
    }
}
