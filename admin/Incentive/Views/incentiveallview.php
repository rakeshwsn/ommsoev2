<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Table</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.bootstrap4.min.css">
</head>
<body>

<?php
$user  = service('user');
$districts = [0 => "select District"];
$blocks = [0 => "Select Block"];
$months = [];
$seasons = [];
$datatable = [];
$mergedUrl = '';
$heading_title = '';
?>

<div class="block">
    <form id="formfilter">
        <div class="block-header block-header-default">
            <h3 class="block-title">Data Filter</h3>
        </div>
        <div class="block-content block-content-full">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tr>
                            <th>District</th>
                            <th>Block</th>
                            <th>Year</th>
                            <th>Season</th>
                            <th>Filter</th>
                        </tr>
                        <tr>
                            <?php 
                            $main = $user->district_id ? "disabled" : "";
                            ?>
                            <td>
                                <?= form_dropdown('district_id', $districts, set_value('district_id', $user->district_id), "id='district_id' class='form-control select2' {$main}"); ?>
                            </td>
                            <td>
                                <?= form_dropdown('block_id', $blocks, set_value('block_id', ''), "id='block_id' class='form-control select2'"); ?>
                            </td>
                            <td>
                                <select class="form-control" id="year" name="year">
                                    <option value="">select</option>
                                    <option value="1">2017-18</option>
                                    <option value="2">2018-19</option>
                                    <option value="3">2020-21</option>
                                    <option value="4">2021-22</option>
                                </select>
                            </td>
                            <td>
                                <select class="form-control" id="season" name="season">
                                    <option value="">select</option>
                                    <?php foreach ($seasons as $key => $_season) { ?>
                                        <option value="<?= $key ?>"><?= $_season ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>
                                <button id="btn-filter" class="btn btn-outline btn-primary"><i class="fa fa-filter"></i> Filter</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="block">
    <a href="<?= $mergedUrl ?>"><button id="btn-download" class="btn btn-outline btn-primary"><i class="fa fa-download"></i> Download</button></a>
    <div class="block-header block-header-default">
        <h3 class="block-title"><?= $heading_title ?></h3>
    </div>
    <div class="block-content block-content-full" id="tableId">
        <form action="" method="post" enctype="multipart/form-data" id="form-datatable">
            <table id="datatable_list" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                    <tr>
                        <th>SL No</th>
                        <th>District</th>
                        <th>Block</th>
                        <th>Year</th>
                        <th>Season</th>
                        <th>GP</th>
                        <th>Village</th>
                        <th>Farmer</th>
                        <th>Spouse Name</th>
                        <th>Gender</th>
                        <th>CASTE</th>
                        <th>Mobile</th>
                        <th>AADHAAR</th>
                        <th>Year of Support</th>
                        <th>Area in Hectare</th>
                        <th>Bank Name</th>
                        <th>Account Number</th>
                        <th>IFSC Code</th>
                        <th>Amount</th>
                        <th>Phase</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sl = 1;
                    foreach ($datatable as $district => $phases) {
                        foreach ($phases as $phase => $records) {
                            foreach ($records as $record) {
                    ?>
                                <tr
