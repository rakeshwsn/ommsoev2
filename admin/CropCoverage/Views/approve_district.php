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
                    <?= $heading_title; ?>[<?=$week_text?>]
                </h3>
                <?php if($show_reject): ?>
                <div class="block-options">
                    <button type="button" data-toggle="tooltip" title="Reject" class="btn btn-danger" id="btn-reject"><i class="fa fa-recycle"></i></button>
                </div>
                <?php endif; ?>
            </div>
            <div class="block-content">
                <table id="block-coverage" class="table table-bordered table-striped table-vcenter table-responsive">
                    <thead>
                    <tr>
                        <th rowspan="3">Block</th>
                        <th rowspan="3">No of GP</th>
                        <th rowspan="3">No. of Farmer Covered (for Nursery and Sowing)</th>
                        <th rowspan="3">Nursery Raised (in Ha.)</th>
                        <th rowspan="3">SMI - Balance Nursery Raised (in Ha.)</th>
                        <th rowspan="3">LT - Balance Nursery Raised (in Ha.)
                        </th>
                        <th colspan="10">Achievement under demonstration (in Ha.) <?=$week_text?></th>
                        <th rowspan="3">Total Ragi</th>
                        <th rowspan="3">Total Non-Ragi </th>
                        <th rowspan="3">Follow up Crops</th>
                        <th rowspan="3">Total Area </th>
                        <th rowspan="3">Status </th>
                        <th rowspan="3" class="text-right no-sort">Actions</th>
                    </tr>
                    <tr>
                        <?php foreach ($crop_practices as $crop_id => $practices): ?>
                            <th colspan="<?= count($practices) ?>"><?= $crops[$crop_id] ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php foreach ($crop_practices as $crop_id => $practices): ?>
                            <?php foreach ($practices as $practice): ?>
                                <th>
                                    <?= $practice ?>
                                </th>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($blocks) {?>
                        <?php foreach ($blocks as $block) {?>
                            <tr>
                                <td><?=$block['block']?></td>
                                <td><?=$block['gps']?></td>
                                <td><?=$block['farmers_covered']?></td>
                                <td><?=$block['nursery_raised']?></td>
                                <td><?=$block['balance_smi']?></td>
                                <td><?=$block['balance_lt']?></td>
                                <td><?=$block['ragi_smi']?></td>
                                <td><?=$block['ragi_lt']?></td>
                                <td><?=$block['ragi_ls']?></td>
                                <td><?=$block['little_millet_lt']?></td>
                                <td><?=$block['little_millet_ls']?></td>
                                <td><?=$block['foxtail_ls']?></td>
                                <td><?=$block['sorghum_ls']?></td>
                                <td><?=$block['kodo_ls']?></td>
                                <td><?=$block['barnyard_ls']?></td>
                                <td><?=$block['pearl_ls']?></td>
                                <td><?=$block['total_ragi']?></td>
                                <td><?=$block['total_non_ragi']?></td>
                                <td><?=$block['total_fc']?></td>
                                <td><?=$block['total_area']?></td>
                                <td><?=$block['status']?></td>
                                <td>
                                    <div class="btn-group btn-group-sm pull-right">
                                    <?=$block['action']?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="4">Data not available.</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<?php if($show_reject){ ?>
<?php js_start(); ?>
<script>
    $(function () {
        $('#btn-reject').click(function () {
            if(confirm('Are you sure you want to reject this district data?')==false){
                return false;
            }
            district_id = <?=$district_id?>;
            start_date = '<?=$week_start_date?>';
            $.ajax({
                url:'<?=$reject_url?>',
                data:{district_id:district_id,start_date:start_date},
                type:'POST',
                dataType:'JSON',
                success:function (res) {
                    if(res.status){
                        location.href = res.redirect;
                    }
                },
                error:function () {
                    alert('Something went wrong');
                }
            });
        });
    });
</script>
<?php js_end(); ?>
<?php } ?>