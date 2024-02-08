<table class="table custom-table " id="txn-table">
    <thead>
        <tr class="highlight-heading1">
            <?php if ($block_id || isset($allgps)) { ?>
                <th rowspan="3">GP</th>
            <?php } else if ($district_id) { ?>
                    <th rowspan="3">Block</th>
                    <th rowspan="3">GPs</th>
            <?php } else if (isset($allblocks)) { ?>
                        <th rowspan="3">District</th>
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
            <th colspan="14">Achievement under demonstration (in Ha.)</th>
            <th rowspan="3">Total Crop Diversification Areas</th>
            <th rowspan="3">Total Rice Fallow Area</th>
        </tr>
        <tr class="highlight-heading2">
            <?php foreach ($crop_practices as $crop_id => $practices): ?>
                <th colspan="<?= count($practices) ?>">
                    <?= $crops[$crop_id] ?>
                </th>
            <?php endforeach; ?>
            <th rowspan="2">Total Ragi</th>
            <th rowspan="2">Total Non-Ragi </th>
            <th rowspan="2">Follow up Crops</th>
            <th rowspan="2">Total Area </th>
        </tr>
        <tr class="highlight-heading3">
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
        <?php foreach ($rows as $block): ?>
            <tr>
                <?php if ($block_id || isset($allgps)) { ?>
                    <td>
                        <?= $block['gp'] ?>
                    </td>
                <?php } else if ($district_id) { ?>
                        <td>
                        <?= $block['block'] ?>
                        </td>
                        <td>
                        <?= $block['gps'] ?>
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
                        <?= $block['district'] ?>
                            </td>
                            <td>
                        <?= $block['blocks'] ?>
                            </td>
                            <td>
                        <?= $block['gps'] ?>
                            </td>
                <?php } ?>
                <td>
                    <?= $block['farmers_covered'] ?>
                </td>
                <td>
                    <?= $block['nursery_raised'] ?>
                </td>
                <td>
                    <?= $block['balance_smi'] ?>
                </td>
                <td>
                    <?= $block['balance_lt'] ?>
                </td>
                <td>
                    <?= $block['ragi_smi'] ?>
                </td>
                <td>
                    <?= $block['ragi_lt'] ?>
                </td>
                <td>
                    <?= $block['ragi_ls'] ?>
                </td>
                <td>
                    <?= $block['little_millet_lt'] ?>
                </td>
                <td>
                    <?= $block['little_millet_ls'] ?>
                </td>
                <td>
                    <?= $block['foxtail_ls'] ?>
                </td>
                <td>
                    <?= $block['sorghum_ls'] ?>
                </td>
                <td>
                    <?= $block['kodo_ls'] ?>
                </td>
                <td>
                    <?= $block['barnyard_ls'] ?>
                </td>
                <td>
                    <?= $block['pearl_ls'] ?>
                </td>
                <td>
                    <?= $block['total_ragi'] ?>
                </td>
                <td>
                    <?= $block['total_non_ragi'] ?>
                </td>
                <td>
                    <?= $block['total_fc'] ?>
                </td>
                <td>
                    <?= $block['total_area'] ?>
                </td>
                <td>
                    <?= $block['total_crop_div'] ?>
                </td>
                <td>
                    <?= $block['total_rfc'] ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>