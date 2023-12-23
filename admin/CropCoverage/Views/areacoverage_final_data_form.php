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
                                <option value="<?= $value ?>" <?php if ($value == strtolower($aftcurrent_season)) {
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
    <?php if($block_id){ ?>
    <div class="col-md-2">
    <form class="dm-uploader" id="uploader">
                        <div role="button" class="btn btn-outline btn-warning">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Upload pdf
                                <input type="file" title="Click to add Files">
                        </div>
                        <div class="status"></div>
                    </form>
                    </div>
               <?php  }else{     ?>
                <div class="col-md-2" style="display: none;">
    <form class="dm-uploader" id="uploader">
                        <div role="button" class="btn btn-outline btn-warning">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Upload pdf
                                <input type="file" title="Click to add Files">
                        </div>
                        <?php }  ?>
                    
                    </form>
                    </div>
                    <div id="loading-overlay">
    <div class="progress" style="width: 100%">
        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progress-bar" style="width:0%">
            <span id="progress-percent">0</span>
        </div>
    </div>
</div>

        <div class="tableFixHead">
            <table class="table custom-table " id="final-table">
                <thead>
                    <tr>
                        <th rowspan="3">GP</th>
                        <th rowspan="3">No Of Villages</th>
                        <th colspan="13">Achievement under demonstration(in Ha.)</th>
                        <th colspan="8">Follow Up Crops (with out incentive) (in Ha)
                        </th>
                        <th rowspan="3">Total Achievement under demonstration (in Ha.)</th>
                        <th rowspan="3">Total Follow Up Crops</th>
                    </tr>
                    <tr>
                     <th rowspan="2">Farmer covered</th>
                        <?php foreach ($crop_practices as $crop_id => $practices): ?>
                            <th colspan="<?= count($practices) ?>">
                                <?= $crops[$crop_id] ?>
                            </th>
                        <?php endforeach; ?>
                        <th rowspan="2">Total Ragi</th>
                        <th rowspan="2">Total Non Ragi</th>
                        <th rowspan="2">Farmer covered</th>
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
                            <td>
                                <input type="number" name="area[<?= $gpdata['gp_id'] ?>][no_of_village]"
                                    id="no_of_village_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['no_of_village']; ?>"
                                    oninput="validatePositiveInteger(this,3);" class="sum-input-vlg">
                            </td>
                            <td>
                                <input type=" number"
                                    name="area[<?= $gpdata['gp_id'] ?>][farmers_covered_under_demonstration]"
                                    id="farmers_demonstration_<?= $gpdata['gp_id'] ?>"
                                    value="<?= $gpdata['farmers_covered_under_demonstration']; ?>"
                                    oninput="validatePositiveInteger(this, 3)" class="sum-input-demon dynamic-input">
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
                                                value="<?= $gpcrops[$key] ?>" oninput="validateField(this, 5)"
                                                data-crop-id="<?= $gpcrops['id'] ?>" data-gp-id="<?= $gpdata['gp_id'] ?>">

                                            <input type="number" step="any"
                                                name="area[<?= $gpdata['gp_id'] ?>][crop_data][<?= $gpcrops['id'] ?>][<?= $key ?>]"
                                                value="<?= $gpcrops[$key] ?>" oninput="validateField(this, 5)"
                                                data-crop-id="<?= $gpcrops['id'] ?>" data-gp-id="<?= $gpdata['gp_id'] ?>" class="<?= $key ?>-input">
                                        </td>
                                        <?php
                                    endif;
                                endforeach;
                            endforeach; ?>
                            <td>
                                <p id="total_ragi_<?= $gpdata['gp_id'] ?>" class="sum-input-total-ragi"></p>
                            </td>
                            <td>
                                <p id="total_non_ragi_<?= $gpdata['gp_id'] ?>" class="sum-input-total-non-ragi"></p>
                            </td>
                             <td>
                                <input type="number" name="area[<?= $gpdata['gp_id'] ?>][farmers_covered_under_followup]"
                                    id="farmers_followup_<?= $gpdata['gp_id'] ?>"
                                    value="<?= $gpdata['farmers_covered_under_followup']; ?>"
                                    oninput="validatePositiveInteger(this, 3)" class="sum-input-fup">
                            </td>
                            <td>
                                <input type="number" name="area[<?= $gpdata['gp_id'] ?>][fup_ragi]" class="fup-input sum-input-fup-ragi"
                                    id="fup_ragi_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_ragi']; ?>"
                                    oninput="validateField(this,5)" data-gp-id="<?= $gpdata['gp_id'] ?>">
                            </td>
                            <td>
                            <input type="number" name="area[<?= $gpdata['gp_id']; ?>][fup_lm]" class="fup-input sum-input-fup-lm"
                                    id="fup_lm_<?= $gpdata['gp_id']; ?>" value="<?= $gpdata['fup_lm']; ?>"
                                    oninput="validateField(this,5)" data-gp-id="<?= $gpdata['gp_id']; ?>">
                            </td>
                            <td>
                                <input type="number" name="area[<?= $gpdata['gp_id'] ?>][fup_fm]" class="fup-input sum-input-fup-fm"
                                    id="fup_fm_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_fm']; ?>"
                                    oninput="validateField(this,5)" data-gp-id="<?= $gpdata['gp_id'] ?>">
                            </td>
                            <td>
                                <input type="number" name="area[<?= $gpdata['gp_id'] ?>][fup_sorghum]" class="fup-input sum-input-fup-sorghum"
                                    id="fup_sorghum_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_sorghum']; ?>"
                                    oninput="validateField(this,5)" data-gp-id="<?= $gpdata['gp_id'] ?>">
                            </td>
                            <td>
                                <input type="number" name="area[<?= $gpdata['gp_id'] ?>][fup_km]" class="fup-input sum-input-fup-km"
                                    id="fup_km_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_km']; ?>"
                                    oninput="validateField(this,5)" data-gp-id="<?= $gpdata['gp_id'] ?>">
                            </td>
                            <td>
                                <input type="number" name="area[<?= $gpdata['gp_id'] ?>][fup_bm]" class="fup-input sum-input-fup-bm"
                                    id="fup_bm_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_bm']; ?>"
                                    oninput="validateField(this,5)" data-gp-id="<?= $gpdata['gp_id'] ?>"
                                    >
                            </td>
                            <td>
                                <input type="number" name="area[<?= $gpdata['gp_id'] ?>][fup_pm]" class="fup-input sum-input-fup-pm"
                                    id="fup_pm_<?= $gpdata['gp_id'] ?>" value="<?= $gpdata['fup_pm']; ?>"
                                    oninput="validateField(this,5)" data-gp-id="<?= $gpdata['gp_id'] ?>"
                                    >
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
                    <tr>
                        <td>All Total</td>
                        <td id="Total-Vlg"></td>
                        <td id="Total-Demon-Farmer"></td>
                        <td id="Total-Ragi-Smi"></td>
                        <td id="Total-Ragi-Lt"></td>
                        <td id="Total-Ragi-Ls"></td>
                        <td id="Total-Lm-Lt"></td>
                        <td id="Total-Lm-Ls"></td>
                        <td id="Total-Fm"></td>
                        <td id="Total-Sorghum"></td>
                        <td id="Total-Km"></td>
                        <td id="Total-Bm"></td>
                        <td id="Total-Pm"></td>
                        <td id="All-Total-Ragi"></td>
                        <td id="All-Total-Non-Ragi"></td>
                        <td id="Total-Fup-Farmer"></td>
                        <td id="Total-Fup-Ragi"></td>
                        <td id="Total-Fup-Lm"></td>
                        <td id="Total-Fup-Fm"></td>
                        <td id="Total-Fup-Sorghum"></td>
                        <td id="Total-Fup-Km"></td>
                        <td id="Total-Fup-Bm"></td>
                        <td id="Total-Fup-Pm"></td>
                        <td id="Total-Ach-Demon"></td>
                        <td id="Total-Ach-Fup"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="text-align: right;padding: 5px;">
            <button onclick="validateSave()" type="submit" id="submit" value="submit" form="form-finaldata" class="btn btn-primary">Save</button>
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
        var decimalRegex = /^\d{1,3}(?:\.\d{1,2})?$/;

        if (!decimalRegex.test(inputValue) || inputValue.length > maxDigits) {
            field.setCustomValidity('Please enter a valid positive decimal number with up to 2 decimal places and a maximum of 5 digits.');
        } else {
            field.setCustomValidity('');
        }
        
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

        
        var totalRagi = totalRagiSMI + totalRagiLT + totalRagiLS;
        var totalNonRagi = totalNonRagiLT + totalNonRagiLS;

        
        $('#total_ragi_' + gpId).text(totalRagi.toFixed(2));
        $('#total_non_ragi_' + gpId).text(totalNonRagi.toFixed(2));

        
        var totalAchDemon = totalRagi + totalNonRagi;
        $('#total_ach_demon_' + gpId).text(totalAchDemon.toFixed(2));

    
    }

    // Attach the focusin event handler to reset original values
    // Attach the focusin event handler to reset original values
$(document).on('focusin', '[name^="area["][name$="[crop_data]"][data-crop-id]', function () {
    $(this).data('original-value', $(this).val());
});

// Attach the focusout and change event handlers to relevant visible input fields
$(document).on('focusout change', '[name^="area["][name$="[crop_data]"]:visible[data-crop-id]', function () {
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
    <script>
        // Attach the oninput event handler to relevant input fields
        document.addEventListener('input', function (event) {
            var target = event.target;

            if (target.classList.contains('sum-input-vlg')) {
                updateTotal('sum-input-vlg', 'Total-Vlg');
            }
            if (target.classList.contains('sum-input-demon')) {
                updateTotal('sum-input-demon', 'Total-Demon-Farmer');
            }
            if (target.classList.contains('sum-input-fup')) {
                updateTotal('sum-input-fup', 'Total-Fup-Farmer');
            }
        
            if (target.classList.contains('sum-input-fup-ragi')) {
                updateTotal('sum-input-fup-ragi', 'Total-Fup-Ragi');
            } if (target.classList.contains('sum-input-fup-lm')) {
                updateTotal('sum-input-fup-lm', 'Total-Fup-Lm');
            } if (target.classList.contains('sum-input-fup-fm')) {
                updateTotal('sum-input-fup-fm', 'Total-Fup-Fm');
            } if (target.classList.contains('sum-input-fup-sorghum')) {
                updateTotal('sum-input-fup-sorghum', 'Total-Fup-Sorghum');
            } if (target.classList.contains('sum-input-fup-km')) {
                updateTotal('sum-input-fup-km', 'Total-Fup-Km');
            } if (target.classList.contains('sum-input-fup-bm')) {
                updateTotal('sum-input-fup-bm', 'Total-Fup-Bm');
            } if (target.classList.contains('sum-input-fup-pm')) {
                updateTotal('sum-input-fup-pm', 'Total-Fup-Pm');
            }
        });

        // Trigger the initial update when the page loads
        document.addEventListener('DOMContentLoaded', function () {
            updateTotal('sum-input-vlg', 'Total-Vlg');
            updateTotal('sum-input-demon', 'Total-Demon-Farmer');
            updateTotal('sum-input-fup', 'Total-Fup-Farmer');
        
            updateTotal('sum-input-fup-ragi', 'Total-Fup-Ragi');
            updateTotal('sum-input-fup-lm', 'Total-Fup-Lm');
            updateTotal('sum-input-fup-fm', 'Total-Fup-Fm');
            updateTotal('sum-input-fup-sorghum', 'Total-Fup-Sorghum');
            updateTotal('sum-input-fup-km', 'Total-Fup-Km');
            updateTotal('sum-input-fup-bm', 'Total-Fup-Bm');
            updateTotal('sum-input-fup-pm', 'Total-Fup-Pm');
        });

        function updateTotal(className, resultId) {
            // Get all input elements with the specified class
            var inputElements = document.getElementsByClassName(className);

            var total = 0;
            var maxDecimalPlaces = 0;

            // Iterate through each input element and sum up the values
            for (var i = 0; i < inputElements.length; i++) {
                var inputValue = parseFloat(inputElements[i].value) || 0;
                total += inputValue;

                // Track the maximum decimal places
                var decimalPlaces = (inputElements[i].value.split('.')[1] || []).length;
                maxDecimalPlaces = Math.max(maxDecimalPlaces, decimalPlaces);
            }

            // Update the content of the total td
            document.getElementById(resultId).innerText = total.toFixed(maxDecimalPlaces);
        }
</script>
<script>
    // Trigger the initial update when the page loads
    document.addEventListener('DOMContentLoaded', function () {
        updateAllTotals();

        // Attach oninput event listeners to all relevant input fields
        $('.smi-input, .lt-input, .ls-input').on('input', function () {
            updateAllTotals();
        });
    });

    function updateAllTotals() {
        // Reset totals
        var totalRagiSmi = 0;
        var totalRagiLt = 0;
        var totalRagiLs = 0;
        var totalLmLt = 0;
        var totalLmLs = 0;
        var totalFmLs = 0;
        var totalSorghumLs = 0;
        var totalKmLs = 0;
        var totalBmLs = 0;
        var totalPmLs = 0;


        // Iterate through crops_data for all GPs
        <?php foreach ($gpsfinaldata as $gpdata): ?>
            <?php foreach ($gpdata['crops_data'] as $gpcrops): ?>
                <?php if (isset($gpcrops['smi'])): ?>
                    // Use the class to identify the input field for the specific crop
                    var smiInputValue = parseFloat($('.smi-input[data-crop-id="<?= $gpcrops['id'] ?>"][data-gp-id="<?= $gpdata['gp_id'] ?>"]').val()) || 0;
                    totalRagiSmi += smiInputValue;
                <?php endif; ?>

                <?php if (isset($gpcrops['lt']) && $gpcrops['id'] == 1): ?>
    // Use the class to identify the input field for the specific crop
    var ltInputValue = parseFloat($('.lt-input[data-crop-id="<?= $gpcrops['id'] ?>"][data-gp-id="<?= $gpdata['gp_id'] ?>"]').val()) || 0;
    totalRagiLt += ltInputValue;
<?php endif; ?>
 <?php if (isset($gpcrops['ls']) && $gpcrops['id'] == 1): ?>
    // Use the class to identify the input field for the specific crop
    var lsInputValue = parseFloat($('.ls-input[data-crop-id="<?= $gpcrops['id'] ?>"][data-gp-id="<?= $gpdata['gp_id'] ?>"]').val()) || 0;
    totalRagiLs += lsInputValue;
<?php endif; ?>
<?php if (isset($gpcrops['lt']) && $gpcrops['id'] == 2): ?>
    // Use the class to identify the input field for the specific crop
    var ltInputValue = parseFloat($('.lt-input[data-crop-id="<?= $gpcrops['id'] ?>"][data-gp-id="<?= $gpdata['gp_id'] ?>"]').val()) || 0;
    totalLmLt += ltInputValue;
<?php endif; ?>
<?php if (isset($gpcrops['ls']) && $gpcrops['id'] == 2): ?>
    // Use the class to identify the input field for the specific crop
    var lsInputValue = parseFloat($('.ls-input[data-crop-id="<?= $gpcrops['id'] ?>"][data-gp-id="<?= $gpdata['gp_id'] ?>"]').val()) || 0;
    totalLmLs += lsInputValue;
<?php endif; ?>
<?php if (isset($gpcrops['ls']) && $gpcrops['id'] == 3): ?>
    // Use the class to identify the input field for the specific crop
    var lsInputValue = parseFloat($('.ls-input[data-crop-id="<?= $gpcrops['id'] ?>"][data-gp-id="<?= $gpdata['gp_id'] ?>"]').val()) || 0;
    totalFmLs += lsInputValue;
<?php endif; ?>
<?php if (isset($gpcrops['ls']) && $gpcrops['id'] == 4): ?>
    // Use the class to identify the input field for the specific crop
    var lsInputValue = parseFloat($('.ls-input[data-crop-id="<?= $gpcrops['id'] ?>"][data-gp-id="<?= $gpdata['gp_id'] ?>"]').val()) || 0;
    totalSorghumLs += lsInputValue;
<?php endif; ?>
<?php if (isset($gpcrops['ls']) && $gpcrops['id'] == 5): ?>
    // Use the class to identify the input field for the specific crop
    var lsInputValue = parseFloat($('.ls-input[data-crop-id="<?= $gpcrops['id'] ?>"][data-gp-id="<?= $gpdata['gp_id'] ?>"]').val()) || 0;
    totalKmLs += lsInputValue;
<?php endif; ?>
<?php if (isset($gpcrops['ls']) && $gpcrops['id'] == 6): ?>
    // Use the class to identify the input field for the specific crop
    var lsInputValue = parseFloat($('.ls-input[data-crop-id="<?= $gpcrops['id'] ?>"][data-gp-id="<?= $gpdata['gp_id'] ?>"]').val()) || 0;
    totalBmLs += lsInputValue;
<?php endif; ?>
<?php if (isset($gpcrops['ls']) && $gpcrops['id'] == 7): ?>
    // Use the class to identify the input field for the specific crop
    var lsInputValue = parseFloat($('.ls-input[data-crop-id="<?= $gpcrops['id'] ?>"][data-gp-id="<?= $gpdata['gp_id'] ?>"]').val()) || 0;
    totalPmLs += lsInputValue;
<?php endif; ?>

            <?php endforeach; ?>
        <?php endforeach; ?>
        var totalAllRagi = totalRagiSmi + totalRagiLt + totalRagiLs;
        var totalAllNonRagi = totalLmLt + totalLmLs + totalFmLs + totalSorghumLs + totalKmLs + totalBmLs + totalPmLs;
        var totalAchDemon =  totalAllRagi + totalAllNonRagi;

        // Update the total fields for Ragi-Smi and Ragi-Lt
        $('#Total-Ragi-Smi').text(totalRagiSmi.toFixed(2));
        $('#Total-Ragi-Lt').text(totalRagiLt.toFixed(2));
        $('#Total-Ragi-Ls').text(totalRagiLs.toFixed(2));
        $('#Total-Lm-Lt').text(totalLmLt.toFixed(2));
        $('#Total-Lm-Ls').text(totalLmLs.toFixed(2));
        $('#Total-Fm').text(totalFmLs.toFixed(2));
        $('#Total-Sorghum').text(totalSorghumLs.toFixed(2));
        $('#Total-Km').text(totalKmLs.toFixed(2));
        $('#Total-Bm').text(totalBmLs.toFixed(2));
        $('#Total-Pm').text(totalPmLs.toFixed(2));
        $('#All-Total-Ragi').text(totalAllRagi.toFixed(2));
        $('#All-Total-Non-Ragi').text(totalAllNonRagi.toFixed(2));
        $('#Total-Ach-Demon').text(totalAchDemon.toFixed(2));
    }
</script>
<script>
    // Attach the oninput event handler to relevant input fields
    document.addEventListener('input', function (event) {
        var target = event.target;

        if (target.classList.contains('sum-input-fup-ragi') ||
            target.classList.contains('sum-input-fup-lm') ||
            target.classList.contains('sum-input-fup-fm') ||
            target.classList.contains('sum-input-fup-sorghum') ||
            target.classList.contains('sum-input-fup-km') ||
            target.classList.contains('sum-input-fup-bm') ||
            target.classList.contains('sum-input-fup-pm')) {
            updateAchFupTotal();
        }
    });

    // Trigger the initial update when the page loads
    document.addEventListener('DOMContentLoaded', function () {
        updateAchFupTotal();
    });

    function updateAchFupTotal() {
        var totalFupRagi = parseFloat(document.getElementById('Total-Fup-Ragi').innerText) || 0;
        var totalFupLm = parseFloat(document.getElementById('Total-Fup-Lm').innerText) || 0;
        var totalFupFm = parseFloat(document.getElementById('Total-Fup-Fm').innerText) || 0;
        var totalFupSorghum = parseFloat(document.getElementById('Total-Fup-Sorghum').innerText) || 0;
        var totalFupKm = parseFloat(document.getElementById('Total-Fup-Km').innerText) || 0;
        var totalFupBm = parseFloat(document.getElementById('Total-Fup-Bm').innerText) || 0;
        var totalFupPm = parseFloat(document.getElementById('Total-Fup-Pm').innerText) || 0;

        var totalAchFup = totalFupRagi + totalFupLm + totalFupFm + totalFupSorghum + totalFupKm + totalFupBm + totalFupPm;

        document.getElementById('Total-Ach-Fup').innerText = totalAchFup.toFixed(2);
    }
</script>
<script type="text/javascript">

    $(document).ready(function () {
    function getQueryParameter(name) {
        var urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    }
        $('#uploader').dmUploader({
            dnd: false,
            url: '<?= $upload_url ?>',
            dataType: 'json',
            maxFileSize: 1000000, // 1MB
            multiple: false,
            allowedTypes: 'application/*',
            extFilter: ['pdf'],
            onInit: function () {
                // Plugin is ready to use
                // console.log('initialized');
            },
            onComplete: function () {
                // All files in the queue are processed (success or error)
                $('#upload-controls').loading('stop');
            },
            onNewFile: function (id, file) {
                // When a new file is added using the file selector or the DnD area
                show_status('');
            },
            onBeforeUpload: function (id) {
                // about to start uploading a file
                setProgress(0);
                var block_id = getQueryParameter('block_id');
                  console.log('block_id:', block_id);
                if (typeof (loading) === 'undefined') {
                    loading = $('#upload-controls').loading({
                        overlay: $('#loading-overlay')
                    });
                } else {
                    $('#upload-controls').loading();
                }
                
            },
            
            onUploadCanceled: function (id) {
                // Happens when a file is directly canceled by the user.
            },
            onUploadProgress: function (id, percent) {
                // Updating file progress
                setProgress(percent);
            },
            onUploadSuccess: function (id, data) {
                // A file was successfully uploaded server response
                if (data.status) {
                    show_status('File uploaded successfully', 'text-success');
                    //location.href = data.url;
                } else {
                    show_status(data.message, 'text-danger');
                }
                $('#progress-bar').width(0 + '%');
            },
            onUploadError: function (id, xhr, status, message) {
                console.log(message);
                show_status(message, 'text-danger');
                $('#upload-controls').loading('stop');
            },
            onFileSizeError: function (file) {
                // file.name
                show_status('Invalid file size', 'text-danger');
            },
            onFileExtError: function (file) {
                // file.name
                show_status('Invalid file type', 'text-danger');
            },
            onFileTypeError: function (file) {
                // file.name
                show_status('Invalid file type', 'text-danger');
            }
        });

        function show_status(msg, className) {
            $('.dm-uploader .status').addClass(className).text(msg);
        }

        function setProgress(percent) {
            $('#progress-bar').width(percent + '%');
            $('#progress-percent').text(percent + '%');
        }
    });
</script>



















