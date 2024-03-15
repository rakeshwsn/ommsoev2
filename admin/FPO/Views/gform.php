<?php
error_reporting(E_ALL);

$html = "";
foreach ($formdata as $form) {
    if (empty($form['input_type'])) {
        continue;
    }

    switch (trim($form['input_type'])) {
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
        default:
            $html .= "";
    }
}

echo $html;
?>

<script>
jQuery(function ($) {
    Codebase.helpers(['flatpickr']);

    $('[data-toggle="custom-file-input"]:not(.js-custom-file-input-enabled)').each(function (e, a) {
        var $t = $(a);
        $t.addClass("js-custom-file-input-enabled").on("change", function (e) {
            var a = e.target.files.length > 1 ? e.target.files.length + " " + ($t.data("lang-files") || "Files") : e.target.files[0].name;
            $t.next(".custom-file-label").css("overflow-x", "hidden").html(a);
        });
    });

    $("[data-calculation]").each(function () {
        var $this = $(this);
        var calculation = $this.data('calculation');
        var targetId = $this.attr('id');

        calculation.split('+').forEach(function (item) {
            $("#" + item).attr('data-calculation-target', targetId);
        });
    });

    $(document).on('keyup', "[data-calculation-target]", function () {
        var targetId = $(this).data('calculation-target');
        var sum = 0;

        $("[data-calculation-target]").each(function
