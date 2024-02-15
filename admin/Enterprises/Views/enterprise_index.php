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

            <div id="datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">

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
                            <option value="">ALL</option>
                            <option value="shg" <?= $management_unit_type == "shg" ? 'selected' : ''; ?>>SHG</option>
                            <option value="fpo" <?= $management_unit_type == "fpo" ? 'selected' : ''; ?>>FPO</option>

                        </select>
                    </div>
                    <div class="col-2">
                        <label class="form-label">DOE</label>
                      
                        <?php echo form_dropdown('doeyear', $years, set_value('doeyear', $doeyear), ['class' => 'form-control mb-3', 'id' => 'doeyear']); ?>


                    </div>
                    <div class="col-2">
                        <label class="form-label">Unit Type</label>
                        <?php echo form_dropdown('unit_id', $units, set_value('unit_id', $unit_id), ['class' => 'form-control mb-3', 'id' => 'units']); ?>


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
                    <table id="datatable" class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer" aria-describedby="datatable_info">
                        <thead>
                            <tr>
                                <th>District</th>
                                <th>Block</th>
                                <th>GP</th>
                                <th>Unit Type</th>
                                <th>Mang. Unit Type</th>
                                <th>Mang. Unit Name</th>
                                <th>DOE</th>
                                <th>DOM</th>
                                <th>Date Added</th>

                                <th class="text-right no-sort sorting_disabled" aria-label="Actions">Actions</th>
                            </tr>
                        </thead>
                       
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
            var href = download_url + '?district_id=' + $('#districts').val() + '&block_id=' + $('#blocks').val() + '&management_unit_type=' + $('#management_unit_type').val() + '&doeyear=' + $('#doeyear').val();
            $('#btn-excel').attr('href', href);
        }
        $('#districts, #blocks, #management_unit_type, #doeyear').on('change', main);
        main();
    });

    var download_url = "<?= $excel_link ?>";

    $(document).ready(function() {
      
        var table = $('#datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "columnDefs": [{
                targets: 'no-sort',
                orderable: false
            }],
            "ajax": {
                url: "<?= $datatable_url ?>", // json datasource
                type: "post", // method  , by default get
                data: function(data) {
                    data.district_id = $('#districts').val();
                    data.block_id = $('#blocks').val();
                    data.unit_id = $('#units').val();
                    data.management_unit_type = $('#management_unit_type').val();
                    data.year = $('#doeyear').val();

                },
                beforeSend: function() {
                    $('.alert-dismissible, .text-danger').remove();
                    $("#datatable_wrapper").LoadingOverlay("show");
                },
                complete: function() {
                    $("#datatable_wrapper").LoadingOverlay("hide");
                },
                error: function() { // error handling
                    $(".datatable_error").html("");
                    $("#datatable_processing").css("display", "none");

                },
                dataType: 'json'
            }
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
                            $('#doeyear').html(html);
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