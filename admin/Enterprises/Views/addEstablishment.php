<?php
$validation = \Config\Services::validation();
?>
<div class="block">
    <form method="post">
        <div class="block-header">
            <h4>Enterprise/Establishment Unit</h4>
        </div>
        <div class="container ">
            <div class="row">
                <div class="col-6 form-group mt-15 <?=$validation->hasError('unit_id')?'is-invalid':''?> ">
                    <label for="unit_id">Name/Type of Unit</label>
                    <?php echo form_dropdown('unit_id', $units, $unit_id, ['class' => 'form-control mb-3', 'id' => 'units']); ?>
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('unit_id'); ?></div>


                </div>
                <div class="col-6 form-group mt-15 <?=$validation->hasError('management_unit_type')?'is-invalid':''?>">
                    <label for="management_unit">Type of management unit</label>
                    <select name="management_unit_type" id="unit" class="form-control">
                        <option value="SHG" <?= $management_unit_type=="SHG" ? 'selected' : ''; ?>> SHG</option>
                        <option value="FPO" <?= $management_unit_type=="FPO" ? 'selected' : ''; ?>> FPO</option>
                    </select>
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('management_unit_type'); ?></div>
 
                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group <?=$validation->hasError('district_id')?'is-invalid':''?>">
                    <label for="district">District</label>
                    <?php echo form_dropdown('district_id', $districts, $district_id, ['class' => 'form-control mb-3', 'id' => 'districts']); ?>
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('district_id'); ?></div>

                </div>
                <div class="col-6 form-group <?=$validation->hasError('managing_unit_name')?'is-invalid':''?>">
                    <label for="managing unit name">Name Of Managing Unit</label>
                    <input type="text" name="managing_unit_name" class="form-control" id="managing_unit_name" placeholder="Name" value="<?= $managing_unit_name?>">
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('managing_unit_name'); ?></div>

                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group <?=$validation->hasError('block_id')?'is-invalid':''?>">
                    <label for="block">Block</label>
    
                    <?php echo form_dropdown('block_id', $blocks, $block_id, ['class' => 'form-control mb-3', 'id' => 'blocks']); ?>
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('block_id'); ?></div>

                </div>
                <div class="col-6 form-group <?=$validation->hasError('contact_person')?'is-invalid':''?>">
                    <label for="Contact Person">Contact Person</label>
                    <input type="text" name="contact_person" class="form-control" id="contact_person" placeholder="Name" value="<?= $contact_person?>">
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('contact_person'); ?></div>

                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group <?=$validation->hasError('gp_id')?'is-invalid':''?>">
                    <label for="Gp">GP</label>
                    <?php echo form_dropdown('gp_id',$gps,$gp_id, ['class' => 'form-control mb-3', 'id' => 'gps']); ?>
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('gp_id'); ?></div>

                </div>
                <div class="col-6 form-group <?=$validation->hasError('contact_mobile')?'is-invalid':''?>">
                    <label for="Contact Mobile">Contact Mobile</label>
                    <input type="text" name="contact_mobile" class="form-control" id="exampleInputPassword1" placeholder="Mobile" value="<?= $contact_mobile?>">
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('contact_mobile'); ?></div>

                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group <?=$validation->hasError('village_id')?'is-invalid':''?>">
                    <label for="village">Village</label>
                    <?php echo form_dropdown('village_id', $villages,$village_id, ['class' => 'form-control mb-3', 'id' => 'villages']); ?>
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('village_id'); ?></div>

                </div>
                <div class="col-6 form-group <?=$validation->hasError('date_estd')?'is-invalid':''?>">
                    <label for="Enterprise Establishment">Date of Enterprise Establishment</label>
                    <input type="date" name="date_estd" class="form-control" id="date_estd" placeholder="Date " value="<?= $date_estd?>">
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('date_estd'); ?></div>

                </div>

            </div>
            <div class="row">
                <div class="col-6 form-group <?=$validation->hasError('budget_fin_yr')?'is-invalid':''?>">
                    <label for="budget finnacial year">Budget Utilized of Financial year</label>
                    <input type="date" name="budget_fin_yr" class="form-control" id="budget_fin_yr" placeholder="Year " value="<?= $budget_fin_yr?>">
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('budget_fin_yr'); ?></div>

                </div>
                <div class="col-6 form-group <?=$validation->hasError('mou_date')?'is-invalid':''?>">
                    <label for="Date of OMU Under OMM">Date of OMU Under OMM</label>
                    <input type="date" name="mou_date" class="form-control" id="mou_unit" placeholder="Date " value="<?= $mou_date?>">
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('mou_date'); ?></div>

                </div>

            </div>
            <div class="row">
                <div class="col-6 form-group <?=$validation->hasError('unit_budget_id')?'is-invalid':''?>">
                    <label for="Established Unit Budget Head">Established Unit Budget Head</label>
                    <?php echo form_dropdown('unit_budget_id', $unit_budgets, $unit_budget_id, ['class' => 'form-control mb-3', 'id' => 'budgets']); ?>
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('unit_budget_id'); ?></div>

                </div>
                <div class="col-6 form-group <?=$validation->hasError('unit_budget_amount')?'is-invalid':''?>">
                    <label for="Budget Utilized in Ruppes">Budget Utilized in Ruppes</label>
                    <input type="text" name="unit_budget_amount" class="form-control" id="unit_budget_amount" placeholder=" Amount" value="<?= $unit_budget_amount?>">
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('unit_budget_amount'); ?></div>

                </div>
            </div>

            <div class="col-6 form-group <?=$validation->hasError('is_support_basis_infr')?'is-invalid':''?>">
                <label for="Addl. Support">Addl. Support provided for basic infrastructure Creation</label>
                <select name="is_support_basis_infr" id="is_support_basis_infr" class="form-control">
                    <option value="No" <?= $is_support_basis_infr=="No" ? 'selected' : ''; ?>>No</option>
                    <option value="Yes" <?= $is_support_basis_infr=="Yes" ? 'selected' : ''; ?>>Yes</option>
                </select>
                <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('is_support_basis_infr'); ?></div>

            </div>
            <div class="col-6 form-group <?=$validation->hasError('purpose_infr_support')?'is-invalid':''?>">
                <label for="Purposeof Addl. infa structure">Type/ Purposeof Addl. infa structure</label>
                <input type="text" name="purpose_infr_support" class="form-control" id=" purpose_infr_support" placeholder="Type/ Purposeof Addl. infa structure " value="<?= $purpose_infr_support?>">
                <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('purpose_infr_support'); ?></div>

            </div>
            <div class="col-6 form-group <?=$validation->hasError('addl_budget_id')?'is-invalid':''?>">
                <label for="exampleInputEmail1">Budget Head Utilised for Addl. infra support</label>
                <?php echo form_dropdown('addl_budget_id', $addl_budgets, $addl_budget_id, ['class' => 'form-control mb-3', 'id' => 'budgets']); ?>
                <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('addl_budget_id'); ?></div>

            </div>
            <div class="col-6 form-group <?=$validation->hasError('support_infr_amount')?'is-invalid':''?>">
                <label for="Budget Ruppes">Budget Utilized in Ruppes</label>
                <input type="text" name="support_infr_amount" class="form-control" id=" support_infr_amount" placeholder=" Amount" value="<?= $support_infr_amount?>">
                <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('support_infr_amount'); ?></div>

            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <button type="reset" class="btn btn-primary">Cancel</button>

        </div>
       
    </form>
</div>

<script>
    $(function() {
        $('#districts').on('change', function() {

            d_id = $(this).val();
            blockid = <?php echo $block_id; ?>

            $.ajax({
                url: 'admin/enterprises/blocks',
                data: {
                    district_id: d_id
                },
                type: 'GET',
                dataType: 'JSON',
                beforeSend: function() {},
                success: function(response) {
                    // console.log(response);
                    if (response.blocks) {
                        html = '<option value="">Select Block</option>';
                        $.each(response.blocks, function(k, v) {

                            html += '<option value="' + v.id + '"' + (blockid == v.id ? ' selected' : '') + '>' + v.name + '</option>';

                        });
                        $('#blocks').html(html)
                        $('#blocks').trigger('change');
                    }
                },
                error: function() {
                    alert('something went wrong');
                },
                complete: function() {

                }
            });
        });
        $('#districts').trigger('change');
        $('#blocks').on('change', function() {

            b_id = $(this).val();
           
            gpid = <?php echo $gp_id; ?>

            $.ajax({
                url: 'admin/enterprises/gps',
                data: {
                    block_id: b_id
                },
                type: 'GET',
                dataType: 'JSON',
                beforeSend: function() {},
                success: function(response) {
                      console.log(response);
                    if (response.gps) {
                        html = '<option value="">Select Gp</option>';
                        $.each(response.gps, function(k, v) {

                            html += '<option value="' + v.id + '"' + (gpid == v.id ? ' selected' : '') + '>' + v.name + '</option>';

                        });
                        $('#gps').html(html);
                        $('#gps').trigger('change');
                    }
                },
                error: function() {
                    alert('something went wrong');
                },
                complete: function() {

                }
            });
        });
       

        $('#gps').on('change', function() {

            g_id = $(this).val();
            villageid = <?php echo $village_id; ?>

            $.ajax({
                url: 'admin/enterprises/villages',
                data: {
                    gp_id: g_id
                },
                type: 'GET',
                dataType: 'JSON',
                beforeSend: function() {},
                success: function(response) {
                    if (response.villages) {
                        html = '<option value="">Select Gp</option>';
                        $.each(response.villages, function(k, v) {

                            html += '<option value="' + v.id + '"' + (villageid == v.id ? ' selected' : '') + '>' + v.name + '</option>';

                        });
                        $('#villages').html(html)
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
</script>