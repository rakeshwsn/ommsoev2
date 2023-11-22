<div class="block block-themed">
    <form>
        <div class="block">
            <div class="block-header block-header-default bg-success">
                <h3 class="block-title">
                    <?= $heading_title; ?>
                </h3>
                <div class="block-options">

                    <?php if ($permission_add == 7): ?>
                        <a href="<?= $add; ?>" data-toggle="tooltip" title="add"
                            class="btn btn-primary ajaxaction">Add/Edit</a>
                    <?php endif; ?>


                </div>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-md-2">
                        <label>Year</label>
                        <select class="form-control" id="year_id" name="year_id">
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
                        <select class="form-control" id="season" name="season">
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
                        <button id="btn-filter" class="btn btn-outline btn-primary">
                            <i class="fa fa-filter"></i> Filter</button>

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
                                    <?= $blockfd->gp_name ?>
                                </td>
                            <?php } else if ($district_id) { ?>
                                    <td>
                                    <?= $blockfd->block ?>
                                    </td>
                                    <td class="total_gp">
                                    <?= $blockfd->total_gp ?>
                                    </td>
                            <?php } else if (isset($allblocks)) { ?>
                                        <td>
                                    <?= $block['district'] ?>
                                        </td>
                                        <td>
                                    <?= $block['block'] ?>
                                        </td>
                                        <td class="total_gp">
                                    <?= $block['gps'] ?>
                                        </td>
                            <?php } else { ?>
                                        <td>
                                    <?= $blockfd->district ?>
                                        </td>
                                        <td>
                                    <?= $blockfd->blocks ?>
                                        </td>
                                        <td class="total_gp">
                                    <?= $blockfd->gps ?>
                                        </td>
                            <?php } ?>
                            <td class="total_village">
                                <?= $blockfd->total_village ?>
                            </td>
                            <td class="total_demon_farmer">
                                <?= $blockfd->total_demon_farmer ?>
                            </td>
                            <td class="total_follow_farmer">
                                <?= $blockfd->total_follow_farmer ?>
                            </td>
                            <td class="ragi_total_smi">
                                <?= $blockfd->ragi_total_smi ?>
                            </td>
                            <td class="ragi_total_lt">
                                <?= $blockfd->ragi_total_lt ?>
                            </td>
                            <td class="ragi_total_ls">
                                <?= $blockfd->ragi_ls ?>
                            </td>
                            <td class="little_millet_lt">
                                <?= $blockfd->little_millet_lt ?>
                            </td>
                            <td class="little_millet_ls">
                                <?= $blockfd->little_millet_ls ?>
                            </td>
                            <td class="foxtail_millet_ls">
                                <?= $blockfd->foxtail_millet_ls ?>
                            </td>
                            <td class="sorghum_ls">
                                <?= $blockfd->sorghum_ls ?>
                            </td>
                            <td class="kodo_millet_ls">
                                <?= $blockfd->kodo_millet_ls ?>
                            </td>
                            <td class="barnyard_millet_ls">
                                <?= $blockfd->barnyard_millet_ls ?>
                            </td>
                            <td class="pearl_millet_ls">
                                <?= $blockfd->pearl_millet_ls ?>
                            </td>
                            <td class="ragi-all-total">
                                <?= $blockfd->ragi_total_smi + $blockfd->ragi_total_lt + $blockfd->ragi_ls ?>
                            </td>
                            <td class="non-ragi-all-total">
                                <?= $blockfd->little_millet_lt + $blockfd->little_millet_ls + $blockfd->foxtail_millet_ls + $blockfd->sorghum_ls + $blockfd->kodo_millet_ls + $blockfd->barnyard_millet_ls + $blockfd->pearl_millet_ls ?>
                            </td>
                            <td class="all-total-fup">
                                <?= $blockfd->total_fup ?>
                            </td>
                            <td class="all-total-area">
                                <?= $blockfd->ragi_total_smi + $blockfd->ragi_total_lt + $blockfd->ragi_ls + $blockfd->little_millet_lt + $blockfd->little_millet_ls + $blockfd->foxtail_millet_ls + $blockfd->sorghum_ls + $blockfd->kodo_millet_ls + $blockfd->barnyard_millet_ls + $blockfd->pearl_millet_ls + $blockfd->total_fup ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td>All Total</td>
                        <?php if (!$district_id && !$block_id) { ?>
                            <td id="allTotalBlock"></td>
                        <?php }
                        if (!$block_id) { ?>
                            <td id="allTotalGp"></td>

                        <?php } ?>
                        <td id="allTotalVlg"></td>
                        <td id="allTotalDemonFar"></td>
                        <td id="allTotalFolFar"></td>
                        <td id="allTotalRagiSMI"></td>
                        <td id="allTotalRagiLT"></td>
                        <td id="allTotalRagiLs"></td>
                        <td id="allTotalLittleMilletLt"></td>
                        <td id="allTotalLittleMilletLs"></td>
                        <td id="allTotalFoxtailMilletLs"></td>
                        <td id="allTotalSorghumLs"></td>
                        <td id="allTotalKodoMilletLs"></td>
                        <td id="allTotalBarnyardMilletLs"></td>
                        <td id="allTotalPearlMilletLs"></td>
                        <td id="allTotalRagi"></td>
                        <td id="allTotalNonRagi"></td>
                        <td id="allTotalFup"></td>
                        <td id="allTotalArea"></td>

                    </tr>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to calculate the sum for a given class and assign it to the specified total element
        function calculateAndAssignSum(className, totalId) {
            // Get all elements with the specified class
            var elements = document.querySelectorAll('.' + className);

            // Initialize sum
            var sum = 0;

            // Loop through each element and add its value to the sum
            elements.forEach(function (element) {
                sum += parseFloat(element.textContent) || 0;
            });

            // Assign the sum to the specified total element
            var totalElement = document.getElementById(totalId);
            if (totalElement) {
                totalElement.textContent = sum.toFixed(); // Adjust the precision as needed
            }
        }

        // Call the function for the 'total_gp' class
        calculateAndAssignSum('total_gp', 'allTotalGp');
        calculateAndAssignSum('blocks', 'allTotalBlock');
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get all elements with the specified classes
        var totalVillageElements = document.querySelectorAll('.total_village');
        var totalDemonFarmerElements = document.querySelectorAll('.total_demon_farmer');
        var totalFollowFarmerElements = document.querySelectorAll('.total_follow_farmer');

        // Initialize sums
        var totalVillageSum = 0;
        var totalDemonFarmerSum = 0;
        var totalFollowFarmerSum = 0;

        // Loop through each element and add its value to the sum
        function calculateSum(elements, sum) {
            elements.forEach(function (element) {
                sum += parseFloat(element.textContent) || 0;
            });
            return sum;
        }

        // Calculate sums for each field
        totalVillageSum = calculateSum(totalVillageElements, totalVillageSum);
        totalDemonFarmerSum = calculateSum(totalDemonFarmerElements, totalDemonFarmerSum);
        totalFollowFarmerSum = calculateSum(totalFollowFarmerElements, totalFollowFarmerSum);

        // Assign the sums to the respective elements
        var allTotalVlgElement = document.getElementById('allTotalVlg');
        var allTotalDemonFarElement = document.getElementById('allTotalDemonFar');
        var allTotalFolFarElement = document.getElementById('allTotalFolFar');

        if (allTotalVlgElement) {
            allTotalVlgElement.textContent = totalVillageSum.toFixed(2); // Adjust the precision as needed
        }
        if (allTotalDemonFarElement) {
            allTotalDemonFarElement.textContent = totalDemonFarmerSum.toFixed(2);
        }
        if (allTotalFolFarElement) {
            allTotalFolFarElement.textContent = totalFollowFarmerSum.toFixed(2);
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to calculate the sum for a given class and assign it to the specified total element
        function calculateAndAssignSum(className, totalId) {
            // Get all elements with the specified class
            var elements = document.querySelectorAll('.' + className);

            // Initialize sum
            var sum = 0;

            // Loop through each element and add its value to the sum
            elements.forEach(function (element) {
                sum += parseFloat(element.textContent) || 0;
            });

            // Assign the sum to the specified total element
            var totalElement = document.getElementById(totalId);
            if (totalElement) {
                totalElement.textContent = sum.toFixed(2); // Adjust the precision as needed
            }
        }

        // Call the function for each specific class
        calculateAndAssignSum('ragi_total_smi', 'allTotalRagiSMI');
        calculateAndAssignSum('ragi_total_lt', 'allTotalRagiLT');
        calculateAndAssignSum('ragi_ls', 'allTotalRagiLs');
        calculateAndAssignSum('little_millet_lt', 'allTotalLittleMilletLt');
        calculateAndAssignSum('little_millet_ls', 'allTotalLittleMilletLs');
        calculateAndAssignSum('foxtail_millet_ls', 'allTotalFoxtailMilletLs');
        calculateAndAssignSum('sorghum_ls', 'allTotalSorghumLs');
        calculateAndAssignSum('kodo_millet_ls', 'allTotalKodoMilletLs');
        calculateAndAssignSum('barnyard_millet_ls', 'allTotalBarnyardMilletLs');
        calculateAndAssignSum('pearl_millet_ls', 'allTotalPearlMilletLs');

        // Calculate and assign the sum for the specified fields
        var totalRagiSum = parseFloat(document.querySelector('.ragi_total_smi').textContent) || 0 +
            parseFloat(document.querySelector('.ragi_total_lt').textContent) || 0 +
            parseFloat(document.querySelector('.ragi_ls').textContent) || 0;

        document.getElementById('allTotalRagi').textContent = totalRagiSum.toFixed(2); // Adjust the precision as needed
    });
</script>
<!-- JavaScript code -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to calculate the sum for a given class and assign it to the specified total element
        function calculateAndAssignSum(className, totalId) {
            // Get all elements with the specified class
            var elements = document.querySelectorAll('.' + className);

            // Initialize sum
            var sum = 0;

            // Loop through each element and add its value to the sum
            elements.forEach(function (element) {
                sum += parseFloat(element.textContent) || 0;
            });

            // Assign the sum to the specified total element
            var totalElement = document.getElementById(totalId);
            if (totalElement) {
                totalElement.textContent = sum.toFixed(2); // Adjust the precision as needed
            }
        }

        // Call the function for the specific class and assign to 'allTotalRagi'
        calculateAndAssignSum('ragi-all-total', 'allTotalRagi');
<<<<<<< HEAD

=======
        calculateAndAssignSum('non-ragi-all-total', 'allTotalNonRagi');
>>>>>>> test
        calculateAndAssignSum('all-total-fup', 'allTotalFup');
        calculateAndAssignSum('all-total-area', 'allTotalArea');

    });
</script>