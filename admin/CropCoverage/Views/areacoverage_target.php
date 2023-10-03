<style>
    .cl-head {
        color: white;
        background-color: black;
    }
</style>
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
                    <select name="year" id="year" class="form-control" disabled>
                        <?php foreach ($years as $year) { ?>
                            <option value="<?= $year['id'] ?>" <?php if ($year['id'] == $year['id']) {
                                  echo 'selected';
                              } ?>><?= $year['name'] ?>
                            </option>
                        <?php } ?>
                    </select>

                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group mg-b-10-force">
                    <select name="season" id="season" class="form-control">
                        <option value="">Select Season</option>
                        <?php foreach ($seasons as $value => $season) { ?>
                            <option value="<?= $value ?>" <?php if ($value == $current_season) {
                                  echo 'selected';
                              } ?>>
                                <?= $season ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tableFixHead">
    <?php
    if (empty($district_id)) { ?>
        <table id="block-coverage" class="table table-bordered table-striped table-vcenter table-responsive">
            <thead>
                <tr>
                    <th rowspan="2" class="cl-head">District</th>
                    <th rowspan="2" class="cl-head">No of Block</th>

                    <?php
                    $totalRagi = 0;
                    foreach ($heading as $crop => $practices) {
                        if ($crop === 'RAGI') {
                            $totalRagi = count($practices);
                            ?>
                            <th colspan="<?= $totalRagi; ?>" class="cl-head">
                                <?= $crop; ?>
                            </th>
                        <?php } ?>
                    <?php } ?>

                    <?php foreach ($heading as $crop => $practices): ?>
                        <?php if ($crop !== 'RAGI'): ?>
                            <th colspan="<?= count($practices); ?>" class="cl-head">
                                <?= $crop; ?>
                            </th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <th colspan="7" class="cl-head">Follow Up Crops (with out incentive)(in Ha)</th>
                    <th rowspan="2" class="cl-head">Total Ragi</th>
                    <th rowspan="2" class="cl-head">Total Non-Ragi</th>

                    <th rowspan="2" class="cl-head">Total Follow up Crops</th>
                    <th rowspan="2" class="cl-head">Total Target</th>


                </tr>
                <tr>
                    <?php foreach ($heading as $crop => $practices): ?>
                        <?php foreach ($practices as $practice): ?>
                            <th class="cl-head">
                                <?= $practice; ?>
                            </th>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    <?php foreach ($heading as $crop => $practices): ?>
                        <th class="cl-head">
                            <?= $crop; ?>
                        </th>
                    <?php endforeach; ?>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($distwisetarget as $disttarget) { ?>
                    <tr>
                        <td>
                            <?= $disttarget["district"]; ?>
                        </td>
                        <td>
                            <?= $disttarget["no_of_block"]; ?>
                        </td>

                        <td>
                            <?= $disttarget["RAGI_SMI"]; ?>
                        </td>
                        <td>
                            <?= $disttarget["RAGI_LT"]; ?>
                        </td>
                        <td>
                            <?= $disttarget["RAGI_LS"]; ?>
                        </td>

                        <td>
                            <?= $disttarget["LITTLE_MILLET_LT"]; ?>
                        </td>
                        <td>
                            <?= $disttarget["LITTLE_MILLET_LS"]; ?>
                        </td>
                        <td>
                            <?= $disttarget["FOXTAIL_MILLET_LS"]; ?>
                        </td>
                        <td>
                            <?= $disttarget["SORGHUM_LS"]; ?>
                        </td>
                        <td>
                            <?= $disttarget["PEARL_MILLET_LS"]; ?>
                        </td>
                        <td>
                            <?= $disttarget["BARNYARD_MILLET_LS"]; ?>
                        </td>
                        <td>
                            <?= $disttarget["KODO_MILLET_LS"]; ?>
                        </td>
                        <td>
                            <?= $disttarget["RAGI_FOLLOWUP"]; ?>
                        </td>
                        <td>
                            <?= $disttarget["LITTLE_MILLET_FOLLOWUP"]; ?>
                        </td>
                        <td>
                            <?= $disttarget["FOXTAIL_MILLET_FOLLOWUP"]; ?>
                        </td>
                        <td>
                            <?= $disttarget["SORGHUM_FOLLOWUP"]; ?>
                        </td>
                        <td>
                            <?= $disttarget["PEARL_MILLET_FOLLOWUP"]; ?>
                        </td>
                        <td>
                            <?= $disttarget["BARNYARD_MILLET_FOLLOWUP"]; ?>
                        </td>
                        <td>
                            <?= $disttarget["KODO_MILLET_FOLLOWUP"]; ?>
                        </td>
                        <td id="dist-ragi" class="dist-ragi">
                            <?= $disttarget["RAGI_SMI"] + $disttarget["RAGI_LT"] + $disttarget["RAGI_LS"]; ?>

                        </td>
                        <td id="dist-non-ragi" class="dist-non-ragi">
                            <?= $disttarget["LITTLE_MILLET_LT"] + $disttarget["LITTLE_MILLET_LS"] + $disttarget["FOXTAIL_MILLET_LS"] + $disttarget["SORGHUM_LS"] + $disttarget["PEARL_MILLET_LS"] + $disttarget["BARNYARD_MILLET_LS"] + $disttarget["KODO_MILLET_LS"]; ?>
                        </td>
                        <td id="dist-follow-up" class="dist-follow-up">
                            <?= $disttarget["RAGI_FOLLOWUP"] + $disttarget["LITTLE_MILLET_FOLLOWUP"] + $disttarget["FOXTAIL_MILLET_FOLLOWUP"] + $disttarget["SORGHUM_FOLLOWUP"] + $disttarget["PEARL_MILLET_FOLLOWUP"] + $disttarget["BARNYARD_MILLET_FOLLOWUP"] + $disttarget["KODO_MILLET_FOLLOWUP"]; ?>
                        </td>
                        <td id="dist-sum-crop" class="dist-sum-crop"> </td>

                    </tr>

                <?php } ?>

                <tr>
                    <td colspan="2" class="text-right cl-head">Millet Wise Total</td>

                    <td colspan="3" class="total-ragi cl-head">
                        <?php
                        $totalRagiSum = 0;
                        foreach ($distwisetarget as $disttarget) {
                            // Calculate the sum of Ragi values for each row
                            $ragiSum = $disttarget["RAGI_SMI"] + $disttarget["RAGI_LT"] + $disttarget["RAGI_LS"];
                            $totalRagiSum += $ragiSum;
                        }
                        echo $totalRagiSum; // Display the total Ragi sum
                        ?>
                    </td>
                    <td colspan="2" class="total-little-millet cl-head">
                        <?php
                        $totalLittleMilletSum = 0;
                        foreach ($distwisetarget as $disttarget) {
                            // Calculate the sum of Little Millet values for each row
                            $littleMilletSum = $disttarget["LITTLE_MILLET_LT"] + $disttarget["LITTLE_MILLET_LS"];
                            $totalLittleMilletSum += $littleMilletSum;
                        }
                        echo $totalLittleMilletSum; // Display the total Little Millet sum
                        ?>
                    </td>
                    <td class="total-foxtail-millet cl-head">
                        <?php
                        $totalFoxtailMilletSum = 0;
                        foreach ($distwisetarget as $disttarget) {
                            // Calculate the sum of Foxtail Millet ls values for each row
                            $foxtailMilletSum = $disttarget["FOXTAIL_MILLET_LS"];
                            $totalFoxtailMilletSum += $foxtailMilletSum;
                        }
                        echo $totalFoxtailMilletSum;
                        ?>
                    </td>

                    <td class="total-sorghum cl-head">
                        <?php
                        $totalSorghumSum = 0;
                        foreach ($distwisetarget as $disttarget) {

                            $sorghumSum = $disttarget["SORGHUM_LS"];
                            $totalSorghumSum += $sorghumSum;
                        }
                        echo $totalSorghumSum;
                        ?>
                    </td>
                    <td class="total-kodo-millet cl-head">
                        <?php
                        $totalKodoMilletSum = 0;
                        foreach ($distwisetarget as $disttarget) {
                            // Calculate the sum of Kodo Millet ls values for each row
                            $kodoMilletSum = $disttarget["KODO_MILLET_LS"];
                            $totalKodoMilletSum += $kodoMilletSum;
                        }
                        echo $totalKodoMilletSum; // Display the total Kodo Millet sum
                        ?>
                    </td>
                    <td class="total-barnyard-millet cl-head">
                        <?php
                        $totalBarnyardMilletSum = 0;
                        foreach ($distwisetarget as $disttarget) {
                            // Calculate the sum of Barnyard Millet ls values for each row
                            $barnyardMilletSum = $disttarget["BARNYARD_MILLET_LS"];
                            $totalBarnyardMilletSum += $barnyardMilletSum;
                        }
                        echo $totalBarnyardMilletSum; // Display the total Barnyard Millet sum
                        ?>
                    </td>
                    <td class="total-pearl-millet cl-head">
                        <?php
                        $totalPearlMilletSum = 0;
                        foreach ($distwisetarget as $disttarget) {
                            // Calculate the sum of Pearl Millet ls values for each row
                            $pearlMilletSum = $disttarget["PEARL_MILLET_LS"];
                            $totalPearlMilletSum += $pearlMilletSum;
                        }
                        echo $totalPearlMilletSum; // Display the total Pearl Millet sum
                        ?>
                    </td>
                    <td class="total-ragi-followup cl-head">
                        <?php
                        $totalRagiFollowupSum = 0;
                        foreach ($distwisetarget as $disttarget) {
                            // Calculate the sum of RAGI_FOLLOWUP values for each row
                            $ragiFollowupSum = $disttarget["RAGI_FOLLOWUP"];
                            $totalRagiFollowupSum += $ragiFollowupSum;
                        }
                        echo $totalRagiFollowupSum; // Display the total RAGI_FOLLOWUP sum
                        ?>
                    </td>
                    <td class="total-little-millet-followup cl-head">
                        <?php
                        $totalLittleMilletFollowupSum = 0;
                        foreach ($distwisetarget as $disttarget) {
                            // Calculate the sum of LITTLE_MILLET_FOLLOWUP values for each row
                            $littleMilletFollowupSum = $disttarget["LITTLE_MILLET_FOLLOWUP"];
                            $totalLittleMilletFollowupSum += $littleMilletFollowupSum;
                        }
                        echo $totalLittleMilletFollowupSum; // Display the total LITTLE_MILLET_FOLLOWUP sum
                        ?>
                    </td>

                    <td class="total-foxtail-millet-followup cl-head">
                        <?php
                        $totalFoxtailMilletFollowupSum = 0;
                        foreach ($distwisetarget as $disttarget) {
                            // Calculate the sum of FOXTAIL_MILLET_FOLLOWUP values for each row
                            $foxtailMilletFollowupSum = $disttarget["FOXTAIL_MILLET_FOLLOWUP"];
                            $totalFoxtailMilletFollowupSum += $foxtailMilletFollowupSum;
                        }
                        echo $totalFoxtailMilletFollowupSum; // Display the total FOXTAIL_MILLET_FOLLOWUP sum
                        ?>
                    </td>

                    <td class="total-sorghum-followup cl-head">
                        <?php
                        $totalSorghumFollowupSum = 0;
                        foreach ($distwisetarget as $disttarget) {
                            // Calculate the sum of SORGHUM_FOLLOWUP values for each row
                            $sorghumFollowupSum = $disttarget["SORGHUM_FOLLOWUP"];
                            $totalSorghumFollowupSum += $sorghumFollowupSum;
                        }
                        echo $totalSorghumFollowupSum; // Display the total SORGHUM_FOLLOWUP sum
                        ?>
                    </td>

                    <td class="total-pearl-millet-followup cl-head">
                        <?php
                        $totalPearlMilletFollowupSum = 0;
                        foreach ($distwisetarget as $disttarget) {
                            // Calculate the sum of PEARL_MILLET_FOLLOWUP values for each row
                            $pearlMilletFollowupSum = $disttarget["PEARL_MILLET_FOLLOWUP"];
                            $totalPearlMilletFollowupSum += $pearlMilletFollowupSum;
                        }
                        echo $totalPearlMilletFollowupSum; // Display the total PEARL_MILLET_FOLLOWUP sum
                        ?>
                    </td>

                    <td class="total-barnyard-millet-followup cl-head">
                        <?php
                        $totalBarnyardMilletFollowupSum = 0;
                        foreach ($distwisetarget as $disttarget) {
                            // Calculate the sum of BARNYARD_MILLET_FOLLOWUP values for each row
                            $barnyardMilletFollowupSum = $disttarget["BARNYARD_MILLET_FOLLOWUP"];
                            $totalBarnyardMilletFollowupSum += $barnyardMilletFollowupSum;
                        }
                        echo $totalBarnyardMilletFollowupSum; // Display the total BARNYARD_MILLET_FOLLOWUP sum
                        ?>
                    </td>

                    <td class="total-kodo-millet-followup cl-head">
                        <?php
                        $totalKodoMilletFollowupSum = 0;
                        foreach ($distwisetarget as $disttarget) {
                            // Calculate the sum of KODO_MILLET_FOLLOWUP values for each row
                            $kodoMilletFollowupSum = $disttarget["KODO_MILLET_FOLLOWUP"];
                            $totalKodoMilletFollowupSum += $kodoMilletFollowupSum;
                        }
                        echo $totalKodoMilletFollowupSum; // Display the total KODO_MILLET_FOLLOWUP sum
                        ?>
                    </td>
                    <td colspan="3" class="cl-head">Total Target</td>
                    <td class="all-total cl-head"></td>
                </tr>
            </tbody>
        </table>
    </div>
<?php } else { ?>
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
                        <th colspan="<?= $totalRagi ?>">
                            <?= $crop; ?>
                        </th>
                    <?php } ?>
                <?php } ?>

                <?php foreach ($heading as $crop => $practices): ?>
                    <?php if ($crop !== 'RAGI'): ?>
                        <th colspan="<?= count($practices) ?>">
                            <?= $crop; ?>
                        </th>
                    <?php endif; ?>
                <?php endforeach; ?>
                <th colspan="7">Follow Up Crops (with out incentive)(in Ha)</th>
                <th rowspan="2">Total Ragi</th>
                <th rowspan="2">Total Non-Ragi</th>
                <th rowspan="2">Total Follow up Crops</th>
                <th rowspan="2">Total Target</th>

                <th class="text-right no-sort" rowspan="2">Actions</th>
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
            <?php foreach ($blockstarget as $data) { ?>
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
                    <td id="sum-crop-block" class="sum-crop-block"> </td>
                    <td>
                        <div class="btn-group btn-group-sm pull-right">
                            <a class="btn btn-sm btn-primary" href="<?= $edit; ?>?block_id=<?= $data['block_id']; ?>"
                                title="<?= $button_edit; ?>"><i class="fa fa-pencil"></i></a>
                        </div>
                    </td>

                </tr>

            <?php } ?>
            <tr>
                <td colspan="18" class="text-right">Total District Target</td>
                <td colspan="5" class="all-total-block"></td>

            </tr>
        </tbody>
    </table>
<?php } ?>

<script>
    $(document).ready(function () {
        $('tr').not(':last-child').each(function () {
            var row = $(this);
            var column1Value = parseFloat(row.find('.ragi').text());
            var column2Value = parseFloat(row.find('.non-ragi').text());
            var column3Value = parseFloat(row.find('.follow-up').text());

            var sum = column1Value + column2Value + column3Value;

            row.find('.sum-crop-block').text(sum);
        });

        var totalSum = 0;
        $('.sum-crop-block').each(function () {
            totalSum += parseFloat($(this).text());
        });

        $('.all-total-block').text(totalSum);
    });
</script>
<script>
    $(document).ready(function () {
        $('tr').not(':last-child').each(function () {
            var row = $(this);
            var column1Value = parseFloat(row.find('.dist-ragi').text());
            var column2Value = parseFloat(row.find('.dist-non-ragi').text());
            var column3Value = parseFloat(row.find('.dist-follow-up').text());

            var sum = column1Value + column2Value + column3Value;

            row.find('.dist-sum-crop').text(sum);
        });

        var totalSum = 0;
        $('.dist-sum-crop').each(function () {
            totalSum += parseFloat($(this).text());
        });

        $('.all-total').text(totalSum);
    });
</script>