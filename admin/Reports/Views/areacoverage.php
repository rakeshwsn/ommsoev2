    <?=$filter_panel?>

    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">Report</h3>
            <div class="block-options">
                <a href="<?=$download_url?>" class="btn btn-secondary" data-toggle="tooltip" data-original-title="Download">
                    <i class="si si-cloud-download"></i>
                </a>
            </div>
        </div>
        <div class="block-content block-content-full">
            <div class="tableFixHead">
                <table class="table custom-table " id="txn-table">
                    <thead>
                    <tr>
                        <?php if($block_id){ ?>
                            <th rowspan="3">GP</th>
                        <?php } else if($district_id){ ?>
                            <th rowspan="3">Block</th>
                            <th rowspan="3">GPs</th>
                        <?php } else { ?>
                            <th rowspan="3">District</th>
                            <th rowspan="3">Blocks</th>
                            <th rowspan="3">GPs</th>
                        <?php } ?>
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
                        <?php foreach ($blocks as $block): ?>
                            <tr>
                                <td><?=$block['district']?></td>
                                <td><?=$block['blocks']?></td>
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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
