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
                    <div class="col-3">
                        <label class="form-label">District</label>
                        <?php echo form_dropdown('district_id', $districts, $district_id, ['class' => 'form-control mb-3', 'id' => 'districts']); ?>

                    </div>
                    <div class="col-3">
                        <label class="form-label">Block</label>
                        <?php echo form_dropdown('block_id', $blocks, $block_id, ['class' => 'form-control mb-3', 'id' => 'blocks']); ?>

                    </div>
                    <div class="col-3 mt-4">
                        <button class="btn btn-primary">Submit</button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

    <div class="block">
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
                                        <td><?= $enterprise['gp'] ?></td>
                                        <td><?= $enterprise['villages'] ?></td>
                                        <td><?= $enterprise['management_unit_type'] ?></td>
                                        <td><?= $enterprise['managing_unit_name'] ?></td>
                                        <td><?= $enterprise['date_estd'] ?></td>
                                        <td><?= $enterprise['mou_date'] ?></td>
                                        <td>
                                            <div class="btn-group btn-group-lg pull-right"><a class="btn btn-sm btn-primary" href="<?= $enterprise['edit_url'] ?>"><i class="fa fa-pencil"></i></a></div>
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
<script>
    $('#districts').on('change', function() {

        d_id = $(this).val();

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