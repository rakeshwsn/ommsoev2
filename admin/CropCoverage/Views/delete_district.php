<div class="main-container">
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Filter</h3>
        </div>
        <div class="block-content block-content-full">
            <form id="form-filter" class="form-horizontal">
                <div class="form-layout">
                    <div class="row mg-b-25">
                        <div class="col-lg-3">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">District: <span class="tx-danger">*</span></label>
                                <?= form_dropdown('district_id', $districts, $district_id,"id='filter_block' class='form-control js-select2'"); ?>
                            </div>
                        </div><!-- col-4 -->
                        <div class="col-lg-3">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">Week: <span class="tx-danger">*</span></label>
                                <?= form_dropdown('start_date', $weeks, $week_start_date,"id='filter_block' class='form-control js-select2'"); ?>
                            </div>
                        </div><!-- col-4 -->
                        <div class="col-lg-3 center">
                            <label class="form-control-label">&nbsp;</label>
                            <div class="form-layout-footer">
                                <button id="btn-filter" class="btn btn-primary">Filter</button>
                            </div><!-- form-layout-footer -->
                        </div>
                    </div><!-- row -->
                </div>
            </form>
        </div>
    </div>
    <div class="block">
        <form action="" method="post">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    <?= $heading_title; ?> [<?=$week_text?>]
                </h3>
                <div class="block-options">
                    <button data-toggle="tooltip" title="" id="btn-delete" class="btn btn-danger"><i
                                class="fa fa-trash"></i> Delete</button>
                </div>
            </div>
            <div class="block-content">
                <table id="block-coverage" class="table custom-table table-bordered table-striped table-vcenter table-responsive">
                    <thead>
                    <tr class="highlight-heading1">
                        <th>#</th>
                        <th>Block</th>
                        <th>No of GP</th>
                        <th>No. of Farmer Covered (for Nursery and Sowing)</th>
                        <th>Nursery Raised (in Ha.)</th>
                        <th>SMI - Balance Nursery Raised (in Ha.)</th>
                        <th>LT - Balance Nursery Raised (in Ha.)</th>
                        <th>Total Ragi</th>
                        <th>Total Non-Ragi </th>
                        <th>Follow up Crops</th>
                        <th>Total Area </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($blocks) {?>
                        <?php foreach ($blocks as $block) {?>
                            <tr>
                                <td><input type="checkbox" value="<?=$block['block_id']?>" name="blocks[]" ></td>
                                <td><?=$block['block']?></td>
                                <td><?=$block['gps']?></td>
                                <td><?=$block['farmers_covered']?></td>
                                <td><?=$block['nursery_raised']?></td>
                                <td><?=$block['balance_smi']?></td>
                                <td><?=$block['balance_lt']?></td>
                                <td><?=$block['total_ragi']?></td>
                                <td><?=$block['total_non_ragi']?></td>
                                <td><?=$block['total_fc']?></td>
                                <td><?=$block['total_area']?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="12">Data not available.</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
    $(function () {
        $('#btn-delete').click(function (e) {
//            e.preventDefault();

            var anyChecked = false;

            $('[name^="blocks"]').each(function() {
                if ($(this).is(':checked')) {
                    anyChecked = true;
                    return false; // Break the loop if a checked checkbox is found
                }
            });

            if (anyChecked) {
                return confirm("Are you sure to delete these block entries?");
            }
            return false;
        });
    });
</script>