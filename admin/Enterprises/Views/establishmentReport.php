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
                        <label class="form-label">Month</label>
                        <?php echo form_dropdown('month_id', $months, set_value('month_id', $month_id), ['class' => 'form-control mb-3', 'id' => 'months']); ?>

                        </select>

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
                        <label class="form-label">By Date</label>
                        <select class="form-control" name="unit_type" id="unit_type">
                            <option value="">All</option>
                            <option value="without_establishment_date" <?= $unit_type == "without_establishment_date" ? 'selected' : ''; ?>>without establishment date</option>
                            <option value="without_mou_date" <?= $unit_type == "without_mou_date" ? 'selected' : ''; ?>>Without mou date</option>
                            <option value="only_establishment_date" <?= $unit_type == "only_establishment_date" ? 'selected' : ''; ?>>Only establishment date</option>

                        </select>

                    </div>
                    <div class="col-11"></div>
                    <div class="col-1">
                        <button class="btn btn-primary">Submit</button>
                    </div>

                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Enterprise Establishment Report</h3>
            <div class="block-options">
                <a href="<?= $download_pdf_url ?>" id="btn-pdf" class="btn btn-outline-danger"><i class="fa fa-file-pdf-o"></i> Download PDF</a>
                <a href="<?= $download_excel_url ?>" id="btn-excel" class="btn btn-outline-danger"><i class="fa fa-file-excel-o"></i> Download Excel</a>
            </div>
        </div>
        <div class="block-content block-content-full">
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card pd-2 mg-b-5">
                        <div class="table-responsive">
                            <table id="page_list" class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer" aria-describedby="page_list_info">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <?php if ($district_id) { ?>
                                            <th colspan="<?= count($unit_names)?>"  >
                                                District: <?= $district_text; ?> ||
                                            <?php  } ?>
                                            <?php if ($block_id) { ?>

                                                Block: <?= $block_text; ?> ||
                                            <?php  } ?>
                                            <?php if ($year_id) { ?>

                                                Year: <?= $year_text; ?> ||
                                            <?php  } ?>
                                            <?php if ($month_id) { ?>

                                                Month: <?= $month_text; ?>  ||
                                        <?php  } ?>
                                        <?php if ($management_unit_type =='management_unit_type') { ?>

                                            Unit: <?= $unit_text; ?> </th>
                                        <?php  } ?>

                                    </tr>
                                    <tr>
                                        <th rowspan="2">
                                            <?php if ($district_id) {
                                                echo "Blocks";
                                            } elseif ($block_id) {
                                                echo "Grampanchayat";
                                            } else {
                                                echo "Districts";
                                            }
                                            ?>

                                        </th>
                                        <th colspan="<?= count($unit_names)?>" class="text-center">Type and No. of Units</th>
                                        <th rowspan="2">Total</th>
                                    </tr>

                                    <tr>
                                        <?php foreach ($unit_names as $unit) : ?>
                                            <th><?= $unit->name ?></th>
                                        <?php endforeach; ?>

                                    </tr>
                                </thead>

                                <tbody>

                                    <?php if ($block_id) { ?>
                                        <?php foreach ($gpunits as $gpunit) : ?>
                                            <tr>
                                                <td><?= $gpunit['gp'] ?></td>
                                                <?php foreach ($gpunit['g_units'] as $gunit) : ?>
                                                    <td><?= $gunit ?></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php } else if ($district_id) { ?>
                                        <?php foreach ($blockunits as $blockunit) : ?>
                                            <tr>
                                                <td><?= $blockunit['block'] ?></td>
                                                <?php foreach ($blockunit['b_units'] as $bunit) : ?>
                                                    <td><?= $bunit ?></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php } else { ?>
                                        <?php foreach ($units as $unit) : ?>
                                            <tr>
                                                <td><?= $unit['district'] ?></td>
                                                <?php foreach ($unit['units'] as $eunit) : ?>
                                                    <td><?= $eunit ?></td>
                                                <?php endforeach; ?>

                                            </tr>

                                        <?php endforeach; ?>
                                    <?php } ?>

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