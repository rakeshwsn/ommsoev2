<?php

function generateHtml($element)
{
    $types = [
        'text' => 'generateTextHtml',
        'email' => 'generateEmailHtml',
        'date' => 'generateDateHtml',
        'select' => 'generateSelectHtml',
        'file' => 'generateFileHtml'
    ];

    if (!isset($types[$element['type']])) {
        throw new InvalidArgumentException("Invalid element type: {$element['type']}");
    }

    $function = $types[$element['type']];
    if (!function_exists($function)) {
        throw new RuntimeException("Function not found: {$function}");
    }

    return $function($element);
}

function generateTextHtml($element)
{
    $readonly = $element['calculation'] ? 'readonly' : '';

    return '
        <div class="form-group row">
            <label class="col-12" for="' . $element['name'] . '">' . $element['label'] . '</label>
            <div class="col-lg-12">
                <div class="input-group">
                    <input type="text" class="form-control" id="' . $element['name'] . '" name="' . $element['id'] . '" value="' . $element['value'] . '" required="' . $element['required'] . '" data-calculation="' . $element['calculation'] . '" ' . $readonly . '>
                </div>
            </div>
        </div>
    ';
}

function generateEmailHtml($element)
{
    return '
        <div class="form-group row">
            <label class="col-12" for="' . $element['name'] . '">' . $element['label'] . '</label>
            <div class="col-lg-12">
                <div class="input-group">
                    <input type="email" class="form-control" id="' . $element['name'] . '" name="' . $element['id'] . '" value="' . $element['value'] . '" required="' . $element['required'] . '">
                </div>
            </div>
        </div>
    ';
}

function generateDateHtml($element)
{
    return '
        <div class="form-group row">
            <label class="col-12" for="' . $element['name'] . '">' . $element['label'] . '</label>
            <div class="col-lg-12">
                <div class="input-group">
                    <input type="text" class="form-control js-flatpickr" placeholder="d-m-Y" id="' . $element['name'] . '" name="' . $element['id'] . '" value="' . $element['value'] . '" required="' . $element['required'] . '" data-date-format="d-m-Y">
                </div>
            </div>
        </div>
    ';
}

function generateSelectHtml($element)
{
    $html = '
        <div class="form-group row">
            <label class="col-12" for="' . $element['name'] . '">' . $element['label'] . '</label>
            <div class="col-lg-12">
                <div class="input-group">
                    <select class="form-control" name="' . $element['id'] . '" id="' . $element['name'] . '">
                        <option value="">Select One</option>
    ';

    foreach (json_decode($element['values']) as $value) {
        $selected = $element['value'] === $value ? 'selected' : '';
        $html .= "
            <option value=\"{$value}\" {$selected}>{$value}</option>
        ";
    }

    $html .= '
                    </select>
                </div>
            </div>
        </div>
    ';

    return $html;
}

function generateFileHtml($element)
{
    return '
        <div class="form-group row">
            <label class="col-12" for="' . $element['name'] . '">' . $element['label'] . '</label>
            <div class="col-lg-12">
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="' . $element['name'] . '" name="fpodoc[' . $element['id'] . ']" data-toggle="custom-file-input">
                        <label class="custom-file-label" for="' . $element['name'] . '">Choose file</label>
                    </div>
                </div>
            </div>
        </div>
    ';
}
