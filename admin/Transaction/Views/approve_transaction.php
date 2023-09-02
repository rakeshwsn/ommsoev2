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
                    <th>Agency Type</th>
                    <th>Month/Year</th>
                    <th>Funding</th>
                    <th>Date Added</th>
                    <th>Txn Type</th>
                    <th>Phy</th>
                    <th>Fin</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?=$district?></td>
                    <td><?=$block?></td>
                    <td><?=$agency_type?></td>
                    <td><?=$month?> / <?=$year?></td>
                    <td><?=$fund_agency?></td>
                    <td><?=$date_added?></td>
                    <td><?=$txn_type_text?></td>
                    <td><?=$phy?></td>
                    <td><?=$fin?></td>
                    <?php if(isset($approval) && $approval): ?>
                        <td><button class="btn btn-primary" id="btn-action"><?=$status?></button> </td>
                    <?php else: ?>
                        <td><?=$status?></td>
                    <?php endif; ?>
                </tr>
                <?php if(!empty($remarks)): ?>
                <tr>
                    <td>Remarks:</td>
                    <td colspan="7"><?=$remarks?></td>
                </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">Transaction Details</h3>
        </div>
        <div class="block-content block-content-full">
            <?php if($show_form) { echo form_open();} ?>
            <div class="tableFixHead">
                <table class="table custom-table " id="txn-table">
                    <thead>
                    <tr>
                        <th rowspan="2" width="10%">Sl no</th>
                        <th rowspan="2" width="30%">Component</th>
                        <th colspan="2" width="15%">Fund Available Up to Date</th>
                        <th colspan="2" width="15%"><?=$txn_type_text?> up to prev month</th>
                        <th colspan="2" width="15%"><?=$txn_type_text?> during the month</th>
                        <th colspan="2" width="15%">Cumulative <?=$txn_type_text?> till month</th>
                    </tr>
                    <tr>
                        <th>Phy</th>
                        <th>Fin</th>
                        <th>Phy</th>
                        <th>Fin</th>
                        <th>Phy</th>
                        <th>Fin</th>
                        <th>Phy</th>
                        <th>Fin</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?=$components?>
                    </tbody>
                </table>
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

<?php js_start(); ?>
<script>
    $(function () {

        $('.physical').keyup(function (e) {
            var ctx = $(this);
            parent = $(ctx).closest('tr');
            parent_id = $(ctx).closest('tr').data('parent');

            //grand total
            gt_mon_phy = 0;
            gt_cum_phy = 0;
            $('.mon_phy').each(function () {
                mon_phy = parseInt($(this).find('input').val()) || 0;
                gt_mon_phy += mon_phy;

                upto_phy = parseInt($(this).closest('tr').find('.upto_phy').text()) || 0;
                gt_cum_phy += (upto_phy+mon_phy)
            });
            $('#gt_mon_phy').text(gt_mon_phy);
            $('#gt_cum_phy').text(gt_cum_phy);

            //sub total
            sub_mon_phy = 0;
            sub_cum_phy = 0;
            $('tr[data-parent="'+parent_id+'"]').each(function () {
                mon_phy = parseInt($(this).find('.mon_phy').find('input').val()) || 0;
                sub_mon_phy += mon_phy;

                upto_phy = parseInt($(this).closest('tr').find('.upto_phy').text()) || 0;
                sub_cum_phy += (upto_phy+mon_phy);
            });
            $('tr[data-parent="'+parent_id+'"].subtotal').find('.sub_mon_phy').text(sub_mon_phy);
            $('tr[data-parent="'+parent_id+'"].subtotal').find('.sub_cum_phy').text(sub_cum_phy);

            //update cum_phy of the row
            upto_phy = parseInt($(parent).find('.upto_phy').text()) || 0;
            mon_phy = parseInt($(this).find('input').val()) || 0;
            cum_phy = upto_phy+mon_phy;

            $(parent).find('.cum_phy').text(cum_phy);
        });

        $('.financial').keyup(function (e) {
            var ctx = $(this);
            parent = $(ctx).closest('tr');
            parent_id = $(ctx).closest('tr').data('parent');

            //grand total
            gt_mon_fin = 0;
            gt_cum_fin = 0;
            $('.mon_fin').each(function () {
                mon_fin = parseFloat($(this).find('input').val()) || 0;
                gt_mon_fin += mon_fin;

                upto_fin = parseFloat($(this).closest('tr').find('.upto_fin').text()) || 0;
                gt_cum_fin += upto_fin+mon_fin
            });
            $('#gt_mon_fin').text(gt_mon_fin);
            $('#gt_cum_fin').text(gt_cum_fin);

            //sub total
            sub_mon_fin = 0;
            sub_cum_fin = 0;
            $('tr[data-parent="'+parent_id+'"]').each(function () {
                mon_fin = parseFloat($(this).find('.mon_fin').find('input').val()) || 0;
                sub_mon_fin += mon_fin;

                upto_fin = parseFloat($(this).closest('tr').find('.upto_fin').text()) || 0;
                sub_cum_fin += upto_fin+mon_fin;
            });
            $('tr[data-parent="'+parent_id+'"].subtotal').find('.sub_mon_fin').text(sub_mon_fin);
            $('tr[data-parent="'+parent_id+'"].subtotal').find('.sub_cum_fin').text(sub_cum_fin);

            //update cum_fin of the row
            upto_fin = parseFloat($(parent).find('.upto_fin').text()) || 0;
            mon_fin = parseFloat($(this).val()) || 0;
            cum_fin = upto_fin+mon_fin;

            $(parent).find('.cum_fin').text(cum_fin);
        });

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
            // Prevent the input if the key is not a number or decimal point
            if (keyCode !== 46 && (keyCode < 48 || keyCode > 57)) {
                event.preventDefault();
            }

            // Allow only one decimal point
            // Prevent the input if there is already a decimal point
            if (keyCode === 46 && $(this).val().indexOf('.') !== -1) {
                event.preventDefault();
            }

            // Disallow comma (,)
            // Prevent the input if the key is a comma
            if (keyCode === 44) {
                event.preventDefault();
            }
        });
    }

</script>

<?php if(isset($approval)) {
    echo $approve_form;
} ?>

<?php js_end(); ?>
