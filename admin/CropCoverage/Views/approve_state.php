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
                                <label class="form-control-label">Week: <span class="tx-danger">*</span></label>

                                <?= form_dropdown('start_date', $weeks, $week_start_date, "id='filter_block' class='form-control js-select2'"); ?>
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
                    <?= $heading_title; ?>[
                    <?= $week_text ?>]
                </h3>
            </div>
            <div class="block-content">
                <table id="block-coverage" class="table table-bordered table-striped table-vcenter table-responsive">
                    <thead>
                        <tr>
                            <th rowspan="3">District</th>
                            <th rowspan="3">Block</th>
                            <th rowspan="3">No of GP</th>
                            <th rowspan="3">No. of Farmer Covered (for Nursery and Sowing)</th>
                            <th rowspan="3">Nursery Raised For Covearge Of Area(In Hectare)</th>
                            <th rowspan="3">SMI - Balance Nursery Raised (in Ha.)</th>
                            <th rowspan="3">LT - Balance Nursery Raised (in Ha.)
                            </th>
                            <th colspan="10">Achievement under demonstration (in Ha.)
                                <?= $week_text ?>
                            </th>
                            <th rowspan="3">Total Ragi</th>
                            <th rowspan="3">Total Non-Ragi </th>
                            <th rowspan="3">Follow up Crops</th>
                            <th rowspan="3">Total Area </th>
                            <th rowspan="3">Total Crop Diversification Area
                            </th>
                            <?php if ($season == 'Rabi') { ?>
                                <th rowspan="3">Total Rice Fallow Area
                                </th>
                            <?php } ?>
                            <th rowspan="3">Status </th>
                            <th rowspan="3" class="text-right no-sort">Actions</th>
                        </tr>
                        <tr>
                            <?php foreach ($crop_practices as $crop_id => $practices): ?>
                                <th colspan="<?= count($practices) ?>">
                                    <?= $crops[$crop_id] ?>
                                </th>
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
                        <?php if ($districts) { ?>
                            <?php foreach ($districts as $district) { ?>
                                <tr>
                                    <td>
                                        <?= $district['district_name'] ?>
                                    </td>
                                    <td>
                                        <?= $district['total_block'] ?>
                                    </td>
                                    <td>
                                        <?= $district['total_gp'] ?>
                                    </td>
                                    <td>
                                        <?= $district['farmer_covered'] ?>
                                    </td>
                                    <td>
                                        <?= $district['nursery_raised'] ?>
                                    </td>
                                    <td>
                                        <?= $district['balance_smi'] ?>
                                    </td>
                                    <td>
                                        <?= $district['balance_lt'] ?>
                                    </td>

                                    <? foreach ($district['achievements'] as $achievement) {
                                        $crop_p = $crop_practices[$achievement['crop_id']];
                                        foreach($crop_p as $p){?>
                                        <td data-method="<?= $p ?>"> <?= $achievement[$p] ?></td>
                                        <?}?>
                                    <? } ?>


                                    <td>
                                        <?= $district['total_ragi'] ?>
                                    </td>
                                    <td>
                                        <?= $district['total_non_ragi'] ?>
                                    </td>
                                    <td>
                                        <?= $district['follow_area'] ?>
                                    </td>
                                    <td>
                                        <?= $district['total_area'] ?>
                                    </td>
                                    <td>
                                        <?= $district['crop_div_area'] ?>
                                    </td>

                                    <?php if ($season == 'Rabi') { ?>
                                        <td>
                                            <?= $district['fallow_area'] ?>
                                        </td>
                                    <?php } ?>
                                    <td><label class="badge badge-<?= $district['status_color'] ?>">
                                            <?= $district['status'] ?>
                                        </label></td>
                                    <td>
                                        <div class="btn-group btn-group-sm pull-right">
                                            <?= $district['action'] ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td>Total</td>
                                <td><?=$totals['total_blocks']?></td>
                                <td><?=$totals['total_gp']?></td>
                                <td><?=$totals['total_farmers_covered']?></td>
                                <td><?=$totals['total_nursery_raised']?></td>
                                <td><?=$totals['total_balance_smi']?></td>
                                <td><?=$totals['total_balance_lt']?></td>
                                <? foreach ($totals['achievements_totals'] as $crop_id=>$achievement) {
                                    $crop_p = $crop_practices[$crop_id];
                                    foreach($crop_p as $p){?>
                                    <td> <?= $achievement[$p] ?></td>
                                    <?}?>
                                <?}?>
                                <td><?=$totals['total_ragi']?></td>
                                <td><?=$totals['total_non_ragi']?></td>
                                <td>  <?=$totals['total_follow_area']?></td>
                                <td>  <?=$totals['total_area']?></td>
                                <td>  <?=$totals['total_crop_div_area']?></td>
                                <?php if ($season == 'Rabi') { ?>
                                    <td>  <?=$totals['total_fallow_area']?></td>
                                <?php } ?>
                                <td></td>
                                </td></td>
                            </tr>
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