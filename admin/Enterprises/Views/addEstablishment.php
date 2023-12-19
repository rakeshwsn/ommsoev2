<?php
$validation = \Config\Services::validation();
?>
<style>
    .error {
        color: red;
    }
</style>
<div class="block">
    <form method="post" id="establishmentform">
        <div class="block-header block-header-default">
            <h4><?php echo $enterprise_text ?></h4>
        </div>

        <div class="container ">

            <div class="row">
                <div class="col-6 form-group mt-15 <?= $validation->hasError('unit_id') ? 'is-invalid' : '' ?> ">
                    <label for="units">Name/Type of Unit <span class="text-danger">*</span></label>
                    <?php echo form_dropdown('unit_id', $units, set_value('unit_id', $unit_id), ['class' => 'form-control', 'id' => 'units', 'required' => 'required']); ?>
                </div>
                <div class="col-6 form-group mt-15 <?= $validation->hasError('management_unit_type') ? 'is-invalid' : '' ?>">
                    <label for="management_unit">Type of management unit<span class="text-danger">*</span></label>
                    <?php echo form_dropdown('management_unit_type', $management_unit_types, set_value('management_unit_type', $management_unit_type), ['class' => 'form-control', 'id' => 'management_unit_type']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group <?= $validation->hasError('district_id') ? 'is-invalid' : '' ?>">
                    <label for="district_id">District<span class="text-danger">*</span></label>
                    <?php echo form_dropdown('district_id', $districts, set_value('district_id', $district_id), ['class' => 'form-control', 'id' => 'districts']); ?>
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('district_id'); ?></div>
                </div>
                <div class="col-6 form-group <?= $validation->hasError('managing_unit_name') ? 'is-invalid' : '' ?>">
                    <label for="managing unit name">Name Of Managing Unit<span class="text-danger">*</span></label>
                    <input type="text" name="managing_unit_name" class="form-control" id="managing_unit_name" placeholder="Name" value="<?= set_value('managing_unit_name', $managing_unit_name) ?>" required>
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('managing_unit_name'); ?></div>


                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group <?= $validation->hasError('block_id') ? 'is-invalid' : '' ?>">
                    <label for="block_id">Block<span class="text-danger">*</span></label>
                    <?php echo form_dropdown('block_id', $blocks, set_value('block_id', $block_id), ['class' => 'form-control required', 'id' => 'blocks']); ?>
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('block_id'); ?></div>

                </div>
                <div class="col-6 form-group <?= $validation->hasError('contact_person') ? 'is-invalid' : '' ?>">
                    <label for="Contact Person">Contact Person<span class="text-danger">*</span></label>
                    <input type="text" name="contact_person" class="form-control" id="contact_person" placeholder="contact name" value="<?= set_value('contact_person', $contact_person) ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group <?= $validation->hasError('gp_id') ? 'is-invalid' : '' ?>">
                    <label for="gp_id">GP<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <?php echo form_dropdown(
                            'gp_id',
                            $gps,
                            set_value('gp_id', $gp_id),
                            ['class' => 'form-control', 'id' => 'gps']
                        ); ?>
                        <div class="input-group-append">
                            <a href="<?= $add_gp_url ?>" class="btn btn-secondary" id="btn-add-gp">Add GP</a>
                        </div>
                    </div>
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('gp_id'); ?></div>

                </div>
                <div class="col-6 form-group <?= $validation->hasError('contact_mobile') ? 'is-invalid' : '' ?>">
                    <label for="Contact Mobile">Contact Mobile<span class="text-danger">*</span></label>
                    <input type="text" name="contact_mobile" class="form-control" id="contact_mobile" placeholder="Mobile" maxlength="10" value="<?= set_value('contact_mobile', $contact_mobile) ?>" required>
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('contact_mobile'); ?></div>

                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group <?= $validation->hasError('village_id') ? 'is-invalid' : '' ?>">
                    <label for="village_id">Village<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <?php echo form_dropdown(
                            'village_id',
                            $villages,
                            set_value('village_id', $village_id),
                            ['class' => 'form-control', 'id' => 'villages']
                        ); ?>
                        <div class="input-group-append">
                            <a href="<?= $add_village_url ?>" class="btn btn-secondary" id="btn-add-village">Add Village</a>
                        </div>
                    </div>
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('village_id'); ?></div>

                </div>
                <div class="col-6 form-group <?= $validation->hasError('date_estd') ? 'is-invalid' : '' ?>">
                    <label for="Enterprise Establishment">Date of Enterprise Establishment<span class="text-danger">*</span></label>
                    <input type="date" name="date_estd" class="form-control" id="date_estd" placeholder="Date " value="<?= set_value('date_estd', $date_estd) ?>" min="2015-01-01" max="2030-12-31">
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('date_estd'); ?></div>

                </div>

            </div>
            <div class="row">
                <div class="col-6 form-group <?= $validation->hasError('budget_fin_yr_id') ? 'is-invalid' : '' ?>">
                    <label for="budget finnacial year">Budget Utilized of Financial year<span class="text-danger">*</span></label>
                    <?php echo form_dropdown('budget_fin_yr_id', $budget_fin_yrs, set_value('budget_fin_yr_id', $budget_fin_yr_id), ['class' => 'form-control', 'id' => 'budget_fin_yr_id']); ?>
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('budget_fin_yr_id'); ?></div>
                </div>
                <div class="col-6 form-group <?= $validation->hasError('mou_date') ? 'is-invalid' : '' ?>">
                    <label for="Date of OMU Under OMM">Date of OMU Under OMM<span class="text-danger">*</span></label>
                    <input type="date" name="mou_date" class="form-control" id="mou_unit" placeholder="Date " value="<?= set_value('mou_date', $mou_date) ?>" min="2015-01-01" max="2030-12-31">
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('mou_date'); ?></div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group <?= $validation->hasError('unit_budget') ? 'is-invalid' : '' ?>">
                    <label for="Established Unit Budget Head">Established Unit Budget Head<span class="text-danger">*</span></label>
                    <input type="text" name="unit_budget" class="form-control" id="unit_budget" placeholder="Enter Budget " value="<?= set_value('unit_budget', $unit_budget) ?>">
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('unit_budget'); ?></div>
                </div>

            </div>
            <div class="row">
                <div class="col-6 form-group <?= $validation->hasError('unit_budget_amount') ? 'is-invalid' : '' ?>">
                    <label for="Budget Utilized in Ruppes">Budget Utilized in Ruppes<span class="text-danger">*</span></label>
                    <input type="text" max="999999999999.99" maxlength="16" name="unit_budget_amount" class="form-control" id="unit_budget_amount" placeholder=" Amount" value="<?= set_value('unit_budget_amount', $unit_budget_amount) ?>">
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('unit_budget_amount'); ?></div>
                </div>

            </div>
            <div class="row">
                <div class="col-6 form-group mt-15 <?= $validation->hasError('is_support_basis_infr') ? 'is-invalid' : '' ?>">
                    <label for="management_unit">Is basic infrastructure support required?</label>
                    <?php echo form_dropdown('is_support_basis_infr', $is_support, set_value('is_support_basis_infr', $is_support_basis_infr), ['class' => 'form-control', 'id' => 'is_support_basis_infr']); ?>
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('is_support_basis_infr'); ?></div>
                </div>
            </div>
            <div class="block" id="budget_utilize">
                <div class="row">
                    <div class=" col-6 form-group <?= $validation->hasError('purpose_infr_support') ? 'is-invalid' : '' ?>">
                        <label for="Purposeof Addl. infa structure">Type/ Purposeof Addl. infa structure<span class="text-danger">*</span></label>
                        <input type="text" name="purpose_infr_support" class="form-control" id="purpose_infr_support" placeholder="Type/ Purposeof Addl. infa structure " value="<?= set_value('purpose_infr_support', $purpose_infr_support) ?>" required>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('purpose_infr_support'); ?></div>
                    </div>
                </div>
                <div class="row">
                    <div class="  col-6 form-group <?= $validation->hasError('addl_budget') ? 'is-invalid' : '' ?>">
                        <label for="exampleInputEmail1">Budget Head Utilised for Addl. infra support<span class="text-danger">*</span></label>
                        <input type="text" name="addl_budget" class="form-control" id="addl_budget" placeholder="Enter Budget " value="<?= set_value('addl_budget', $addl_budget) ?>">
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('addl_budget'); ?></div>
                    </div>
                </div>
                <div class="row">
                    <div class=" col-6 form-group <?= $validation->hasError('support_infr_amount') ? 'is-invalid' : '' ?>">
                        <label for="Budget Ruppes">Budget Utilized in Ruppes<span class="text-danger">*</span></label>
                        <input type="text" name="support_infr_amount" max="999999999999.99" maxlength="16" class="form-control" id="support_infr_amount" placeholder=" Amount" value="<?= set_value('support_infr_amount', $support_infr_amount) ?>">
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('support_infr_amount'); ?></div>
                    </div>
                </div>
            </div>
            <div class="row text-right">
                <div class="form-group ">
                    <button type="submit" class="btn btn-primary" id="submit">Submit</button>
                    <a href="admin/enterprises/cancel" class="btn btn-primary">Cancel</a>
                </div>
            </div>

        </div>

    </form>
</div>
<?php js_start(); ?>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

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
                        html = '<option value="">Select Block</potion>';
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
        //$('#districts').trigger('change');
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
                    // console.log(response);
                    if (response.gps) {
                        html = '<option value="">Select Gp</option>';
                        $.each(response.gps, function(k, v) {

                            html += '<option value="' + v.id + '"' + (gpid == v.id ? ' selected' : '') + '>' + v.name + '</option>';

                        });
                        $('#gps').html(html);


                    }
                },
                error: function() {
                    alert('something went wrong');
                },
                complete: function() {

                }
            });
        });
        //$('#gps').trigger('change');
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
                    // console.log(response);
                    if (response.villages) {
                        html = '<option value="">Select Village</option>';
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

        //hide show addl budget
        $('#is_support_basis_infr').on('change', function() {
            $is_support_basis_infr = $(this).val();
            if ($is_support_basis_infr == '0') {
                $('#budget_utilize').hide();
            } else {
                $('#budget_utilize').show();
            }
        });
        $('#is_support_basis_infr').trigger('change');

        //add gp btn click
        $('#btn-add-gp').click(function(e) {
            e.preventDefault();

            url = $(this).attr('href');
            dist = $('#districts').val();
            block = $('#blocks').val();

            url += "?district_id=" + dist + "&block_id=" + block;

            var popupWindow = window.open(url, "Add GP", "width=500,height=500");
            if (popupWindow) {
                //Browser has allowed it to be opened
                popupWindow.focus();
            } else {
                //Browser has blocked it
                alert('Please allow popups for this website');
            }
            var popupTimer = setInterval(function() {
                if (popupWindow.closed) {
                    clearInterval(popupTimer);
                    // console.log('Popup window closed.');
                    // Add your event handling logic here
                    $('#blocks').trigger('change');
                }
            });
        });
        //add village btn click
        $('#btn-add-village').click(function(e) {
            e.preventDefault();

            url = $(this).attr('href');
            dist = $('#districts').val();
            block = $('#blocks').val();
            gp = $('#gps').val();

            url += "?district_id=" + dist + "&block_id=" + block + "&gp_id=" + gp;

            var popupWindow = window.open(url, "Add Village", "width=500,height=500");
            if (popupWindow) {
                //Browser has allowed it to be opened
                popupWindow.focus();
            } else {
                //Browser has blocked it
                alert('Please allow popups for this website');
            }
            var popupTimer = setInterval(function() {
                if (popupWindow.closed) {
                    clearInterval(popupTimer);
                    // console.log('Popup window closed.');
                    // Add your event handling logic here
                    $('#gps').trigger('change');
                }
            });
        });
    });
    $(document).ready(function() {
        $("#establishmentform").validate({
            rules: {
                managing_unit_name: {
                    required: true,
                    lettersonly: true
                },
                unit_id: {
                    required: true,
                    ddrequired: true
                },
                gp_id: {
                    required: true,
                    ddrequired: true
                },
                district_id: {
                    required: true,
                    ddrequired: true
                },
                block_id: {
                    required: true,
                    ddrequired: true
                },
                village_id: {
                    required: true,
                    ddrequired: true
                },
                contact_person: {
                    required: true,
                    lettersonly: true
                },
                contact_mobile: {
                    required: true,
                    mobile:true
                },
                unit_budget_amount: {
                    required: true,
                    rupees: true
                },
                unit_budget: {
                    required: true,
                    decimal: true
                },
                purpose_infr_support: {
                    required: true,
                    lettersonly: true
                },
                addl_budget: {
                    required: true,
                    decimal: true
                },
                support_infr_amount: {
                    required: true,
                    rupees: true
                },
                budget_fin_yr_id: {
                    required: true,
                    ddrequired: true
                },
                date_estd: {
                    required: true,
                },
                mou_date: {
                    required: true,
                }

            },
            messages: {
                managing_unit_name: {
                    lettersonly: "Please enter only letters and spaces."
                },
                contact_mobile: {
                    mobile: "This is not a valid mobile number "
                },
                unit_budget_amount: {
                    ruppes: "Please enter  ruppes (ex-00.00) "
                },
                unit_budget: {
                    decimal: "Please enter only decimal numbers."
                },
            },
            errorPlacement: function(error, element) {
                //error.insertAfter(element); // Places the error message after the element
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent('.input-group'));
                } else {
                    error.insertAfter(element);
                }
            },
        });

    })
    $(document).ready(function() {
        jQuery.validator.addMethod("lettersonly", function(value, element) {
            return this.optional(element) || /^(?!.*(.)\1{3})[a-zA-Z\s]{1,40}$/.test(value);
        }, "Please enter only letters and spaces.");
        jQuery.validator.addMethod("digitsOnly", function(value, element) {
            return this.optional(element) || /^(?:\+?91|0)?[6789]\d{9}$/.test(value);
        }, "Please enter exactly 10 digits.");
        jQuery.validator.addMethod("decimal", function(value, element) {
            return this.optional(element) || /^\d+\.\d$/.test(value);
        }, "Please enter decimal number ");
        jQuery.validator.addMethod("ruppes", function(value, element) {
            return this.optional(element) || /^\d{1,10}\.\d{1,2}$/.test(value);
        }, "Please enter  rupees (ex-12.00) ");
        jQuery.validator.addMethod("ddrequired", function(value, element) {
            return this.optional(element) || (parseFloat(value) > 0);
        }, "* This is a required field");
        jQuery.validator.addMethod("mobile", function(value, element) {
            return this.optional(element) || /([0-9]{11}$)|(^[5-9][0-9]{9}$)/.test(value);
        }, "Please enter a valid mobile number");
    });
</script>
<?php js_end(); ?>