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
                                <option value="<?= $year['id'] ?>" <?php if ($year['id'] == $year_id) {
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
                                <option value="<?= $value ?>" <?php if ($value == strtolower($current_season)) {
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
                                <option value="<?= $block['id'] ?>" <?php if ($block['id'] == $block_id) {

                                      echo 'selected';
                                  } ?>>
                                    <?= $block['name'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2" style="margin-top:25px;">
                        <button id="btn-filter" class="btn btn-outline btn-primary">
                            <i class="fa fa-filter"></i> Filter</button>
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
                                <input type="hidden" name="area[<?= $gpdata['gp_id'] ?>][master_id]"
                                    value="<?= $gpdata['id']; ?>">
                                <input type="hidden" name="area[<?= $gpdata['gp_id'] ?>][gp_id]"
                                    value="<?= $gpdata['gp_id']; ?>">

                            </td>
                            <!-- Other fields related to GP data -->
                            <td>
                                <input type="number" name="area[<?= $gpdata['gp_id'] ?>][no_of_village]"
                                    id="no_of_village_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['no_of_village']; ?>"
                                    oninput="validatePositiveInteger(this, 5)">
                            </td>
                            <td>
                                <input type=" number"
                                    name="area[<?= $gpdata['gp_id'] ?>][farmers_covered_under_demonstration]"
                                    id="farmers_demonstration_<?= $gpdata['gp_id'] ?>"
                                    value="<?= $gpdata['farmers_covered_under_demonstration']; ?>"
                                    oninput="validatePositiveInteger(this, 5)">
                            </td>
                            <td>
                                <input type="number" name="area[<?= $gpdata['gp_id'] ?>][farmers_covered_under_followup]"
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
                                            <input type="hidden"
                                                name="area[<?= $gpdata['gp_id'] ?>][crop_data][<?= $gpcrops['id'] ?>][<?= $key ?>]"
                                                value="<?= $gpcrops[$key] ?>" oninput="validateField(this, 7)"
                                                data-crop-id="<?= $gpcrops['id'] ?>" data-gp-id="<?= $gpdata['gp_id'] ?>">

                                            <input type="number"
                                                name="area[<?= $gpdata['gp_id'] ?>][crop_data][<?= $gpcrops['id'] ?>][<?= $key ?>]"
                                                value="<?= $gpcrops[$key] ?>" oninput="validateField(this, 7)"
                                                data-crop-id="<?= $gpcrops['id'] ?>" data-gp-id="<?= $gpdata['gp_id'] ?>">
                                        </td>
                                        <?php
                                    endif;
                                endforeach;
                            endforeach; ?>
                            <td>
                                <p id="total_ragi_<?= $gpdata['gp_id'] ?>"></p>
                            </td>
                            <td>
                                <p id="total_non_ragi_<?= $gpdata['gp_id'] ?>"></p>
                            </td>
                            <td>
                                <input type="number" name="area[<?= $gpdata['gp_id'] ?>][fup_ragi]" class="fup-input"
                                    id="fup_ragi_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_ragi']; ?>"
                                    oninput="validateField(this,7)" data-gp-id="<?= $gpdata['gp_id'] ?>">
                            </td>
                            <td>
                                <input type="number" name="area[<?= $gpdata['gp_id'] ?>][fup_lm]" class="fup-input"
                                    id="fup_lm_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_lm']; ?>"
                                    oninput="validateField(this,7)" data-gp-id="<?= $gpdata['gp_id'] ?>">
                            </td>
                            <td>
                                <input type="number" name="area[<?= $gpdata['gp_id'] ?>][fup_fm]" class="fup-input"
                                    id="fup_fm_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_fm']; ?>"
                                    oninput="validateField(this,7)" data-gp-id="<?= $gpdata['gp_id'] ?>">
                            </td>
                            <td>
                                <input type="number" name="area[<?= $gpdata['gp_id'] ?>][fup_sorghum]" class="fup-input"
                                    id="fup_sorghum_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_sorghum']; ?>"
                                    oninput="validateField(this,7)" data-gp-id="<?= $gpdata['gp_id'] ?>">
                            </td>
                            <td>
                                <input type="number" name="area[<?= $gpdata['gp_id'] ?>][fup_km]" class="fup-input"
                                    id="fup_km_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_km']; ?>"
                                    oninput="validateField(this,7)" data-gp-id="<?= $gpdata['gp_id'] ?>">
                            </td>
                            <td>
                                <input type="number" name="area[<?= $gpdata['gp_id'] ?>][fup_bm]" class="fup-input"
                                    id="fup_bm_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_bm']; ?>"
                                    oninput="validateField(this,7)" data-gp-id="<?= $gpdata['gp_id'] ?>">
                            </td>
                            <td>
                                <input type="number" name="area[<?= $gpdata['gp_id'] ?>][fup_pm]" class="fup-input"
                                    id="fup_pm_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_pm']; ?>"
                                    oninput="validateField(this,7)" data-gp-id="<?= $gpdata['gp_id'] ?>">
                            </td>
                            <td>
                                <p id="total_ach_demon_<?= $gpdata['gp_id'] ?>"></p>

                            </td>
                            <td>
                                <p id="total_fup_<?= $gpdata['gp_id'] ?>"></p>

                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?= form_close(); ?>

                </tbody>

            </table>

        </div>
        <div style="text-align: right;padding: 5px;">
            <button type="submit" id="submit" form="form-finaldata" class="btn btn-primary">Save</button>
        </div>
    </div>
</div>
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
<?php
// Assuming $gpsfinaldata is the array containing GP data
$gpIds = array_map(function ($gpdata) {
    return $gpdata['gp_id'];
}, $gpsfinaldata);
?>
<script>
    var gpIds = <?= json_encode($gpIds) ?>;

    function updateTotalFup(gpId) {
        var fupInputs = document.querySelectorAll('.fup-input[data-gp-id="' + gpId + '"]');
        var sum = 0;

        fupInputs.forEach(function (input) {
            sum += parseFloat(input.value) || 0;
        });

        document.getElementById('total_fup_' + gpId).textContent = sum.toFixed(2);

        // Update the total Achievement under Demonstration for the specific GP
        updateTotals(gpId);
    }

    document.addEventListener('input', function (event) {
        var target = event.target;

        if (target.classList.contains('fup-input')) {
            var gpId = target.dataset.gpId;
            updateTotalFup(gpId);
        }
    });

    // Initialize the event listeners for each GP
    gpIds.forEach(function (gpId) {
        var fupInputs = document.querySelectorAll('.fup-input[data-gp-id="' + gpId + '"]');

        fupInputs.forEach(function (input) {
            input.addEventListener('input', function () {
                updateTotalFup(gpId);
            });
        });
    });
</script>

<script>
    function validateField(field, maxDigits) {
        var inputValue = field.value.trim();
        var decimalRegex = /^\d+(\.\d{1,5})?/; // Regular expression for valid decimal numbers

        if (!decimalRegex.test(inputValue) || inputValue.length > maxDigits) {
            field.setCustomValidity('Please enter a valid positive decimal number with up to 5 decimal places and a maximum of 7 digits.');
        } else {
            field.setCustomValidity('');
        }

        // Call the updateTotals function with the appropriate parameters
        var gpId = field.dataset.gpId;
        updateTotals(gpId);
    }

    function updateTotals(gpId) {
        // Reset totals
        var totalRagiSMI = 0;
        var totalRagiLT = 0;
        var totalRagiLS = 0;
        var totalNonRagiLT = 0;
        var totalNonRagiLS = 0;

        // Iterate through visible number inputs with names containing "crop_data" for the specific GP
        $('[name^="area[' + gpId + '][crop_data]"]:visible').each(function () {
            var cropId = $(this).data('crop-id');
            var inputValue = parseFloat($(this).val()) || 0;

            if (cropId === 1) { // Ragi
                if ($(this).attr('name').indexOf('[smi]') !== -1) {
                    totalRagiSMI += inputValue;
                } else if ($(this).attr('name').indexOf('[lt]') !== -1) {
                    totalRagiLT += inputValue;
                } else if ($(this).attr('name').indexOf('[ls]') !== -1) {
                    totalRagiLS += inputValue;
                }
            } else { // Non-Ragi
                if ($(this).attr('name').indexOf('[lt]') !== -1) {
                    totalNonRagiLT += inputValue;
                } else if ($(this).attr('name').indexOf('[ls]') !== -1) {
                    totalNonRagiLS += inputValue;
                }
            }
        });

        // Sum up the values for Ragi and non-Ragi crops
        var totalRagi = totalRagiSMI + totalRagiLT + totalRagiLS;
        var totalNonRagi = totalNonRagiLT + totalNonRagiLS;

        // Update the total fields for the specific GP
        $('#total_ragi_' + gpId).text(totalRagi.toFixed(2));
        $('#total_non_ragi_' + gpId).text(totalNonRagi.toFixed(2));

        // Calculate and update the total Achievement under Demonstration for the specific GP
        var totalAchDemon = totalRagi + totalNonRagi;
        $('#total_ach_demon_' + gpId).text(totalAchDemon.toFixed(2));

        // Update the total Follow Up Crops field
    }

    // Attach the focusin event handler to reset original values
    $(document).on('focusin', '[name^="area[' + gpId + '][crop_data]"][data-crop-id]', function () {
        $(this).data('original-value', $(this).val());
    });

    // Attach the focusout and change event handlers to relevant visible input fields
    $(document).on('focusout change', '[name^="area[' + gpId + '][crop_data]"]:visible[data-crop-id]', function () {
        var gpId = $(this).data('gp-id');
        updateTotals(gpId);
    });

    // Trigger the initial update when the page loads for each GP
    $(document).ready(function () {
        $('[name^="area["][name$="[crop_data]"][data-crop-id]').each(function () {
            var gpId = $(this).data('gp-id');
            updateTotals(gpId);
        });
    });
</script>