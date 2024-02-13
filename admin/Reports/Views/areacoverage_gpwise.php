<?= $filter_panel ?>
<div class="block block-themed">
    <div class="block-header bg-muted">
        <h3 class="block-title">
            <?= $title ?>
        </h3>
        <div class="block-options">
            <a href="<?= $download_url ?>" class="btn btn-secondary" data-toggle="tooltip"
                data-original-title="Download">
                <i class="si si-cloud-download"></i>
            </a>
        </div>
    </div>
    <div class="block-content block-content-full">
        <div class="tableFixHead">
            <table class="table custom-table " id="txn-table">
                <thead>
                    <tr>
                        <th rowspan="3">District</th>
                        <th rowspan="3">Block</th>
                        <th rowspan="3">GP</th>
                        <th rowspan="3">No. of Farmer Covered (for Nursery and Sowing)</th>
                        <th colspan="14">Achievement under demonstration (in Ha.)</th>
                        <th rowspan="3">Total Crop Diversification Area
                        </th>
                        <?php if ($current_season == 'rabi') { ?>
                            <th rowspan="3">Total Rice Fallow Area
                            </th>
                        <?php } ?>
                    </tr>
                    <tr>
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
                    <?php foreach ($rows as $gp):

                        ?>
                        <tr>
                            <td>
                                <?= $gp['district_name'] ?>
                            </td>
                            <td>
                                <?= $gp['block_name'] ?>
                            </td>
                            <td>
                                <?= $gp['gp'] ?>
                            </td>

                            <td>
                                <?= $gp['farmers_covered'] ?>
                            </td>
                            <td>
                                <?= $gp['ragi_smi'] ?>
                            </td>
                            <td>
                                <?= $gp['ragi_lt'] ?>
                            </td>
                            <td>
                                <?= $gp['ragi_ls'] ?>
                            </td>
                            <td>
                                <?= $gp['little_millet_lt'] ?>
                            </td>
                            <td>
                                <?= $gp['little_millet_ls'] ?>
                            </td>
                            <td>
                                <?= $gp['foxtail_ls'] ?>
                            </td>
                            <td>
                                <?= $gp['sorghum_ls'] ?>
                            </td>
                            <td>
                                <?= $gp['kodo_ls'] ?>
                            </td>
                            <td>
                                <?= $gp['barnyard_ls'] ?>
                            </td>
                            <td>
                                <?= $gp['pearl_ls'] ?>
                            </td>
                            <td>
                                <?= $gp['total_ragi'] ?>
                            </td>
                            <td>
                                <?= $gp['total_non_ragi'] ?>
                            </td>
                            <td>
                                <?= $gp['total_fc'] ?>
                            </td>
                            <td>
                                <?= $gp['total_area'] ?>
                            </td>
                            <td>
                                <?= $gp['total_crop_div'] ?>
                            </td>
                            <?php if ($current_season == 'rabi') { ?>
                                <td>
                                    <?= $gp['total_rfc'] ?>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#district').on('change', function () {
            district_id = $(this).val();
            $.ajax({
                url: '<?= $get_blocks ?>',
                data: { district_id: district_id },
                type: 'GET',
                dataType: 'JSON',
                success: function (res) {
                    html = '<option value="">All Blocks</option>';
                    if (res) {
                        $.each(res, function (i, v) {
                            html += '<option value="' + v.id + '">' + v.name + '</option>';
                        });
                    }
                    $('#block').html(html);
                },
                error: function () {
                    alert('Something went wrong');
                }
            });
        });
    });
</script>