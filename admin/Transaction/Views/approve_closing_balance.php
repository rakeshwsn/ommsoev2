<!-- Main content -->
<section class="content">

    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Summary</h3>
        </div>
        <div class="block-content">
            <table class="table table-bordered table-vcenter">
                <thead class="thead-light">
                <tr>
                    <th>Year</th>
                    <th>Month</th>
                    <th>Agency Type</th>
                    <th>Opening (+)</th>
                    <th>Fund Receipt (+)</th>
                    <th>Misc Transaction (+)</th>
                    <th>Expense (-)</th>
                    <th>Closing</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?=$summary['year']?></td>
                    <td><?=$summary['month']?></td>
                    <td><?=$summary['agency_type']?></td>
                    <td><?=$summary['ob']?></td>
                    <td><?=$summary['fr']?></td>
                    <td><?=$summary['mt']?></td>
                    <td><?=$summary['exp']?></td>
                    <td><?=$summary['bal']?></td>
                    <?php if(isset($approval) && $approval): ?>
                        <td><button class="btn btn-primary" id="btn-action"><?=$status?></button> </td>
                    <?php else: ?>
                        <td><?=$status?></td>
                    <?php endif; ?>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Closing Balance Breakup</h3>
        </div>
        <div class="block-content">
            <form method="post">
            <table id="closing-balance-breakup" class="table table-bordered table-vcenter">
                <thead class="thead-light">
                <tr>
                    <th style="width:300px;">Particulars</th>
                    <th style="width:300px;">Amount</th>
                    <th>Attachment</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Advance</td>
                    <td><input class="form-control amount" value="<?=$advance?>" name="advance"></td>
                    <td class="dm-uploader">
                        <?php if($advance_file_url) { ?>
                            <?=$advance_file_url?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>Bank (including bank interest)</td>
                    <td><input class="form-control amount" name="bank" value="<?=$bank?>"></td>
                    <td>
                        <?php if($bank_file_url) { ?>
                            <?=$bank_file_url?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>Cash</td>
                    <td><input class="form-control amount" name="cash" value="<?=$cash?>"></td>
                    <td class="dm-uploader">
                        <?php if($cash_file_url) { ?>
                            <?=$cash_file_url?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td><input class="form-control" id="total" disabled></td>
                    <td>
                        <?php if(isset($correction)) { ?>
                            <button id="btn-submit" class="btn btn-sm btn-primary"><i class="si si-paper-plane"></i> Submit</button>
                        <?php } ?>
                    </td>
                </tr>
                </tbody>
            </table>
            </form>
        </div>
    </div>

</section>
<!-- content -->

<?php js_start(); ?>
<script>
    var cb = parseFloat(<?=$summary['bal']?>);

    function toggleSubmit() {
        _total = $('#total').val();
        $('#btn-submit').attr('disabled',cb!=_total);
    }

    $(function () {
        calcTotal();
        toggleSubmit();
        $('.amount').on('keyup',function (e) {
            if (/^-?\d*\.?\d{0,6}$/.test(this.value)==false) {
                $(this).val(this.value.replace(e.key,''));
            } else {
                calcTotal();
                toggleSubmit();
            }
        });
    });

    var total;
    function calcTotal(){
        total = 0;
        $('.amount').each(function (k,v) {
            val = parseFloat($(this).val()) || 0;
            total += val
        });
        $('#total').val(total.toFixed(2));
    }
</script>
<?php if(isset($approval)) {
    echo $approve_form;
} ?>

<?php js_end(); ?>
