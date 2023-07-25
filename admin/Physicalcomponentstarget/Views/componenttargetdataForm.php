<style>


    #loadingWindow {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
    }

    .loadingIcon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 50px;
        height: 50px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .form-control {
        height: 38px;
        /* Adjust the height as needed */
    }

    .form-control {
        height: 38px;
        /* Adjust the height as needed */
    }

    #datatable {
        width: 100%;
        margin-bottom: 20px;
    }

    #datatable thead th {
        background-color: #f9fafb;
        color: #333;
        font-weight: bold;
        vertical-align: middle;
    }

    #datatable tbody td {
        vertical-align: middle;
    }

    #datatable tbody tr:nth-child(even) {
        background-color: #f4f4f4;
    }

    /* Styling the input fields inside the table */
    .crop-input {
        width: 140px; /* Set the width as desired */
        text-align: center;
    }

    /* Styling the "Submit" button at the bottom */
    .text-right #submitButton {
        margin-top: 10px;
    }
</style>

<?php
$validation = \Config\Services::validation();
?>

<div class="row">
    <div class="col-xl-12">
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title">Physical Components Target</h3>

            </div>
            <div id="loadingWindow">
                <div class="loadingIcon"></div>
            </div>
            <?php echo form_open_multipart('', array('class' => 'form-horizontal', 'id' => 'form-budget', 'name' => 'form-budget', 'role' => 'form')); ?>

            <div class="block-content">
                <div class="budgetplan">
                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="input-category">Year</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="year_id" name="year_id">
                                <option value="">select</option>
                                <?php foreach ($allYears as $allYear) { ?>
                                    <option value="<?php echo $allYear->id; ?>" <?php if ($editYear == $allYear->id) echo 'selected'; ?>><?php echo $allYear->name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="block-content block-content-full" style="overflow-y: scroll;">
                    <table id="datatable" class="table table-bordered table-striped table-vcenter js-dataTable-full">

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

<script>
    $(document).ready(function() {
        // Handle change event on district and year select elements
        $('#year_id').change(function() {
            var yearId = $('#year_id').val();
            $.ajax({
                url: '<?php echo admin_url("physicalcomponentstarget/searchtargetdata"); ?>',
                method: 'POST',
                data: {
                    year_id: yearId,
                },
                success: function(response) {
                    // console.log("hello");
                    $('#datatable').html(response);

                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });
        $('#year_id').trigger('change');

    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        function calculateTotals() {
            $('tbody tr').each(function() {
                var total = 0;
                $(this).find('.crop-input').each(function() {
                    var value = parseFloat($(this).val());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });
                $(this).find('.total-value').text(total);
            });
        }
        $(document).on('input', '.crop-input', function() {
            var value = $(this).val();
            var sanitizedValue = value.replace(/[^0-9]/g, '');
            $(this).val(sanitizedValue);
            calculateTotals();
        });

        calculateTotals();

        $(document).ajaxComplete(function() {
            calculateTotals();
        });

        $('#year_id').trigger('change');
    });
</script>

<script>
    $(document).ready(function() {
        $('#form-budget').on('submit', function() {
            $('#loadingWindow').show();
        });
    });
</script>


<?php js_end(); ?>