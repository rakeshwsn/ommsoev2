<table class="table custom-table " id="txn-table">
    <thead>
        <tr>
            <?php if ($block_id) { ?>
                <th rowspan="3">GP</th>
            <?php } else if ($district_id) { ?>
                    <th rowspan="3">Block</th>
                    <th rowspan="3">Total GPs</th>
            <?php } else if (isset($allblocks)) { ?>
                        <th rowspan="3">District</th>
                        <th rowspan="3">Block</th>
                        <th rowspan="3">GPs</th>
            <?php } else { ?>
                        <th rowspan="3">District</th>
                        <th rowspan="3">Total Blocks</th>
                        <th rowspan="3">Total GPs</th>
            <?php } ?>
            <th rowspan="3">Villages</th>
            <th rowspan="3">Farmer covered under Demonstration</th>
            <th rowspan="3">Farmer covered under Follow Up Crop</th>
            <th colspan="12">Achievement under demonstration(in Ha.)</th>
            <th rowspan="3">Total Follow up Crops</th>
            <th rowspan="3">Total Area </th>
        </tr>
        <tr>
            <?php foreach ($crop_practices as $crop_id => $practices): ?>
                <th colspan="<?= count($practices) ?>">
                    <?= $crops[$crop_id] ?>
                </th>
            <?php endforeach; ?>
            <th rowspan="2">Total Ragi</th>
            <th rowspan="2">Total Non-Ragi </th>
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
        <?php foreach ($blocksfd as $blockfd): ?>
            <tr>
                <?php if ($block_id) { ?>
                    <td>
                        <?= $blockfd->gp ?>
                    </td>
                <?php } else if ($district_id) { ?>
                        <td>
                        <?= $blockfd->block ?>
                        </td>
                        <td>
                        <?= $blockfd->total_gp ?>
                        </td>
                <?php } else if (isset($allblocks)) { ?>
                            <td>
                        <?= $block['district'] ?>
                            </td>
                            <td>
                        <?= $block['block'] ?>
                            </td>
                            <td>
                        <?= $block['gps'] ?>
                            </td>
                <?php } else { ?>
                            <td>
                        <?= $blockfd->district ?>
                            </td>
                            <td>
                        <?= $blockfd->blocks ?>
                            </td>
                            <td>
                        <?= $blockfd->gps ?>
                            </td>
                <?php } ?>
                <td>
                    <?= $blockfd->total_village ?>
                </td>
                <td>
                    <?= $blockfd->total_demon_farmer ?>
                </td>
                <td>
                    <?= $blockfd->total_follow_farmer ?>
                </td>
                <td>
                    <?= $blockfd->ragi_total_smi ?>
                </td>
                <td>
                    <?= $blockfd->ragi_total_lt ?>
                </td>
                <td>
                    <?= $blockfd->ragi_ls ?>
                </td>
                <td>
                    <?= $blockfd->little_millet_lt ?>
                </td>
                <td>
                    <?= $blockfd->little_millet_ls ?>
                </td>
                <td>
                    <?= $blockfd->foxtail_millet_ls ?>
                </td>
                <td>
                    <?= $blockfd->sorghum_ls ?>
                </td>
                <td>
                    <?= $blockfd->kodo_millet_ls ?>
                </td>
                <td>
                    <?= $blockfd->barnyard_millet_ls ?>
                </td>
                <td>
                    <?= $blockfd->pearl_millet_ls ?>
                </td>
                <td>
                    <?= $blockfd->ragi_total_smi + $blockfd->ragi_total_lt + $blockfd->ragi_ls ?>
                </td>
                <td>
                    <?= $blockfd->little_millet_lt + $blockfd->little_millet_ls + $blockfd->foxtail_millet_ls + $blockfd->sorghum_ls + $blockfd->kodo_millet_ls + $blockfd->barnyard_millet_ls + $blockfd->pearl_millet_ls ?>
                </td>
                <td>
                    <?= $blockfd->total_fup ?>
                </td>
                <td>
                    <?= $blockfd->ragi_total_smi + $blockfd->ragi_total_lt + $blockfd->ragi_ls + $blockfd->little_millet_lt + $blockfd->little_millet_ls + $blockfd->foxtail_millet_ls + $blockfd->sorghum_ls + $blockfd->kodo_millet_ls + $blockfd->barnyard_millet_ls + $blockfd->pearl_millet_ls + $blockfd->total_fup ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>