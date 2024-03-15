<div class="main-container">
    <div class="block">
        <form method="post" id="">
            <div class="block-header block-header-default">
                <h3 class="block-title">Edit</h3>
            </div>

            <div class="block-content block-content-full">
                <div id="page_list_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <!-- DataTables functionality is initialized with .js-dataTable-full class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label">Year</label>
                            <input type="text" name="year_id" class="form-control" value="<?= $entranses['year_name'] ?>" readonly>
                            <span id="em1" class="text-danger"></span>

                        </div>
                        <div class="col-4">
                            <label class="form-label">District</label>
                            <input type="text" name="district_id" class="form-control" value="<?= $entranses['district_name'] ?>" readonly>
                            <span id="em2" class="text-danger"></span>

                        </div>
                        <div class="col-4">
                            <label class="form-label">Months</label>
                            <input type="text" name="month_id" class="form-control" value="<?= $entranses['month_name'] ?>" readonly>
                            <span id="em3" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label">Block</label>
                            <input type="text" name="block_id" class="form-control" value="<?= $entranses['block_name'] ?>" readonly>
                            <span id="em6" class="text-danger"></span>
                        </div>
                        <div class="col-4">
                            <label class="form-label">Gp</label>
                            <input type="text" name="gp_id" class="form-control" value="<?= $entranses['gp_name'] ?>" readonly>
                            <span id="em9" class="text-danger"></span>
                        </div>

                        <div class="col-4">
                            <label class="form-label">Village</label>
                            <input type="text" name="village_id" class="form-control" value="<?= $entranses['village_name'] ?>" readonly>
                            <span id="em5" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-4">
                            <label class="form-label">Unit Type</label>
                            <input type="text" name="unit_id" class="form-control" value="<?= $entranses['unit_name'] ?>" readonly>
                            <span id="em3" class="text-danger"></span>

                        </div>
                        <div class="col-4">
                            <label class="form-label">Fortnight</label>
                            <input type="text" name="period" class="form-control" value="<?= $entranses['period'] ?>" readonly>

                            <span id="em" class="text-danger"></span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title"> Edit Transaction Data</h3>
        </div>
        <div class="block-content block-content-full">
            <div class="block">
                <form action="" method="post" id="establishmentEditForm">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="page_list" class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer" aria-describedby="page_list_info">
                                <thead>
                                    <?php foreach ($unit_groups as $key => $columns) {
                                        if ($key == $entranses['unit_group_name']) {  ?>
                                            <tr>
                                                <?php foreach ($columns as $column) { ?>
                                                    <th><?= $column['label'] ?></th>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                </thead>
                                <tbody>
                                    <tr class="odd">
                                        <?php foreach ($unit_groups as $key => $columns) {
                                            if ($key == $entranses['unit_group_name']) {  ?>
                                                <?php foreach ($columns as $key => $column) { ?>
                                                    <td>
                                                        <input type="text" id="<?= $key ?>" name="<?= $key ?>" class="form-control numbers" value="<?= $entranses[$key] ?>" required>
                                                        <p class="errorTxt text-danger m-0">&nbsp;</p>
                                                    </td>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
    #loading-overlay {
        background: rgb(255 255 255 / 80%);
        display: flex;
        align-items: center;

        justify-content: center;
        text-align: center;
        z-index: 9999;
    }
</style>
<?php js_start(); ?>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

<script>
    $(document).ready(function() {
        jQuery.validator.addMethod("number", function(value, element) {
            return this.optional(element) || /^(\d{1,10})$/.test(value);
        }, "Please enter only numbers");
        jQuery.validator.addMethod("decimal", function(value, element) {
            return this.optional(element) || /^(\d{1,10})(\.\d{0,2})?$/.test(value);
        }, "Please enter only numbers");
        $("#establishmentEditForm").validate({
            ignore: [],
            rules: {
                <?php foreach ($unit_groups as $key => $columns) {
                    if ($key == $entranses['unit_group_name']) { ?>
                        <?php foreach ($columns as $key => $column) { ?>

                            <?= $key ?>: {
                                required: true,
                                <?= $column['rules'] ?>: true
                            },
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            },
            messages: {
                <?php foreach ($unit_groups as $key => $columns) {
                    if ($key == $entranses['unit_group_name']) { ?>
                        <?php foreach ($columns as $key => $column) { ?>

                            <?= $key ?>: {
                                <?= $column['rules'] ?>: "Please enter only numbers"
                            },
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            },
            errorPlacement: function(error, element) {
                var placement = $(element).siblings('.errorTxt');
                if (placement) {
                    $(placement).html(error)
                } else {
                    error.insertAfter(element);
                }
            }
        });

       
    });
</script>




<?php js_end(); ?>