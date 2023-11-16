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
                                    id="no_of_village_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['no_of_village']; ?>"
                                    oninput="validatePositiveInteger(this, 5)">
                            </td>

                            <td>
                                <input type=" number" name="farmers_covered_under_demonstration[<?= $gpdata['gp_id'] ?>]"
                                    id="farmers_demonstration_<?= $gpdata['gp_id'] ?>"
                                    value="<?= $gpdata['farmers_covered_under_demonstration']; ?>"
                                    oninput="validatePositiveInteger(this, 5)">
                            </td>
                            <td>
                                <input type="number" name="farmers_covered_under_followup[<?= $gpdata['gp_id'] ?>]"
                                    id="farmers_followup_<?= $gpdata['gp_id'] ?>"
                                    value="<?= $gpdata['farmers_covered_under_followup']; ?>"
                                    oninput="validatePositiveInteger(this,5)">
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
                                                value="<?= $gpcrops['crops'] ?>" oninput="validateField(this,7)">
                                            <input type="number"
                                                name="crop_data[<?= $gpdata['gp_id'] ?>][<?= $gpcrops['id'] ?>][<?= $key ?>]"
                                                value="<?= $gpcrops[$key] ?>" oninput="validateField(this,7)">
                                        </td>
                                        <?php

                                    endif;
                                endforeach;
                            endforeach; ?>

                            <td>
                                <p id="total_ragi">
                                    10
                                </p>
                            </td>
                            <td>
                                <p id="total_non_ragi">
                                    15
                                </p>
                            </td>
                            <td>
                                <input type="number" name="fup_ragi[<?= $gpdata['gp_id'] ?>]"
                                    id="fup_ragi_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_ragi']; ?>"
                                    oninput="validateField(this,7)">
                            </td>
                            <td>
                                <input type="number" name="fup_lm[<?= $gpdata['gp_id'] ?>]"
                                    id="fup_lm_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_lm']; ?>"
                                    oninput="validateField(this,7)">
                            </td>
                            <td>
                                <input type="number" name="fup_fm[<?= $gpdata['gp_id'] ?>]"
                                    id="fup_fm_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_fm']; ?>"
                                    oninput="validateField(this,7)">
                            </td>
                            <td>
                                <input type="number" name="fup_sorghum[<?= $gpdata['gp_id'] ?>]"
                                    id="fup_sorghum_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_sorghum']; ?>"
                                    oninput="validateField(this,7)">
                            </td>
                            <td>
                                <input type="number" name="fup_km[<?= $gpdata['gp_id'] ?>]"
                                    id="fup_km_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_km']; ?>"
                                    oninput="validateField(this,7)">
                            </td>
                            <td>
                                <input type="number" name="fup_bm[<?= $gpdata['gp_id'] ?>]"
                                    id="fup_bm_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_bm']; ?>"
                                    oninput="validateField(this,7)">
                            </td>
                            <td>
                                <input type="number" name="fup_pm[<?= $gpdata['gp_id'] ?>]"
                                    id="fup_pm_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_pm']; ?>"
                                    oninput="validateField(this,7)">
                            </td>
                            <td>
                                <input type="text" value="12" name="total_ach_demon" id="total_ach_demon" disabled>
                            </td>
                            <td>
                                <input type="" value="15" name="total_fup" id="total_fup" disabled>
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
    function validateField(field, maxDigits) {
        var inputValue = field.value.trim();
        var decimalRegex = /^\d+(\.\d{1,5})?$/; // Regular expression for valid decimal numbers

        if (!decimalRegex.test(inputValue) || inputValue.length > maxDigits) {
            field.setCustomValidity('Please enter a valid positive decimal number with up to 5 decimal places and a maximum of 7 digits.');
        } else {
            field.setCustomValidity('');
        }
    }
</script>
<script>
    function validatePositiveInteger(input, maxLength) {
        // Remove non-numeric characters (except ".") from the input
        input.value = input.value.replace(/[^0-9]/g, '');

        // Ensure the input is not negative
        if (parseInt(input.value) < 0) {
            input.value = '';
        }

        // Ensure the input has a maximum length of maxLength
        if (input.value.length > maxLength) {
            input.value = input.value.slice(0, maxLength);
        }
    }
</script>