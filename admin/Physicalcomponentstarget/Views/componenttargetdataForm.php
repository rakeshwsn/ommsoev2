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
                                <option value="1" selected>2023-24</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="block-content block-content-full" style="overflow-y: scroll;">
                    <table id="datatable" class="table table-bordered table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th>District</th>
                                <?php foreach ($components as $component) : ?>
                                    <th>
                                        <?= $component['description']; ?>
                                    </th>
                                <?php endforeach; ?>
                                <th>Total</th> <!-- Added the Total column header -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($districts_main as $districts_mains) : ?>
                                <tr>
                                    <td>
                                        <?= $districts_mains->name; ?>
                                    </td>
                                    <?php foreach ($components as $component) : ?>
                                        <td>
                                            <?php
                                            $value = '';
                                            foreach ($main_master as $item) {
                                                if ($item['district_id'] == $districts_mains->id && $item['mc_id'] == $component['id']) {
                                                    $value = $item['total'];
                                                    break;
                                                }
                                            }
                                            ?>
                                            <input type="number" name="component[<?= $districts_mains->id ?>][<?= $component['id'] ?>]" class="crop-input form-control" oninput="calculateTotals()" value="<?= $value ?>">
                                        </td>
                                    <?php endforeach; ?>
                                    <td>
                                        <span class="total-value"></span> <!-- Added the total-value span for displaying the total -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

                <div class="form-group text-right">
                    <button id="submitButton" class="btn btn-alt-primary">Submit</button>
                </div>
            </div>

            <?php echo form_close(); ?>



        </div>
    </div>
</div>



<?php js_start(); ?>
<script type="text/javascript">
    $(document).ready(function() {
        // Allow only numbers in the input field
        $('.crop-input').on('input', function() {
            var value = $(this).val();
            var sanitizedValue = value.replace(/[^0-9]/g, '');
            $(this).val(sanitizedValue);
            calculateTotals();
        });

        $('.crop-input').each(function() {
            $(this).trigger('input');
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
  $('#form-budget').on('submit', function() {
    $('#loadingWindow').show();
  });
});
</script>
<?php js_end(); ?>