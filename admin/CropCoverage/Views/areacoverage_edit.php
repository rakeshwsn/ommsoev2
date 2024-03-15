<style>
    .w-50p{width:50px;}
</style>
<!-- Main content -->
<section class="content">

    <div class="block block-themed">
        <div class="block-header bg-info">
            <h3 class="block-title">Summary</h3>
        </div>
        <div class="block-content block-content-full">
            <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                <tr>
                    <th>District</th>
                    <th>Block</th>
                    <th>GP</th>
                    <th>Year</th>
                    <th>Season</th>
                    <th>Date Added</th>
                    <th>Week Start</th>
                    <th>Week End</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?=$district?></td>
                    <td><?=$block?></td>
                    <td><?=$gp?></td>
                    <td><?=$year?></td>
                    <td><?=$season?></td>
                    <td><?=$date_added?></td>
                    <td><?=$start_date?></td>
                    <td><?=$end_date?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">Area Coverage Details</h3>
        </div>
        <div class="block-content block-content-full">
            <?php if($show_form) { echo form_open();} ?>
            <div class="tableFixHead1">
                <table class="table custom-table " id="basic-table">
                    <tbody>
                        <tr>
                            <td>Farmers Covered</td>
                            <td><input type="text" name="crop_coverage[farmers_covered]" value="<?= $crop_coverage['farmers_covered'] ?>" class="form-control physical"></td>
                        </tr>
                    </tbody>
                </table>
                <table class="table custom-table " id="basic-table">
                    <tbody>
                        <tr>
                            <td>Nursery</td>
                            <td><input type="text" name="nursery[nursery_raised]" value="
                            <?= $nursery_info['nursery_raised'] ?? ''; ?>" class="form-control financial"></td>
                        </tr>
                        <tr>
                            <td>Balance SMI</td>
                            <td><input type="text" name="nursery[balance_smi]" value="<?= $nursery_info['balance_smi'] ?? ''; ?>" class="form-control financial"></td>
                        </tr>
                        <tr>
                            <td>Balance LT</td>
                            <td><input type="text" name="nursery[balance_lt]" value="<?= $nursery_info['balance_lt'] ?? ''; ?>" class="form-control financial"></td>
                        </tr>
                    </tbody>
                </table>
                <table class="table custom-table " id="crop-table">
                    <thead>
                    <tr>
                        <th rowspan="2">Crop</th>
                        <th colspan="3">Practice</th>
                    </tr>
                    <tr>
                        <th>SMI</th>
                        <th>LT</th>
                        <th>LS</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($crops as $crop): ?>
                            <tr>
                                <td><?= $crop['crop'] ?></td>
                                <?php foreach ($crop['practices'] as $practice_id => $practice): ?>
                                    <td><input type="text" class="form-control financial"
                                            <?php if(!$practice['status']){echo 'disabled';}?>
                                                name="area[<?= $crop['crop_id']?>][<?= $practice_id ?>]"
                                               value="<?= $practice['area']?>"></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <table class="table custom-table " id="fuc-table">
                    <thead>
                    <tr>
                        <th>Follow Up Crop</th>
                        <th>Area</th>
                    </tr>
                    </thead>
                    <tbody>
                
                    <?php foreach ($fups as $fup) { ?>
                    <tr>
                        <td>
                            <?php echo $fup['crop'] ?>
                        </td>
                        <td>
                            <input type="text" class="form-control financial" name="fup[<?=$fup['crop_id']?>]" value="<?php echo $fup['area'] ?>">
                        </td>
                    </tr>
                    <?php } ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td>Total</td>
                        <td id="total-fup"><?=$fups_total?></td>
                    </tr>
                    </tfoot>
                </table>
                <table class="table custom-table " id="diversification-table">
                    <div class="block-header bg-dark">
                        <h3 class="block-title text-white">Crop Diversification Details</h3>
                    </div>
                    <tbody>
                        <tr>
                            <td>
                                Total Crop Diversification Ragi
                            </td>
                            <td>
                                <input type="text" name="crop_div[crop_div_ragi]" value="<?= $crop_coverage['crop_div_ragi'] ?>" class="form-control financial">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Total Crop Diversification Non Ragi
                            </td>
                            <td>
                                 <input type="text" name="crop_div[crop_div_non_ragi]" value="<?= $crop_coverage['crop_div_non_ragi'] ?>" class="form-control financial">
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td>Total</td>
                        <td id="total-crop-diversification">0</td>
                    </tr>
                    </tfoot>
                </table>
                <?php if ($season === 'Rabi') { ?>
                <table class="table custom-table " id="fuc-table">
                    <thead>
                    <tr>
                        <th>Rice Fallow Crops</th>
                        <th>Area</th>
                    </tr>
                    </thead>
                    <tbody>
                
                    <?php foreach ($ricefallows as $ricefallow) { ?>
                    <tr>
                        <td>
                            <?php echo $ricefallow['crop'] ?>
                        </td>
                        <td>
                            <input type="text" class="form-control financial" name="ricefallow[<?=$ricefallow['crop_id']?>]" value="<?= $ricefallow['area']; ?>">
                        </td>
                    </tr>
                    <?php } ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td>Total</td>
                        <td id="total-rfc"><?= $rfc_total ?></td>
                    </tr>
                    </tfoot>
                </table>
                 <?php } ?>
            </div>
            <?php if($show_form): ?>
            <div class="row">
                <div class="col mt-4">
                    <button type="submit" class="btn btn-alt-primary float-right">Submit</button>
                    </div>
                </div>
            <?php echo form_close(); ?>
        <?php endif; ?>
            </div>
    </div>
</section>

<script>
    $(function () {

        numOnly();
        decimalOnly();

        $('input').on('input',function (e) {
            var maxVal = parseFloat($(this).attr('max'));

            // Get the input value as a number
            var val = parseFloat($(this).val());

            // Check if the input value is greater than the maximum allowed value
            if (!isNaN(val) && val > maxVal) {
                // Set the input value to the maximum allowed value
                $(this).val(maxVal);
            }
        });

        $.each(<?=json_encode($practices)?>,function (i,v) {
            $('[name$="['+v+']').keyup(function (e) {
                var tot=0;
                $('[name$="['+v+']"]').each(function (i,element) {
                    if($(element).is(':disabled')){
                        return false;
                    }
                    tot += parseFloat($(element).val() || 0);
                });
                $('[name$="area[0]['+v+']"]').val(tot);
            });
        });

        $('[name^="fup"]').keyup(function (e) {
            var tot=0;
            $('[name^="fup"]').each(function (i,element) {
                tot += parseFloat($(element).val() || 0);
            });
            $('#total-fup').text(tot);
        });
        $('[name^="ricefallow"]').keyup(function (e) {
            var tot=0;
            $('[name^="ricefallow"]').each(function (i,element) {
                tot += parseFloat($(element).val() || 0);
            });
            $('#total-rfc').text(tot);
        });
    });
    //rakesh
    function numOnly() {
        //input type text to number
        // Get the input field
        var input = $('.physical');

        // Attach keypress event handler
        input.keypress(function(event) {
            // Get the key code of the pressed key
            var keyCode = event.which;

            // Check if the key is a number
            if (keyCode < 48 || keyCode > 57) {
                // Prevent the input if the key is not a number
                event.preventDefault();
            }
        });
    }
    function decimalOnly() {
        // Get the input field
        var input = $('.financial');

        $('.financial').on('keypress',function (e) {
            // Get the key code of the pressed key
            var keyCode = event.which;

            // Allow decimal point (.) and numbers (48-57) only
            if (keyCode !== 46 && (keyCode < 48 || keyCode > 57)) {
                // Prevent the input if the key is not a number or decimal point
                event.preventDefault();
            }

            // Allow only one decimal point
            if (keyCode === 46 && $(this).val().indexOf('.') !== -1) {
                // Prevent the input if there is already a decimal point
                event.preventDefault();
            }
            // Disallow comma (,)
            if (keyCode === 44) {
                // Prevent the input if the key is a comma
                event.preventDefault();
            }
        });
    }
    //disable inputs
    <?php if(!$show_form){ ?>
    $(function () {
        $('input').each(function (i,v) {
            $(this).attr('disabled',true);
        });
    });
    <?php } ?>
</script>
<script>
    $('[name^="crop_div"]').keyup(function () {
        var total = 0;

        $('[name^="crop_div"]').each(function (i, element) {
            total += parseFloat($(element).val() || 0);
        });

        $('#total-crop-diversification').text(total.toFixed(2));
    });
</script>

