<!-- Main content -->
<div class="block" id="upload-controls">
    <div class="block-header block-header-default">
        <h3 class="block-title">Filter Submissions</h3>
    </div>
    <div class="block-content block-content-full">
        <form>
            <div class="row">
                <div class="col-md-2">
                    <select class="form-control" id="year" name="year" required>
                        <option value="">Choose Year</option>
                        <?php foreach ($years as $year) { ?>
                            <option value="<?=$year['id']?>" <?php if($year['id']==$year_id){echo 'selected';} ?>><?=$year['name']?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" id="month" name="month" required>
                        <option value="">Choose Month</option>
                        <?php foreach ($months as $month) { ?>
                            <option value="<?=$month['id']?>" <?php if($month['id']==$month_id){echo 'selected';} ?>><?=$month['name']?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" id="txn_type" name="txn_type">
                        <option value="">Choose Module</option>
                        <?php foreach ($modules as $module): ?>
                            <option value="<?=$module['modulecode']?>" <?php if($txn_type==$module['modulecode']){echo 'selected';} ?>><?=$module['module']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if($districts): ?>
                    <div class="col-md-2">
                        <select class="form-control" id="district_id" name="district_id">
                            <option value="">Choose District (if district level)</option>
                            <?php foreach ($districts as $district): ?>
                                <option value="<?=$district['id']?>" <?php if($district['id']==$district_id){echo 'selected';} ?>><?=$district['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <?php if($blocks): ?>
                    <div class="col-md-2">
                        <select class="form-control" id="block_id" name="block_id">
                            <option value="">Choose Block (if block level)</option>
                            <?php foreach ($blocks as $block): ?>
                                <option value="<?=$block['id']?>" <?php if($block['id']==$block_id){echo 'selected';} ?>><?=$block['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>
            <div class="row mt-3">
                <div class="col-md-2">
                    <button id="btn-add" class="btn btn-outline btn-primary"><i class="fa fa-table"></i> Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!--  Table block  -->
<div class="block">
    <div class="block-content block-content-full">
        <table class="table table-bordered table-striped table-vcenter js-dataTable-full" id="datatable">
            <thead>
            <tr>
                <th>Year</th>
                <th>Month</th>
                <th>Date Added</th>
                <th>Txn Type</th>
                <th>Block</th>
                <th>Agency Type</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($upload_statuses as $status): ?>
                <tr>
                    <td><?=$status->year?></td>
                    <td><?=$status->month?></td>
                    <td><?=$status->created_at?></td>
                    <td><?=$status->module?></td>
                    <td><?=$status->block?></td>
                    <td><?=$status->agency_type?></td>
                    <td><?=$status->status?></td>
                    <td><?php if($status->action) { ?> <a href="<?=$status->action?>" class="btn btn-primary"><i class="fa fa-send"></i></a> <?php } ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- content -->

<?php js_start(); ?>
<script>

    var loading;

    $(function () {

        $('.js-dataTable-full').dataTable({
            columnDefs: [ { orderable: false, targets: [ 4 ] } ],
            autoWidth: false
        });
    });

</script>
<?php js_end(); ?>