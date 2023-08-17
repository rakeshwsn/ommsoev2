<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title">
            <?= $heading_title; ?>
        </h3>
    </div>
    <div class="block-content block-content-full">
        <div class="row mg-b-25">
            <div class="col-lg-3">
                <div class="form-group mg-b-10-force">
                    <label class="form-control-label">Year: <span class="txt-danger">*
                            <?= $year_id; ?>
                        </span></label>

                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group mg-b-10-force">
                    <label class="form-control-label">Season:<span class="txt-danger">*
                            <?= $season; ?>
                        </span>
                    </label>
                </div>
            </div>
            <table id="block-coverage" class="table table-bordered table-striped table-vcenter table-responsive">
                <thead>
                    <tr>
                        <th rowspan="2">Block</th>
                        <?php
                        $totalRagi = 0;
                        foreach ($heading as $crop => $practices) {
                            if ($crop === 'RAGI') {
                                $totalRagi = count($practices);
                                ?>
                                <th colspan="<?= $totalRagi ?>"><?= $crop; ?></th>
                            <?php } ?>
                        <?php } ?>

                        <?php foreach ($heading as $crop => $practices): ?>
                            <?php if ($crop !== 'RAGI'): ?>
                                <th colspan="<?= count($practices) ?>"><?= $crop; ?></th>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <th colspan="7">Follow Up Crops (with out incentive)(in Ha)</th>
                        <th rowspan="2">Total Ragi</th>
                        <th rowspan="2">Total Non-Ragi</th>
                        <th rowspan="2">Total Follow up Crops</th>
                        <th rowspan="2">Total Target</th>

                        <th class="text-right no-sort rowspan-2">Actions</th>
                    </tr>
                    <tr>
                        <?php foreach ($heading as $crop => $practices): ?>
                            <?php foreach ($practices as $practice): ?>
                                <th>
                                    <?= $practice; ?>
                                </th>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        <?php foreach ($heading as $crop => $practices): ?>
                            <th>
                                <?= $crop; ?>
                            </th>
                        <?php endforeach; ?>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($practicedata as $data) { ?>
                        <tr>
                            <td>
                                <?= $data["block"]; ?>
                            </td>
                            <td>
                                <?= $data["RAGI_SMI"]; ?>
                            </td>
                            <td>
                                <?= $data["RAGI_LT"]; ?>
                            </td>
                            <td>
                                <?= $data["RAGI_LS"]; ?>
                            </td>

                            <td>
                                <?= $data["LITTLE_MILLET_LT"]; ?>
                            </td>
                            <td>
                                <?= $data["LITTLE_MILLET_LS"]; ?>
                            </td>
                            <td>
                                <?= $data["FOXTAIL_MILLET_LS"]; ?>
                            </td>
                            <td>
                                <?= $data["SORGHUM_LS"]; ?>
                            </td>
                            <td>
                                <?= $data["PEARL_MILLET_LS"]; ?>
                            </td>
                            <td>
                                <?= $data["BARNYARD_MILLET_LS"]; ?>
                            </td>
                            <td>
                                <?= $data["KODO_MILLET_LS"]; ?>
                            </td>
                            <td>
                                <?= $data["RAGI_FOLLOWUP"]; ?>
                            </td>
                            <td>
                                <?= $data["LITTLE_MILLET_FOLLOWUP"]; ?>
                            </td>
                            <td>
                                <?= $data["FOXTAIL_MILLET_FOLLOWUP"]; ?>
                            </td>
                            <td>
                                <?= $data["SORGHUM_FOLLOWUP"]; ?>
                            </td>
                            <td>
                                <?= $data["PEARL_MILLET_FOLLOWUP"]; ?>
                            </td>
                            <td>
                                <?= $data["BARNYARD_MILLET_FOLLOWUP"]; ?>
                            </td>
                            <td>
                                <?= $data["KODO_MILLET_FOLLOWUP"]; ?>
                            </td>
                            <td id="ragi" class="ragi">
                                <?= $data["RAGI_SMI"] + $data["RAGI_LT"] + $data["RAGI_LS"]; ?>
                            </td>
                            <td id="non-ragi" class="non-ragi">
                                <?= $data["LITTLE_MILLET_LT"] + $data["LITTLE_MILLET_LS"] + $data["FOXTAIL_MILLET_LS"] + $data["SORGHUM_LS"] + $data["PEARL_MILLET_LS"] + $data["BARNYARD_MILLET_LS"] + $data["KODO_MILLET_LS"]; ?>
                            </td>
                            <td id="follow-up" class="follow-up">
                                <?= $data["RAGI_FOLLOWUP"] + $data["LITTLE_MILLET_FOLLOWUP"] + $data["FOXTAIL_MILLET_FOLLOWUP"] + $data["SORGHUM_FOLLOWUP"] + $data["PEARL_MILLET_FOLLOWUP"] + $data["BARNYARD_MILLET_FOLLOWUP"] + $data["KODO_MILLET_FOLLOWUP"]; ?>
                            </td>
                            <td id="sum-crop" class="sum-crop"> </td>
                            <td>
                                <div class="btn-group btn-group-sm pull-right">
                                    <a class="btn btn-sm btn-primary"
                                        href="<?= $edit; ?>?block_id=<?= $data['block_id']; ?>"
                                        title="<?= $button_edit; ?>"><i class="fa fa-pencil"></i></a>
                                </div>
                            </td>

                        </tr>

                    <?php } ?>
                    <tr>
                        <td colspan="18" class="text-right">Total District Target</td>
                        <td colspan="5" class="all-total"></td>

                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('tr').not(':last-child').each(function () {
            var row = $(this);
            var column1Value = parseFloat(row.find('.ragi').text());
            var column2Value = parseFloat(row.find('.non-ragi').text());
            var column3Value = parseFloat(row.find('.follow-up').text());

            var sum = column1Value + column2Value + column3Value;

            row.find('.sum-crop').text(sum);
        });

        var totalSum = 0;
        $('.sum-crop').each(function () {
            totalSum += parseFloat($(this).text());
        });

        $('.all-total').text(totalSum);
    });
</script>