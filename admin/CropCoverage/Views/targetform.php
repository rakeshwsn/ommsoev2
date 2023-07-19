<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title">Add Area Coverage Target</h3>
        <div class="block-options">
            <button type="submit" form="form-target" class="btn btn-primary">Save</button>
            <a href="<?= $cancel; ?>" data-toggle="tooltip" title="Cancel" class="btn btn-primary">Cancel</a>
        </div>
    </div>
    <div class="block-content block-content-full">
        <?= form_open('', 'id="form-target"'); ?>
        <div class="form-layout">
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group mg-b-10-force">
                        <label class="form-control-label">Block: </label>
                        <?= form_dropdown('block_id', option_array_value($blocks, 'id', 'name'), set_value('block_id', isset($_GET['block_id']) ? $_GET['block_id'] : $block_id), "id='filter_block' class='form-control js-select2' disabled"); ?>
                    </div>
                </div>

                <div class="col-lg-2" style="padding-top: 30px;">
                    <div class="form-group mg-b-10-force">
                        <label class="form-control-label">Year:
                            <?= $year_id; ?>
                        </label>

                    </div>
                </div>
                <div class="col-lg-2" style="padding-top: 30px;">
                    <div class="form-group mg-b-10-force">
                        <label class="form-control-label">Season:
                            <?= $season ?>
                        </label>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <table id="datatable" class="table table-bordered table-striped table-vcenter">
        <thead>
            <tr>
                <th>Crop Name</th>
                <?php foreach ($practices as $practice): ?>
                    <th>
                        <?= $practice['practices']; ?>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($crops as $index => $crop): ?>
                <tr>
                    <td>
                        <?= $crop['crops']; ?>
                    </td>
                    <?php foreach ($practices as $practice): ?>
                        <?php
                        $inputValue = '';
                        $isLTDisabled = true; // Initialize with default value (disabled).
                        if ($practice['practices'] === 'SMI') {
                            if ($crop['crops'] === 'Ragi') {
                                $inputValue = ($index === 0 && isset($practicedata) && $practicedata) ? $practicedata[$crop['id']][strtolower($practice['practices'])] : '';
                            } else {
                                $inputValue = '';
                            }
                        } elseif ($practice['practices'] === 'lt') {
                            if (in_array($crop['crops'], ['Ragi', 'Little Millet'])) {
                                $inputValue = (isset($practicedata) && $practicedata) ? $practicedata[$crop['id']][strtolower($practice['practices'])] : '';
                                $isLTDisabled = false; // Enable the input field for 'lt' practice.
                            } else {
                                $inputValue = '';
                            }
                        } else {
                            $inputValue = '';
                        }
                        ?>
                        <td>
                            <?php if ($practice['practices'] === 'SMI'): ?>
                                <?php if ($crop['crops'] === 'Ragi'): ?>
                                    <input type="number" id="crop_<?= $crop['id']; ?>_practice_<?= $practice['id']; ?>"
                                        name="crop[<?= $crop['id'] ?>][<?= $practice['practices'] ?>]" class="crop-input"
                                        value="<?= $inputValue ?>" oninput="calculateTotals()">
                                <?php else: ?>
                                    <!-- <input type="number" disabled> -->
                                <?php endif; ?>
                            <?php elseif ($practice['practices'] === 'lt'): ?>
                                <input type="number" id="crop_<?= $crop['id']; ?>_practice_<?= $practice['id']; ?>"
                                    name="crop[<?= $crop['id'] ?>][<?= $practice['practices'] ?>]" class="crop-input"
                                    value="<?= $inputValue ?>" <?= $isLTDisabled ? 'disabled' : '' ?> oninput="calculateTotals()">
                            <?php else: ?>
                                <input type="number" id="crop_<?= $crop['id']; ?>_practice_<?= $practice['id']; ?>"
                                    name="crop[<?= $crop['id'] ?>][<?= $practice['practices'] ?>]" class="crop-input"
                                    value="<?= $inputValue ?>" oninput="calculateTotals()">
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td>Total</td>
                <td><input type="number" id="total-smi" class="total-input" readonly></td>
                <td><input type="number" id="total-lt" class="total-input" readonly></td>
                <td><input type="number" id="total-ls" class="total-input" readonly></td>
            </tr>
        </tbody>

    </table>




    <?= form_close(); ?>
</div>
</div>
<script>
    function calculateTotals() {
        var cropInputs = document.getElementsByClassName('crop-input');
        var totalInputs = document.getElementsByClassName('total-input');
        var totalSMI = 0;
        var totalLT = 0;
        var totalLS = 0;

        for (var i = 0; i < cropInputs.length; i++) {
            var inputValue = parseFloat(cropInputs[i].value);

            if (!isNaN(inputValue)) {
                var inputId = cropInputs[i].id;
                var practice = inputId.split('_')[3];

                if (practice === '1') {
                    totalSMI += inputValue;
                } else if (practice === '2') {
                    totalLT += inputValue;
                } else if (practice === '3') {
                    totalLS += inputValue;
                }
            }
        }

        document.getElementById('total-smi').value = totalSMI;
        document.getElementById('total-lt').value = totalLT;
        document.getElementById('total-ls').value = totalLS;
    }
    document.addEventListener('DOMContentLoaded', calculateTotals);
</script>
<script>
    $(document).ready(function () {
        $('#district').change(function () {
            var districtId = $(this).val();

            // Clear block dropdown
            $('#block').html('');

            // Make AJAX request to fetch blocks
            $.ajax({
                url: '<?= admin_url("/areacoverage/fetch-blocks"); ?>/', // Replace with the URL for fetching blocks
                method: 'POST',
                data: { districtId: districtId },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Populate block dropdown with options
                        var blocks = response.blocks;
                        $.each(blocks, function (key, block) {
                            $('#block').append('<option value="' + block.id + '">' + block.name + '</option>');
                        });
                    } else {
                        // Handle error case
                        console.error(response.message);
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    // Handle error case
                    console.error(textStatus + ': ' + errorThrown);
                }
            });
        });
    });
</script>