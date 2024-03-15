<div class="main-container">
    <div class="block">
        <form action="" method="post">
            <div class="block-header block-header-default">
                <h3 class="block-title"></h3>

            </div>
            <div class="block-content block-content-full">
                <!-- DataTables functionality is initialized with .js-dataTable-full class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->

                <div id="page_list_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Year :</label>
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
                                    <th>Block</th>
                                    <th>GramPanchayat</th>
                                    <th>Village</th>
                                    <th>Framers</th>
                                    <th>CHC</th>
                                    <th>CMSC</th>
                                    <th>Acheivement</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($districts as $key => $district) { ?>
                                <tr class="odd">

                                    <td><?=++$key?></td>
                                    <td><?=$district['name']?></td>
                                    <td><input type="text" name="district[<?=$district['id']?>][blocks]" class="form-control" value="<?=$district['blocks']?>" ></td>
                                    <td><input type="text" name="district[<?=$district['id']?>][gps]" class="form-control" value="<?=$district['gps']?>" ></td>
                                    <td><input type="text" name="district[<?=$district['id']?>][villages]" class="form-control" value="<?=$district['villages']?>" ></td>
                                    <td><input type="text" name="district[<?=$district['id']?>][tentative_farmers]" class="form-control" value="<?=$district['tentative_farmers']?>" ></td>
                                    <td><input type="text" name="district[<?=$district['id']?>][chcs]" class="form-control" value="<?=$district['chcs']?>" ></td>
                                    <td><input type="text" name="district[<?=$district['id']?>][cmscs]" class="form-control" value="<?=$district['cmscs']?>" ></td>
                                    <td><input type="text" name="district[<?=$district['id']?>][acheivement]" class="form-control" value="<?=$district['acheivement']?>" ></td>

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