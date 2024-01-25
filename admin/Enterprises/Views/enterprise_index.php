<div class="main-container">
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Filter</h3>
            <div class="block-options">
                <a href="admin/enterprises/add" data-toggle="tooltip" title="" class="btn btn-primary js-tooltip-enabled">
                    <i class="fa fa-plus"></i>
                </a>
            </div>
        </div>

        <div class="block-content block-content-full">
            <!-- DataTables functionality is initialized with .js-dataTable-full class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->

            <div id="page_list_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">

                <?php echo form_open('', ['method' => 'get']); ?>
                <div class="row">
                    <div class="col-2">
                        <label class="form-label">District</label>
                        <?php echo form_dropdown('district_id', $districts, set_value('district_id', $district_id), ['class' => 'form-control mb-3', 'id' => 'districts']); ?>

                    </div>
                    <div class="col-2">
                        <label class="form-label">Block</label>
                        <?php echo form_dropdown('block_id', $blocks, set_value('block_id', $block_id), ['class' => 'form-control mb-3', 'id' => 'blocks']); ?>

                    </div>
                    <div class="col-2">
                        <label class="form-label">Managing Unit Type</label>
                        <select class="form-control" name="management_unit_type" id="management_unit_type">
                            <option value="all">ALL</option>
                            <option value="shg" <?= $management_unit_type == "shg" ? 'selected' : ''; ?>>SHG</option>
                            <option value="fpo" <?= $management_unit_type == "fpo" ? 'selected' : ''; ?>>FPO</option>

                        </select>
                    </div>
                    <div class="col-2">
                        <label class="form-label">DOE</label>
                        <select name="doeyear" class="form-control mb-3" id="years">
                            <?php foreach ($years as $yearvalue) : ?>
                                <?php $selected = ($doeyear == $yearvalue) ? 'selected' : ''; ?>
                                <option value="<?= $yearvalue ?>" <?= $selected ?>><?= $yearvalue ?></option>
                            <?php endforeach; ?>
                        </select>

                    </div>
                    <div class="col-2">
                        <label class="form-label">Unit Type</label>
                        <?php echo form_dropdown('unit_id', $units, set_value('unit_id', $unit_id), ['class' => 'form-control mb-3', 'id' => 'districts']); ?>


                    </div>
                    <div class="col-2 mt-4 ">
                        <button class="btn btn-primary">Submit</button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Enterprises List</h3>
            <div class="block-options">
            <a href="<?= $excel_link ?>" id="btn-excel" class="btn btn-outline-danger"><i class="fa fa-file-excel-o"></i> Download Form</a>
            </div>

        </div>
        <div class="block-content block-content-full">
            <div class="row">
                <div class="col-sm-12">
                    <table id="page_list" class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer" aria-describedby="page_list_info">
                        <thead>
                            <tr>
                                <th>District</th>
                                <th>Block</th>
                                <th>GP</th>
                                <th>Village</th>
                                <th>Unit Type</th>
                                <th>Mang. Unit Type</th>
                                <th>Mang. Unit Name</th>
                                <th>DOE</th>
                                <th>DOM</th>

                                <th class="text-right no-sort sorting_disabled" aria-label="Actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($enterprises) { ?>
                                <?php foreach ($enterprises as $enterprise) { ?>
                                    <tr class="odd">
                                        <td><?= $enterprise['districts'] ?></td>
                                        <td><?= $enterprise['blocks'] ?></td>
                                        <td><?= $enterprise['gps'] ?></td>
                                        <td><?= $enterprise['villages'] ?></td>
                                        <td><?= $enterprise['unit_name'] ?></td>
                                        <td><?= $enterprise['management_unit_type'] ?></td>
                                        <td><?= $enterprise['managing_unit_name'] ?></td>
                                        <td><?= $enterprise['date_estd'] ?></td>
                                        <td><?= $enterprise['mou_date'] ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm pull-right"><a class="btn btn-sm btn-primary" href="<?= $enterprise['edit_url'] ?>"><i class="fa fa-pencil"></i></a></div>
                                        </td>
                                    </tr>
                                <?php } ?>
                        </tbody>
                    <?php  } else { ?>
                        <tbody>
                            <tr class="odd">
                                <td colspan="9">
                                    <h3 class="text-center">data not avaliable</h3>
                                </td>
                            </tr>
                        </tbody>
                    <?php  } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var download_url = "http://ommsoev2.local/admin/enterprises/exceldownld";

    $(function() {
        function main() {
            var href = download_url + '?district_id=' + $('#districts').val() + '&block_id=' + $('#blocks').val() + '&management_unit_type=' + $('#management_unit_type').val() + '&doeyear=' + $('#years').val();
            $('#btn-excel').attr('href', href);
        }
        $('#districts, #blocks, #management_unit_type, #years').on('change', main);
        main();
    });
    
    var download_url = "<?= $excel_link ?>";
    
    $(document).ready(function() {
        var table = $('#page_list').DataTable({
            "paging": true,
            "pageLength": 10
        });

        $(function() {
            $('#districts').on('change', function() {

                var d_id = $(this).val(); // Declare d_id with var

                $.ajax({
                    url: 'admin/enterprises/blocks',
                    data: {
                        district_id: d_id
                    },
                    type: 'GET',
                    dataType: 'JSON',
                    beforeSend: function() {},
                    success: function(response) {
                        if (response.blocks) {

                            var html = '<option value="">Select Block</option>'; // Declare html with var
                            $.each(response.blocks, function(k, v) {
                                html += '<option value="' + v.id + '">' + v.name + '</option>';
                            });
                            $('#blocks').html(html);
                        }
                    },
                    error: function() {
                        alert('something went wrong');
                    },
                    complete: function() {

                    }
                });

                $.ajax({
                    url: 'admin/enterprises/doe',
                    data: {
                        district_id: d_id
                    },
                    type: 'GET',
                    dataType: 'JSON',
                    beforeSend: function() {},
                    success: function(response) {


                        if (response.years) {
                            var html = '<option value="">Select DOE</option>';
                            $.each(response.years, function(k, v) {
                                html += '<option value="' + v.year + '">' + v.year + '</option>';
                            });
                            $('#years').html(html);
                        }
                    },
                    error: function() {
                        alert('something went wrong');
                    },
                    complete: function() {

                    }
                });

            });
        });



    })
</script>