<div class="main-container">
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Area</h3>

        </div>
        <div class="block-content block-content-full">
            <!-- DataTables functionality is initialized with .js-dataTable-full class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->

            <form>
                <div id="page_list_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="row">
                        <div class="col-2">
                            <label class="form-label">Year :</label>
                            <?php if (isset($year_text)) { ?>
                                <strong><?= $year_text ?></strong>
                            <?php } else { ?>
                                <?php echo form_dropdown('year_id', $years, $year_id, ['class' => 'form-control mb-3']); ?>
                            <?php } ?>
                        </div>
                        <div class="col-2">
                            <label class="form-label">District :</label>
                            <?php if (isset($district_text)) { ?>
                                <strong><?= $district_text ?></strong>
                            <?php } else { ?>
                                <?php echo form_dropdown('district_id', $districts, $district_id, ['class' => 'form-control mb-3', 'id' => 'districts']); ?>
                            <?php } ?>
                        </div>
                        <div class="col-2">
                            <label class="form-label">Block :</label>
                            <?php if (isset($block_text)) { ?>
                                <strong><?= $block_text ?></strong>
                            <?php } else { ?>
                                <?php echo form_dropdown('block_id', $blocks, $block_id, ['class' => 'form-control mb-3', 'id' => 'blocks']); ?>
                            <?php } ?>
                        </div>
                        <div class="col-2">
                            <label class="form-label">Season :</label>
                            <select class="form-control mb-3" name="season" id="season">
                                <option value="rabi"<?= set_select('season', 'rabi', $season == "rabi") ? 'selected' : ''; ?>>rabi</option>
                                <option value="kharif"<?= set_select('season', 'kharif', $season == "kharif") ? 'selected' : ''; ?>>kharif</option>
                            </select>

                        </div>
                        <div class="col-3 mt-4">
                            <button class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="block-content block-content-full">
            <form action="" method="post">
                <div class="row">
                    <div class="col-sm-12">
                        <table id="page_list" class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer" aria-describedby="page_list_info">
                            <thead>
                                <tr>

                                    <th>slno</th>
                                    <th>Gp</th>
                                    <th>Farmers</th>
                                    <th>Achievement</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($gps as $key => $gp) { ?>
                                    <tr class="odd">

                                        <td><?= ++$key ?></td>
                                        <td><?= $gp['name'] ?></td>
                                        <td><input type="text" name="gp[<?= $gp['id'] ?>][farmers]" class="form-control" value="<?= $gp['farmers'] ?>"></td>
                                        <td><input type="text" name="gp[<?= $gp['id'] ?>][achievement]" class="form-control" value="<?= $gp['achievement'] ?>"></td>

                                    </tr>
                                <?php } ?>

                            </tbody>
                        </table>
                        <div style="float: right;">
                            <button class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('#districts').on('change', function() {

        d_id = $(this).val();

        $.ajax({
            url: 'admin/dashboard/blocks',
            data: {
                district_id: d_id
            },
            type: 'GET',
            dataType: 'JSON',
            beforeSend: function() {},
            success: function(response) {
                if (response.blocks) {
                    html = '<option value="">Select Block</option>';
                    $.each(response.blocks, function(k, v) {

                        html += '<option value="' + v.id + '">' + v.name + '</option>';

                    });
                    $('#blocks').html(html)
                }
            },
            error: function() {
                alert('something went wrong');
            },
            complete: function() {

            }
        });
    });
</script>