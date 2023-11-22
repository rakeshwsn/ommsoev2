<div class="main-container">
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Edit</h3>
        </div>
       
        <div class="block-content block-content-full">
            <div id="page_list_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                <!-- DataTables functionality is initialized with .js-dataTable-full class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
                <div class="row">
                    <div class="col-4">
                        <label class="form-label">Year</label>
                        <input type="text" name="" class="form-control"  value="<?= $entranses['year_id']?>"readonly>          
                        <span id="em1" class="text-danger"></span>

                    </div>
                    <div class="col-4">
                        <label class="form-label">District</label>
                        <input type="text" name="" class="form-control" value="<?= $entranses['district_id']?>"readonly>                 
                        <span id="em2" class="text-danger"></span>

                    </div>
                    <div class="col-4">
                        <label class="form-label">Months</label>
                        <input type="text" name="" class="form-control" value="<?= $entranses['month_id']?>"readonly>                    
                        <span id="em3" class="text-danger"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <label class="form-label">Block</label>
                        <input type="text" name="" class="form-control" value="<?= $entranses['block_id']?>"readonly>                        
                        <span id="em6" class="text-danger"></span>
                    </div>
                    <div class="col-4">
                        <label class="form-label">Gp</label>
                        <input type="text" name="" class="form-control" value="<?= $entranses['gp_id']?>"readonly>              
                        <span id="em9" class="text-danger"></span>
                    </div>

                    <div class="col-4">
                        <label class="form-label">Village</label>
                        <input type="text" name="" class="form-control" value="<?= $entranses['village_id']?>"readonly>
                        <span id="em5" class="text-danger"></span>
                    </div>
                </div>
                <div class="row">

                    <div class="col-4">
                        <label class="form-label">Unit Type</label>
                        <input type="text" name="" class="form-control" value="<?= $entranses['unit_id']?>"readonly>           
                         <span id="em3" class="text-danger"></span>

                    </div>
                    <div class="col-4">
                        <label class="form-label">Fortnight</label>
                        <input type="text" name="" class="form-control" value="<?= $entranses['period']?>"readonly>             

                        <span id="em" class="text-danger"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title"> Edit Data</h3>
        </div>
        <div class="block-content block-content-full">
            <div class="block">
                <form action="" method="post">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="page_list" class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer" aria-describedby="page_list_info">
                                <thead>
                                    <tr>
                                        <th>Date Upload</th>
                                        <th>No. of Days Functional</th>
                                        <th>Quintals of Produce processed</th>
                                        <th>Charges per Quintal</th>
                                        <th>Total Expenditure in the fortnight</th>
                                        <th>Total Tourn Over in the fortnight</th>
                                    </tr>
                                </thead>
                              
                                <tbody>

                                    <tr class="odd">
                                        <td><input type="date_create" name="created_at" class="form-control" value="<?= $entranses['created_at']?>"></td>
                                        <td><input type="text" name="no_of_days_functional" class="form-control" value="<?= $entranses['no_of_days_functional'] ?>"></td>
                                        <td><input type="text" name="produced" class="form-control" value="<?= $entranses['produced'] ?>"></td>
                                        <td><input type="text" name="charges_per_qtl" class="form-control" value="<?= $entranses['charges_per_qtl'] ?>"></td>
                                        <td><input type="text" name="total_expend" class="form-control" value="<?= $entranses['total_expend'] ?>"></td>
                                        <td><input type="text" name="total_turnover" class="form-control" value="<?= $entranses['total_turnover'] ?>"></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>

        </div>

    </div>



</div>
<style>
    #loading-overlay {
        background: rgb(255 255 255 / 80%);
        display: flex;
        align-items: center;

        justify-content: center;
        text-align: center;
        z-index: 9999;
    }
</style>
<?php js_start(); ?>


<?php js_end(); ?>