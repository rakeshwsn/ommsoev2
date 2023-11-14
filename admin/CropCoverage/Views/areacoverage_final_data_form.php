<div class="block block-themed">
    <form>
        <div class="block">
            <div class="block-header block-header-default bg-success">
                <h3 class="block-title">
                    Add Area Coverage Final Data
                </h3>
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
                        <label>Block</label>
                        <select class="form-control" id="block" name="block_id">
                            <option value="">All Blocks</option>
                            <?php foreach ($blocks as $block) { ?>
                                <option value="<?= $block['id'] ?>">
                                    <?= $block['name'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2" style="margin-top:25px;">
                        <button id="btn-submit" class="btn btn-outline btn-primary">
                            Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="block">
    <div class="block-content block-content-full">
        <div class="tableFixHead">
            <table class="table custom-table " id="final-table">
                <thead>
                    <tr>
                        <th rowspan="3">GP</th>
                        <th rowspan="3">No Of Villages</th>
                        <th rowspan="3">Farmer covered under Demonstration</th>
                        <th rowspan="3">Farmer covered under Follow Up Crop</th>
                        <th colspan="12">Achievement under demonstration(in Ha.)</th>

                        <th colspan="7">Follow Up Crops (with out incentive) (in Ha)
                        </th>
                        <th rowspan="3">Total Achievement under demonstration (in Ha.)</th>
                        <th rowspan="3">Total Follow Up Crops</th>



                    </tr>
                    <tr>
                        <?php foreach ($crop_practices as $crop_id => $practices): ?>
                            <th colspan="<?= count($practices) ?>">
                                <?= $crops[$crop_id] ?>
                            </th>
                        <?php endforeach; ?>
                        <th rowspan="2">Total Ragi</th>
                        <th rowspan="2">Total Non Ragi</th>
                        <?php foreach ($crop_practices as $crop_id => $practices): ?>
                            <th rowspan="7">
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
                    <?= form_open('', 'id="form-finaldata"'); ?>
                    <?php foreach ($gpsfinaldata as $index => $gpdata): ?>
                        <tr>
                            <td>
                                <?= $gpdata['name']; ?>
                                <input type="hidden" name="gp_id[]" value="<?= $gpdata['gp_id']; ?>">
                            </td>
                            <!-- Other fields related to GP data -->
                            <td>
                                <input type="number" name="no_of_village[<?= $gpdata['gp_id'] ?>]"
                                    id="no_of_village_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['no_of_village']; ?>">
                            </td>

                            <td>
                                <input type="number" name="farmers_covered_under_demonstration[<?= $gpdata['gp_id'] ?>]"
                                    id="farmers_demonstration_<?= $gpdata['gp_id'] ?>"
                                    value="<?= $gpdata['farmers_covered_under_demonstration']; ?>">
                            </td>
                            <td>
                                <input type="number" name="farmers_covered_under_followup[<?= $gpdata['gp_id'] ?>]"
                                    id="farmers_followup_<?= $gpdata['gp_id'] ?>"
                                    value="<?= $gpdata['farmers_covered_under_followup']; ?>">
                            </td>
                            <!-- Other GP-specific fields -->
                            <?php
                            $keys = ['smi', 'lt', 'ls'];
                            foreach ($gpdata['crops_data'] as $gpcrops):
                                foreach ($keys as $key):
                                    if (isset($gpcrops[$key])):
                                        ?>

                                        <td>
                                            <input type="hidden" name="crop_data[<?= $gpdata['gp_id'] ?>][<?= $gpcrops['id'] ?>][crops]"
                                                value="<?= $gpcrops['crops'] ?>">
                                            <input type="number"
                                                name="crop_data[<?= $gpdata['gp_id'] ?>][<?= $gpcrops['id'] ?>][<?= $key ?>]"
                                                value="<?= $gpcrops[$key] ?>">
                                        </td>
                                        <?php

                                    endif;
                                endforeach;
                            endforeach; ?>

                            <td>
                                <p id="total_ragi">
                                    <?= $totalRagi ?>
                                </p>
                            </td>
                            <td>
                                <p id="total_non_ragi">
                                    <?= $totalNonRagi; ?>
                                </p>
                            </td>
                            <td>
                                <input type="number" name="fup_ragi[<?= $gpdata['gp_id'] ?>]"
                                    id="fup_ragi_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_ragi']; ?>">
                            </td>
                            <td>
                                <input type="number" name="fup_lm[<?= $gpdata['gp_id'] ?>]"
                                    id="fup_lm_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_lm']; ?>">
                            </td>
                            <td>
                                <input type="number" name="fup_fm[<?= $gpdata['gp_id'] ?>]"
                                    id="fup_fm_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_fm']; ?>">
                            </td>
                            <td>
                                <input type="number" name="fup_sorghum[<?= $gpdata['gp_id'] ?>]"
                                    id="fup_sorghum_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_sorghum']; ?>">
                            </td>
                            <td>
                                <input type="number" name="fup_km[<?= $gpdata['gp_id'] ?>]"
                                    id="fup_km_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_km']; ?>">
                            </td>
                            <td>
                                <input type="number" name="fup_bm[<?= $gpdata['gp_id'] ?>]"
                                    id="fup_bm_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_bm']; ?>">
                            </td>
                            <td>
                                <input type="number" name="fup_pm[<?= $gpdata['gp_id'] ?>]"
                                    id="fup_pm_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_pm']; ?>">
                            </td>
                            <td>
                                <input type="text" value="r" name="total_ach_demon" id="total_ach_demon" disabled>
                            </td>
                            <td>
                                <input type="" value="" name="total_fup" id="total_fup" disabled>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                    <?= form_close(); ?>
                </tbody>

            </table>
            <button type="submit" id="submit" form="form-finaldata" class="btn btn-primary">Save</button>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Function to calculate totals
        function calculateTotals() {
            var totalRagi = 0;
            var totalNonRagi = 0;

            var inputFields = document.querySelectorAll("input[name^='crop_data']");
            inputFields.forEach(function (inputField) {
                var value = parseFloat(inputField.value) || 0;

                if (!isNaN(value)) {
                    var inputName = inputField.name;
                    var key = inputName.match(/\[([^\]]+)\]$/)[1];

                    if (key) {
                        if (['smi', 'lt', 'ls'].includes(key)) {
                            totalRagi += value;
                        } else {
                            totalNonRagi += value;
                        }
                    }
                }
            });

            // Update the total values in the HTML
            document.getElementById("total_ragi").textContent = totalRagi.toFixed(2);
            document.getElementById("total_non_ragi").textContent = totalNonRagi.toFixed(2);
        }

        // Use event delegation to handle input changes
        document.addEventListener("input", function (event) {
            if (event.target && event.target.matches("input[name^='crop_data']")) {
                calculateTotals();
            }
        });

        // Initial calculation
        calculateTotals();
    });
</script>