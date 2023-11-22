<div class="main-container">
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Filter</h3>

        </div>

        <div class="block-content block-content-full">
            <!-- DataTables functionality is initialized with .js-dataTable-full class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->

            <div id="page_list_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">

                <?php echo form_open('', ['method' => 'get']); ?>
                <div class="row">
                    <div class="col-2">
                        <label class="form-label">Year</label>
                        <?php echo form_dropdown('year_id', $years, set_value('year_id', $year_id), ['class' => 'form-control mb-3', 'id' => 'years']); ?>

                    </div>
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
                            <option value="all">all</option>
                            <option value="shg" <?= $management_unit_type == "shg" ? 'selected' : ''; ?>>SHG</option>
                            <option value="fpo" <?= $management_unit_type == "fpo" ? 'selected' : ''; ?>>FPO</option>

                        </select>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Month</label>
                        <?php echo form_dropdown('month_id', $months, set_value('month_id', $month_id), ['class' => 'form-control mb-3', 'id' => 'months']); ?>

                        </select>

                    </div>
                   
                    <div class="col-2 mt-4">
                        <button class="btn btn-primary">Submit</button>
                    </div>
                  
                    <div class="col-2 mt-4">

                        <a href="" id="btn-excel" class="btn btn-outline-danger"><i class="fa fa-file-excel-o"></i> Download Excel</a>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <div class="block">
        <div class="block-content block-content-full">

            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card pd-2 mg-b-5">
                        <h2 class="text-center mg-b-10">MPR of Enterprises Established</h2>
                        <h5 class="text-center">year : 2023-24 | month : October | district : Gajapati</h5>
                        <div class="table-responsive">
                            <table class="minimalistBlack mg-b-15 table-bordered1 table-striped1 border-dark" border="1">
                                <thead>
                                    <tr>
                                        <th class="tg-8d8j" rowspan="3">Type of Unit </th>
                                        <th class="tg-nrix text-center" colspan="3">No. of Functional Unit </th>
                                        <th class="tg-nrix text-center" colspan="6">Transaction Data </th>
                                    </tr>
                                    <tr>
                                        <th class="tg-nrix" rowspan="2">Up to Previous Month </th>
                                        <th class="tg-nrix" rowspan="2">During the Month </th>
                                        <th class="tg-nrix" rowspan="2">Cumulative </th>
                                        <th class="tg-nrix" colspan="2">Up to Previous Month </th>
                                        <th class="tg-nrix" colspan="2">During the Month </th>
                                        <th class="tg-nrix" colspan="2">Cumulative </th>
                                    </tr>
                                    <tr>
                                        <th class="tg-nrix">Total Turnover (in Rs)</th>
                                        <th class="tg-nrix">Total Income (in Rs)</th>
                                        <th class="tg-nrix">Total Turnover (in Rs)</th>
                                        <th class="tg-nrix">Total Income (in Rs)</th>
                                        <th class="tg-nrix">Total Turnover (in Rs)</th>
                                        <th class="tg-nrix">Total Income (in Rs)</th>
                                    </tr>
                                </thead>

                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {


            $(function() {
                $('#districts').on('change', function() {

                    var d_id = $(this).val(); // Declare d_id with var

                    $.ajax({
                        url: 'admin/enterprisesreport/blocks',
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



                });
            });



        })
    </script>