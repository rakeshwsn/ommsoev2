<div class="main-container">
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Enterprise Form</h3>
        </div>
        <div class="block-content block-content-full">
            <form method="post" id="">
                <div class="row">
                    <div class="col-6">
                        <label class="form-label">Year</label>
                        <input type="text" name="year_id" class="form-control" value="<?= $year_name; ?>" readonly>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Unit Name</label>
                        <input type="text" name="unit_id" class="form-control" value="<?= $unit_name; ?>" readonly>
                    </div>
                </div>
            </form>
        </div>
   
        <div class="block-content block-content-full">
            <form action="" method="post">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table id="page_list" class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer" aria-describedby="page_list_info">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th>District Name</th>
                                        <th>WSHG</th>
                                        <th>FPOS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($enterprisesList as $district) : ?>
                                        <tr>
                                            <td><?= $district->district ?></td>
                                            <td><input type="text" name="district[<?= $district->district_id ?>][wshg]" id="" class="form-control" value="<?= $district->wshg ?>"></td>
                                            <td><input type="text" name="district[<?= $district->district_id ?>][fpos]" id="" class="form-control" value="<?= $district->fpos ?>"></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div style="float: right;">
                                <button type="submit" class="btn btn-primary" id="submit">Submit</button>
                                <button type="cancel" class="btn btn-danger" id="cancel">Cancel</button>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
