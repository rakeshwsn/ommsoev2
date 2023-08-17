<style>
    .block-content {
        width: 100%;
    }

    .table-container {
        overflow-y: scroll;
    }

    .table-header {
        display: flex;
        background-color: #f2f2f2;
        font-weight: bold;
        padding: 10px;
    }

    .header-cell {
        flex: 1;
        padding: 10px;
        text-align: center;
    }

    .table-body {
        overflow: auto;
    }

    .table-row {
        display: flex;
        border-bottom: 1px solid #ccc;
    }

    .table-row:last-child {
        border-bottom: none;
    }

    .table-cell {
        flex: 1;
        padding: 10px;
        text-align: center;
    }

    .table-cell a.btn {
        display: inline-block;
        padding: 5px;
        text-decoration: none;
    }
</style>

<?php
$validation = \Config\Services::validation();
?>

<div class="row">
    <div class="col-xl-12">
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title">Physical Components Achievement</h3>

            </div>
            <?php echo form_open_multipart('', array('class' => 'form-horizontal', 'id' => 'form-budget', 'name' => 'form-budget', 'role' => 'form')); ?>

            <div class="block-content">
                <?php
                $user  = service('user');
                //printr($user->getId());
                ?>
                <?php
                if ($user->district_id) {
                    $main = "disabled";
                } else {
                    $main = "";
                }

                ?>
                <div class="budgetplan">
                    <?php $user  = service('user'); ?>
                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="input-category">District</label>
                        <div class="col-sm-10">

                            <select name="district_id" id="district_id" class='form-control select2' <?php echo $main; ?>>
                                <?php foreach ($districts as $district) : ?>
                                    <option value="<?php echo $district->district_id ?>" data-fundagency="<?php echo $district->fund_agency_id ?>" <?php echo ($district->district_id == $user->district_id && $district->fund_agency_id == $user->fund_agency_id) ? 'selected="selected"' : '' ?>><?php echo $district->district_formatted ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="budgetplan">
                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="input-category">Year</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="year_id" name="year_id">
                                <?php foreach ($allYears as $allYear) { ?>
                                    <option value="<?php echo $allYear->id; ?>" <?php if ($editYear == $allYear->id) echo 'selected'; ?>><?php echo $allYear->name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="budgetplan">
                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="input-category">Month</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="month_id" name="month_id">
                                <option value="">select</option>
                                <?php foreach ($get_months as $get_month) { ?>
                                    <option value="<?= $get_month['number'] ?>" <?php if (date('n') - 1 == $get_month['number']) echo 'selected'; ?>>
                                        <?= $get_month['name'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>


                <h6 class="text-center text-danger"><strong>Don't Put Wrong Month Data While Upload The Acheivements, Once You Upload No longer To be Edit that month data, For that You Have to ask admin edit that month Data<span>*</span></strong></h6>

                <div class="block-content block-content-full" style="overflow-y: scroll;">
                    <table id="datatable" class="table table-bordered table-striped table-vcenter">
                        <!-- ajax code append here -->
                    </table>
                </div>

                <!-- <div class="form-group text-right">
                    <button id="submitButton" class="btn btn-alt-primary">Submit</button>
                </div> -->
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>



<?php js_start(); ?>
<script type="text/javascript">
    $(document).ready(function() {
        // Calculate totals when input values change
        $('.crop-input').on('input', function() {
            calculateTotals();
        });

        function calculateTotals() {
            $('.total-value').each(function() {
                var total = 0;
                var row = $(this).closest('tr');
                row.find('.crop-input').each(function() {
                    var value = parseFloat($(this).val());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });
                $(this).text(total);
            });
        }
    });
</script>

<script>
    $(document).ready(function() {
        // Handle change event on district and year select elements
        $('#district_id, #year_id, #month_id').change(function() {
            var districtId = $('#district_id').val();
            var fundAgencyId = $('#district_id option:selected').data('fundagency');
            var yearId = $('#year_id').val();
            var monthId = $('#month_id').val();
            var monthName = $("#month_id option:selected").text().trim();
            var yearName = $("#year_id option:selected").text().trim();
            var currentMonth = new Date().getMonth() + 1;
            $.ajax({
                url: '<?php echo admin_url("physicalachievement/searchtargetdata"); ?>',
                method: 'POST',
                data: {
                    district_id: districtId,
                    fund_agency_id: fundAgencyId,
                    year_id: yearId,
                    month_id: monthId,
                    month_name: monthName,
                    year_name: yearName
                },
                success: function(response) {

                    //  console.log(fundAgencyId);
                    $('#datatable').html(response);
                    $('.currentMonth').trigger('input');


                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });


        $('#district_id, #year_id, #month_id').trigger('change');
    });
</script>
<script>
    $(document).ready(function() {

        $(document).on('input', '.currentMonth', function() {
            var $row = $(this).closest('tr');
            var targetTotal = parseFloat($row.find('.targettotal').text());
            var achTotal = parseFloat($row.find('.achTotal').text());
            var currentMonthValue = parseFloat($(this).val());
            // var fpo = parseFloat($(this).val());
            // var wshg = parseFloat($(this).val());
            var cumulativeTotal = achTotal + currentMonthValue;
            if (currentMonthValue + achTotal > targetTotal) {
                currentMonthValue = 0;
                cumulativeTotal = 0;
            }

            $row.find('.currentMonth').val(currentMonthValue);
            $row.find('.cumulative').val(cumulativeTotal);

            if (cumulativeTotal > targetTotal) {
                $row.addClass('has-error');
                $row.find('.message').text('Cumulative total exceeds target total');
            } else {
                $row.removeClass('has-error');
                $row.find('.message').text('');
            }
        });

        $(document).on('input', '.fwsg', function() {
            var that = $(this);
            var $row = $(this).closest('tr');
            var currentMonthValue = $row.find(".currentMonth").val();
            var fpo = $row.find(".fpo");
            var wshg = $row.find(".wshg");
            var fpoValue = parseFloat(fpo.val());
            var wshgValue = parseFloat(wshg.val());
            var thatt = parseInt(that.val().replace(/[^\d.]/g, '')) || 0;
            fposhg = parseInt($(fpo).val()) + parseInt($(wshg).val());

            $(fpo).val(fpoValue);
            $(wshg).val(wshgValue);

            if (thatt > currentMonthValue) {
                $(fpo).val(0);
                $(wshg).val(0); // = 0;

            }

            if (that.is(fpo)) {
                fpoval = currentMonthValue - $(fpo).val();
                $(wshg).val(fpoval);
            } else {
                wshgval = currentMonthValue - $(wshg).val();
                $(fpo).val(wshgval);
            }


            // $row.find('.fpo').val(fpo);
            //$row.find('.cumulative').val(cumulativeTotal);
        });


    });
</script>
<?php js_end(); ?>