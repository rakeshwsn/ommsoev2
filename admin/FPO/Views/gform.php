<?php
error_reporting(E_ALL);

$html = "";
foreach ($formData as $form) {
    if ($form['inputType'] === null) {
        continue;
    }

    switch ($form['inputType']) {
        case "text":
            $html .= generateTextHtml($form);
            break;
        case "email":
            $html .= generateEmailHtml($form);
            break;
        case "date":
            $html .= generateDateHtml($form);
            break;
        case "select":
            $html .= generateSelectHtml($form);
            break;
        case "file":
            $html .= generateFileHtml($form);
            break;
    }
}

echo $html;
?>

<script>
jQuery(function ($) {
    Codebase.helpers(['flatpickr']);

    $('[data-toggle="custom-file-input"]:not(.js-custom-file-input-enabled)').each(function (index, element) {
        const $t = $(element);
        $t.addClass("js-custom-file-input-enabled").on("change", function (event) {
            const files = event.target.files;
            const fileName = files.length > 1 ? files.length + " " + ($t.data("langFiles") || "Files") : files[0].name;
            $t.next(".custom-file-label").css("overflow-x", "hidden").html(fileName);
        });
    });

    function setCalculationTarget(element) {
        const $this = $(element);
        const calculation = $this.data('calculation');
        const targetId = $this.attr('id');

        if (calculation && targetId) {
            calculation.split('+').forEach(function (item) {
                const $item = $("#" + item);

                if ($item.length) {
                    $item.attr('data-calculation-target', targetId);
                }
            });
        }
    }

    $("[data-calculation]").each(function () {

