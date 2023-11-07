<div class="block block-themed">
    <div class="block-header bg-primary-op">
        <h3 class="block-title">Filter</h3>
    </div>
    <form>
        <div class="block">
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-md-2">
                        <label>Year</label>
                        <?= form_dropdown('year_id', array_column($years, 'name', 'id'), $year_id, "class='form-control' id='filter_year_id' required") ?>

                    </div>
                    <div class="col-md-2">
                        <label>Season</label>

                        <select class="form-control" id="season" name="season" class='form-control' id='filter_season'>
                            <option value="">Select Season</option>
                            <?php foreach ($seasons as $value => $season) { ?>
                                <option value="<?= $value ?>" <?php if ($value == $current_season) {
                                      echo 'selected';
                                  } ?>>
                                    <?= $season ?>
                                </option>
                            <?php } ?>
                        </select>


                    </div>
                    <div class="col-lg-3">
                        <div class="form-group mg-b-10-force">
                            <label class="form-control-label">Week: <span class="tx-danger">*</span></label>
                            <?= form_dropdown('start_date', $weeks, $week_start_date, "id='filter_week' class='form-control js-select2'"); ?>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group mg-b-10-force">
                            <label class="form-control-label">Districts: <span class="tx-danger">*</span></label>
                            <?= form_dropdown('district_id', option_array_value($districts, 'id', 'name', array('0' => 'Select Districts')), set_value('district_id', $id ?? ''), "id='filter_district' class='form-control js-select2'"); ?>


                        </div>
                    </div>
                    <div class="col-md-2 mt-4">
                        <button id="btn-filter" class="btn btn-outline btn-primary">
                            <i class="fa fa-filter"></i> Filter</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="block block-themed">
    <div class="block-header bg-muted">
        <h3 class="block-title">Status Report</h3>
    </div>
    <div class="block-content block-content-full">
        <div class="tableFixHead">
            <table class="table  table-borderless" id="txn-table">
                <thead>
                    <tr class="highlight-heading1">


                        <th>Districts</th>
                        <th>Blocks</th>
                        <th>Status</th>
                        <th>Action Taken</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <?php foreach ($blockstatuses as $blockstatus): ?>
                        <tr>
                            <td>
                                <?= $blockstatus['district'] ?>
                            </td>
                            <td>
                                <?= $blockstatus['block'] ?>
                            </td>
                            <td>
                                <?= $blockstatus['status'] ?>
                            </td>
                            <td>
                                <?= $blockstatus['action_taken'] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php js_start(); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#btn-filter').click(function () {
            filterTable();
        });

        $('#btn-reset').click(function () {
            resetFilters();
        });

        Codebase.helpers(['select2']);
    });

    function filterTable() {
        var year_id = $('#filter_year_id').val();
        var season = $('#filter_season').val();
        var week = $('#filter_week').val();
        var district = $('#filter_district').val();


        // Perform your filtering logic here

        // Example: Fetch filtered data using AJAX and update the table
        $.ajax({
            url: "<?= $filtered_data_url; ?>",
            type: "post",
            data: { year_id: year_id, season: season, week: week, district: district },
            dataType: 'json',
            beforeSend: function () {
                $('.alert-dismissible, .text-danger').remove();
                // Show loading overlay or spinner if needed
            },
            success: function (data) {
                // Update your table with the filtered data
                updateTable(data);
            },
            complete: function () {
                // Hide loading overlay or spinner if needed
            },
            error: function () {
                // Handle error if the AJAX request fails
            }
        });
    }

    function resetFilters() {
        $('#form-filter')[0].reset();
        // Perform resetting of the table here if needed
    }

    function updateTable(data) {
        // Assuming your table body has an ID of 'table-body'
        var tableBody = $('#table-body');
        tableBody.empty(); // Clear the existing table rows

        // Loop through the data and create rows for the table
        for (var i = 0; i < data.length; i++) {
            var row = '<tr>';
            row += '<td>' + data[i].column1 + '</td>'; // Adjust column names
            row += '<td>' + data[i].column2 + '</td>';
            // Add more columns as needed
            row += '</tr>';

            tableBody.append(row);
        }
    }
</script>
<?php js_end(); ?>