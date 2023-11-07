<div class="block block-themed">
    <form>
        <div class="block">
            <div class="block-header block-header-default bg-success">
                <h3 class="block-title">
                    <?= $heading_title; ?>
                </h3>
                <div class="block-options">
                    <a href="<?= $add; ?>" data-toggle="tooltip" title="add" class="btn btn-primary ajaxaction">Add<i
                            class="fa fa-plus"></i></a>
                </div>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-md-2">
                        <label>Year</label>
                        <select class="form-control" id="year_id" name="year_id" disabled>
                            <?php foreach ($years as $year) { ?>
                                <option value="<?= $year['id'] ?>" <?php if ($year['id'] == $year['id']) {
                                      echo 'selected';
                                  } ?>><?= $year['name'] ?>
                                </option>
                            <?php } ?>

                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Season</label>
                        <select class="form-control" id="season" name="season" disabled>
                            <?php foreach ($seasons as $value => $season) { ?>
                                <option value="<?= $value ?>" <?php if ($value == $current_season) {
                                      echo 'selected';
                                  } ?>>
                                    <?= $season ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Districts</label>
                        <select class="form-control" id="district" name="district_id">
                            <option>Select District</option>

                            <?php foreach ($districts as $district) { ?>
                                <option value="<?= $district['id'] ?>" <?php if ($district['id'] == $district_id) {
                                      echo 'selected';
                                  } ?>>
                                    <?= $district['name'] ?>
                                </option>
                            <?php } ?>

                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Block</label>
                        <select class="form-control" id="block" name="block_id">
                            <option value="">All Blocks</option>
                            <?php foreach ($blocks as $block) { ?>
                                <option value="<?= $block['id'] ?>" <?php if ($block['id'] == $block_id) {
                                      echo 'selected';
                                  } ?>>
                                    <?= $block['name'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>


                    <div class="col-md-2" style="margin-top:25px;">
                        <a href="" class="btn btn-square btn-info min-width-125 mb-10">filter</a>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="block">
    <div class="block-header block-header-default  bg-primary">
        <h3 class="block-title">View Area Coverage Final Data</h3>
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
                        <?php if ($block_id) { ?>
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
                                    <th rowspan="3">Total Blocks</th>
                                    <th rowspan="3">Total GPs</th>
                        <?php } ?>
                        <th rowspan="3">No Of Villages</th>
                        <th rowspan="3">Farmer covered under Demonstration</th>
                        <th rowspan="3">Farmer covered under Follow Up Crop</th>
                        <th colspan="12">Achievement under demonstration (in Ha.)</th>
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
        </div>
    </div>
</div>
<?php js_start(); ?>
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
        // $('[id="district"]').trigger('change');

    });
</script>
<?php js_end(); ?>