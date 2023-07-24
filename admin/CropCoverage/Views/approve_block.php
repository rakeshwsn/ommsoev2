<div class="main-container">
    <!--<div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Summary</h3>
            <div class="block-content">
                <table id="area-coverage-summary">
                    <tr>
                        <th>Week</th>
                        <th>Total GPs</th>
                        <th>Total GPs</th>
                    </tr>
                </table>
            </div>
        </div>
    </div>-->
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                <?= $heading_title; ?>
            </h3>
            <?php if(!$approved){ ?>
            <div class="block-options">
                <a data-toggle="tooltip" title="" id="btn-action" class="btn btn-success btn-approve"><i class="fa fa-check"></i> Approve</a>
            </div>
            <?php } ?>
        </div>
        <div class="block-content">
            <table id="block-coverage" class="table table-bordered table-striped table-vcenter table-responsive">
                <thead>
                <tr>
                    <th rowspan="3">Week</th>
                    <th rowspan="3">GP</th>
                    <th rowspan="3">No. of Farmer Covered (for Nursery and Sowing)</th>
                    <th rowspan="3">Nursery Raised (in Ha.)</th>
                    <th rowspan="3">SMI - Balance Nursery Raised (in Ha.)</th>
                    <th rowspan="3">LT - Balance Nursery Raised (in Ha.)
                    </th>
                    <th colspan="10">Achievement under demonstration (in Ha.)</th>
                    <th rowspan="3">Total Ragi</th>
                    <th rowspan="3">Total Non-Ragi </th>
                    <th rowspan="3">Follow up Crops</th>
                    <th rowspan="3">Total Area </th>
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
                            <td><?=$block['week']?></td>
                            <td><?=$block['gp']?></td>
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
    </div>
</div>

<?php if(isset($approval)) {
    echo $approve_form;
} ?>
<?php js_start(); ?>
<script>
    $(function () {
        $('.btn-approve').click(function (e) {
            e.preventDefault();
            url = $(this).attr('href');
            /*$.ajax({
                'url':url,
                ''
            });*/
        });
    });
</script>
<?php js_end(); ?>