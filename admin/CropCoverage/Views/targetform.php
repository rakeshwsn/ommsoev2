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
                <?php if (!$district_id): ?>
                    <div class="col-lg-3">
                        <div class="form-group mg-b-10-force">
                            <label class="form-control-label">Districts: <span class="tx-danger">*</span></label>
                            <?= form_dropdown('district_id', option_array_value($districts, 'id', 'name', array('0' => 'Select Districts')), set_value('district_id'), "id='filter_district' class='form-control js-select2'") ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="col-lg-3">
                    <div class="form-group mg-b-10-force">
                        <label class="form-control-label">Block: <span class="tx-danger">*</span></label>
                        <?= form_dropdown('block_id', option_array_value($blocks, 'id', 'name'), set_value('block_id', isset($_GET['block_id']) ? $_GET['block_id'] : $block_id), "id='filter_block' class='form-control js-select2' disabled"); ?>

                    </div>
                </div>
            </div>
        </div>
        <table id="target_form" class="table table-bordered table-striped table-vcenter">
            <thead>
                <tr>
                    <th>Crop Name</th>
                    <?php foreach ($practices as $practice): ?>
                        <th>
                            <?= $practice['practices']; ?>
                        </th>
                    <?php endforeach; ?>
                    <th>FOLLOW UP CROPS</th>
                    <th>Rice Fallow CROPS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($practicedata as $index => $crop): ?>
                    <tr>
                        <td>
                            <?= $crop['crops']; ?>
                        </td>
                        <td>
                            <input type="number" step=".01" data-practice="1"
                                id="crop_<?= $crop['id']; ?>_practice_<?= $practice['id']; ?>"
                                name="crop[<?= $crop['id'] ?>][smi]" class="crop-input" value="<?= $crop['smi']['value'] ?>"
                                oninput="calculateTotals()" <?= !$crop['smi']['status'] ? 'disabled' : '' ?>>
                        </td>
                        <td>
                            <input type="number" step=".01" data-practice="2"
                                id="crop_<?= $crop['id']; ?>_practice_<?= $practice['id']; ?>"
                                name="crop[<?= $crop['id'] ?>][lt]" class="crop-input" value="<?= $crop['lt']['value'] ?>"
                                oninput="calculateTotals()" <?= !$crop['lt']['status'] ? 'disabled' : '' ?>>

                        </td>
                        <td>
                            <input type="number" step=".01" data-practice="3"
                                id="crop_<?= $crop['id']; ?>_practice_<?= $practice['id']; ?>"
                                name="crop[<?= $crop['id'] ?>][ls]" class="crop-input" value="<?= $crop['ls']['value'] ?>"
                                oninput="calculateTotals()" <?= !$crop['ls']['status'] ? 'disabled' : '' ?>>

                        </td>
                        <td>
                            <input type="number" step=".01" data-practice="4"
                                id="followup_<?= $crop['id']; ?>_practice_<?= $practice['id']; ?>"
                                name="followup[<?= $crop['id'] ?>][followup]" class="crop-input"
                                value="<?= $crop['followup']['value'] ?>" oninput="calculateTotals()">

                        </td>
                        <td>
                            <input type="number" step=".01" data-practice="5"
                                id="rice_fallow_<?= $crop['id']; ?>_practice_<?= $practice['id']; ?>; ?>"
                                name="rice_fallow[<?= $crop['id'] ?>][rice_fallow]" class="crop-input"
                                value="<?= $crop['rice_fallow']['value'] ?>" oninput="calculateTotals()">

                        </td>

                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td>Total</td>
                    <td><input type="number" id="total-smi" class="total-input" readonly></td>
                    <td><input type="number" id="total-lt" class="total-input" readonly></td>
                    <td><input type="number" id="total-ls" class="total-input" readonly></td>
                    <td><input type="number" id="total-followup" class="total-input" readonly></td>
                    <td><input type="number" id="total-rice_fallow" class="total-input" readonly></td>
                </tr>
            </tbody>
        </table>
        <?= form_close(); ?>
    </div>
</div>
<script>
    function validateField(field) {
        var inputValue = field.value.trim();
        var decimalRegex = /^(\d{0,5}(\.\d{0,5})?)?$/;

        if (inputValue !== '' && !decimalRegex.test(inputValue)) {
            field.setCustomValidity('Please enter a valid positive decimal number with up to 5 decimal places.');
        } else {
            field.setCustomValidity('');
        }
    }

    function calculateTotals() {
        var cropInputs = document.getElementsByClassName('crop-input');
        var totalSMI = 0;
        var totalLT = 0;
        var totalLS = 0;
        var totalFOLLOWUP = 0;
        var totalRICEFALLOW = 0;

        for (var i = 0; i < cropInputs.length; i++) {
            validateField(cropInputs[i]); // Validate the input field

            var inputValue = cropInputs[i].value.trim();
            if (inputValue !== '') {
                var practice = cropInputs[i].getAttribute('data-practice');
                var value = parseFloat(inputValue);

                if (practice === '1') {
                    totalSMI += value;
                } else if (practice === '2') {
                    totalLT += value;
                } else if (practice === '3') {
                    totalLS += value;
                }
                else if (practice === '4') {
                    totalFOLLOWUP += value;
                }
                else if (practice === '5') {
                    totalRICEFALLOW += value;

                }
            }
        }

        document.getElementById('total-smi').value = totalSMI.toFixed(2);
        document.getElementById('total-lt').value = totalLT.toFixed(2);
        document.getElementById('total-ls').value = totalLS.toFixed(2);
        document.getElementById('total-followup').value = totalFOLLOWUP.toFixed(2);
        document.getElementById('total-followup').value = totalFOLLOWUP.toFixed(2);
        document.getElementById('total-rice_fallow').value = totalRICEFALLOW.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', calculateTotals);

    // Add event listeners to validate fields on input
    var cropInputs = document.getElementsByClassName('crop-input');
    for (var i = 0; i < cropInputs.length; i++) {
        cropInputs[i].addEventListener('input', function () {
            validateField(this);
            calculateTotals();
        });
    }
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