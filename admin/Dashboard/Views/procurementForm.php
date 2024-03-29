<div class="main-container">
    <div class="block">
        <form action="" method="post">
            <div class="block-header block-header-default">
                <h3 class="block-title">Procurement</h3>

            </div>
            <div class="block-content block-content-full">
                <!-- DataTables functionality is initialized with .js-dataTable-full class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->

                <div id="page_list_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Year</label>
                            <?php if(isset($year_text)){ ?>
                                <strong><?=$year_text?></strong>
                            <?php } else { ?>
                                <?php echo form_dropdown('year_id', $years, $year_id, ['class' => 'form-control mb-3']); ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-sm-12">
                        <table id="page_list" class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer" aria-describedby="page_list_info" >
                            <thead>
                                <tr>

                                    <th>slno</th>
                                    <th>District</th>
                                    <th>Quantity</th>
                                    <th>Farmer</th>
                                  

                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($districts as $key => $district) { ?>
                                <tr class="odd">

                                    <td><?=++$key?></td>
                                    <td><?=$district['name']?></td>
                                    <td><input type="text" name="district[<?=$district['id']?>][quantity]" class="form-control" value="<?=$district['quantity']?>"></td>
                                    <td><input type="text" name="district[<?=$district['id']?>][farmers]" class="form-control" value="<?=$district['farmers']?>" ></td>

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