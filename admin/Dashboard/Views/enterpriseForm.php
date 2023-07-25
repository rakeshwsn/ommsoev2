<div class="main-container">
    <div class="block">
        <form action="" method="post">
            <div class="block-header block-header-default">
                <h3 class="block-title">Enterprises:</h3>

            </div>
            <div class="block-content block-content-full">
                <!-- DataTables functionality is initialized with .js-dataTable-full class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->

                <div id="page_list_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Year</label>
                            <?php if (isset($year_text)) { ?>
                                <strong><?= $year_text ?></strong>
                            <?php } else { ?>
                                <strong>Not Avaliable</strong>
                            <?php } ?>
                        </div>
                        <div class="col-6">
                            <label class="form-label">District:</label>
                            <?php if (isset($district_text)) { ?>
                                <strong><?= $district_text ?></strong>
                            <?php } else { ?>
                                <strong>Not Avaliable</strong>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-sm-12">
                        <table id="page_list" class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer" aria-describedby="page_list_info">
                            <thead>
                                <tr>

                                    <th>slno</th>
                                    <th>Unit Name</th>
                                    <th>WSHG</th>
                                    <th>FPOS</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($units as $key => $unit)
                               { ?>
                                    <tr class="odd">

                                        <td><?= ++$key ?></td>
                                        <td><?= $unit['unit_name'] ?>
                                            <input type="hidden" name="unit_name[<?= $unit['unit_id'] ?>][name]" class="form-control" value="<?= $unit['unit_name'] ?>">
                                        </td>
                                        <td><input type="text" name="unit_name[<?= $unit['unit_id'] ?>][wshg]" class="form-control" value="<?= $unit['wshg'] ?>"></td>
                                        <td><input type="text" name="unit_name[<?= $unit['unit_id'] ?>][fpos]" class="form-control" value="<?= $unit['fpos'] ?>"></td>
                                    </tr>
                                <?php } ?>

                            </tbody>
                        </table>
                        <div style="float: right;">
                            <button class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>