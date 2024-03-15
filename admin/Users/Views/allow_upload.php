<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Allow Upload</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker1/1.9.0/css/bootstrap-datepicker.min.css">
</head>
<body>

<div class="col-12">
    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">Allow Upload</h3>
        </div>
        <div class="block-content block-content-full">
            <form>
                <div class="row">
                    <div class="col-md-2">
                        <select class="form-control" id="year" name="year">
                            <option value="">Choose Year</option>
                            <?php foreach (getAllYears() as $_year) { ?>
                                <option value="<?=htmlspecialchars($_year['id'])?>" <?php if ($_year['id']==$year_id){ echo 'selected'; } ?>><?=htmlspecialchars($_year['name'])?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" id="month" name="month">
                            <option value="">Choose Month</option>
                            <?php foreach (getAllMonths() as $_month) { ?>
                                <option value="<?=htmlspecialchars($_month['id'])?>" <?php if ($_month['id']==$month_id){ echo 'selected'; } ?>><?=htmlspecialchars($_month['name'])?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" id="agency_type_id" name="agency_type_id">
                            <option value="">Choose Agency</option>
                            <?php foreach ($agency_types as $_agency) { ?>
                                <option value="<?=htmlspecialchars($_agency['id'])?>" <?php if ($_agency['id']==$agency_type_id){ echo 'selected'; } ?>><?=htmlspecialchars($_agency['name'])?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php if(isset($districts)): ?>
                        <div class="col-md-2">
                            <select class="form-control" id="district_id" name="district_id">
                                <option value="">Choose District</option>
                                <?php foreach ($districts as $_district) { ?>
                                    <option value="<?=htmlspecialchars($_district['id'])?>" <?php if ($_district['id']==$district_id){ echo 'selected'; } ?>><?=htmlspecialchars($_district['name'])?></option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-2">
                        <button class="btn btn-primary"><i class="fa fa-search"></i> Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="block">
        <div class="block-content block-content-full">
            <table class="table table-striped table-vcenter">
                <thead>
                <tr>
                    <th class="text-center">District</th>
                    <th class="text-center">Block</th>
                    <th>Agency</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Extended Date</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?=htmlspecialchars($user['district'])?></td>
                        <td><?=htmlspecialchars($user['block'])?></td>
                        <td><?=htmlspecialchars($user['firstname'])?></td>
                        <td><?=htmlspecialchars($user['from_date'])?></td>
                        <td><?=htmlspecialchars($user['to_date'])?></td>
                        <td><input type="text"
                                   data-upload-id="<?=htmlspecialchars($user['upload_id'])?>"
                                   data-user-id="<?=htmlspecialchars($user['user_id'])?>"
                                   data-date-format="dd/mm/yyyy"
                                   class="js-datepicker form-control"
                                   value="<?=htmlspecialchars($user['extended_date'])?>"></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdnjs.
