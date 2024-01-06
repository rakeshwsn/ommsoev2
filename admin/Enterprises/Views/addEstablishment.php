<?php
$validation = \Config\Services::validation();
?>
<style>
    .error {
        color: red;
    }

    .dotted-border {
        border: 2px dotted rgba(124 129 134 / 50%);
        border-radius: 1%;
        padding-bottom: 20px;
        padding-top: 20px;

    }
</style>
<div class="block">
    <form method="post" id="establishmentform">
        <div class="block-header block-header-default">
            <h1 class="block-title"><b><?php echo $enterprise_text ?></b></h1>
        </div>


        <div class="dotted-border mx-4 my-4 pd-3">

            <div class="container">
                <h3 class="text-left text-dark my-3" style="font-weight: bold !important;">Local Info</h3>
                <div class="row">
                    <div class="col-6 form-group <?= $validation->hasError('district_id') ? 'is-invalid' : '' ?>">
                        <label for="district_id">District<span class="text-danger">*</span></label>
                        <?php
                        $select_attributes = array(
                            'class' => 'form-control js-select2',
                            'id' => 'districts',
                        );
                        if ($district_id) {
                            $select_attributes = array_merge($select_attributes, array('readonly' => 'readonly'));
                        }
                        echo form_dropdown('district_id', $districts, set_value('lgd_code', $district_id), $select_attributes, ['class' => 'form-control', 'id' => 'districts']); ?>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('district_id'); ?></div>
                    </div>
                    <div class="col-6 form-group <?= $validation->hasError('block_id') ? 'is-invalid' : '' ?>">
                        <label for="block_id">Block<span class="text-danger">*</span></label>
                        <?php
                        $select_attributes = array(
                            'class' => 'form-control js-select2',
                            'id' => 'blocks',
                        );
                        if ($block_id) {
                            $select_attributes = array_merge($select_attributes, array('readonly' => 'readonly'));
                        }
                        echo form_dropdown('block_id', $blocks, set_value('block_lgd_code', $block_id), $select_attributes, ['class' => 'form-control required', 'id' => 'blocks']); ?>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('block_id'); ?></div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-6 form-group <?= $validation->hasError('gp_id') ? 'is-invalid' : '' ?>">
                        <label for="gp_id">GP<span class="text-danger">*</span></label>
                        <div class="input-group">
                            <?php
                            $select_attributes = array(
                                'class' => 'form-control js-select2',
                                'id' => 'gps',
                            );
                            if ($gp_id) {
                                $select_attributes = array_merge($select_attributes, array('readonly' => 'readonly'));
                            }
                            echo form_dropdown('gp_id', $gps, set_value('gp_lgd_code', $gp_id), $select_attributes, ['class' => 'form-control', 'id' => 'gps']); ?>
                            <div class="input-group-append">
                                <a href="<?= $add_gp_url ?>" class="btn btn-sm btn-secondary" id="btn-add-gp">Add GP</a>

                            </div>
                        </div>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('gp_id'); ?></div>

                    </div>

                    <div class="col-6 form-group <?= $validation->hasError('village_id') ? 'is-invalid' : '' ?>">
                        <label for="village_id">Village<span class="text-danger">*</span></label>
                        <div class="input-group">
                            <?php
                            $select_attributes = array(
                                'class' => 'form-control js-select2',
                                'id' => 'villages',
                            );
                            if ($village_id) {
                                $select_attributes = array_merge($select_attributes, array('readonly' => 'readonly'));
                            }
                            echo form_dropdown('village_id', $villages, set_value('village_lgd_code', $village_id), $select_attributes, ['class' => 'form-control', 'id' => 'villages']); ?>
                            <div class="input-group-append">
                                <a href="<?= $add_village_url ?>" class="btn btn-sm btn-secondary" id="btn-add-village">Add Village</a>
                            </div>
                        </div>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('village_id'); ?></div>

                    </div>
                </div>


            </div>
        </div>

        <div class="dotted-border mx-4 my-4 pd-3">

            <div class="container">
                <h3 class="text-left text-dark my-3" style="font-weight: bold !important;">Unit Info</h3>
                <div class="row">
                    <div class="col-6 form-group mt-15 <?= $validation->hasError('unit_id') ? 'is-invalid' : '' ?> ">
                        <label for="units">Name/Type of Unit <span class="text-danger">*</span></label>
                        <?php
                        $select_attributes = array(
                            'class' => 'form-control js-select2',
                            'id' => 'units',
                        );
                        if ($unit_id) {
                            $select_attributes = array_merge($select_attributes, array('readonly' => 'readonly'));
                        }
                        echo form_dropdown('unit_id', $units, set_value('unit_id', $unit_id), $select_attributes, ['class' => 'form-control', 'id' => 'units', 'required' => 'required']); ?>
                    </div>
                    <div class="col-6 form-group mt-15 <?= $validation->hasError('management_unit_type') ? 'is-invalid' : '' ?>">
                        <label for="management_unit">Type of management unit<span class="text-danger">*</span></label>
                        <?php echo form_dropdown('management_unit_type', $management_unit_types, set_value('management_unit_type', $management_unit_type), ['class' => 'form-control', 'id' => 'management_unit_type']); ?>
                    </div>
                </div>
                <div class="row">

                    <div class="col-6 form-group <?= $validation->hasError('managing_unit_name') ? 'is-invalid' : '' ?>">
                        <label for="managing unit name">Name Of Managing Unit<span class="text-danger">*</span></label>
                        <input type="text" name="managing_unit_name" class="form-control" id="managing_unit_name" placeholder="Name" value="<?= set_value('managing_unit_name', $managing_unit_name) ?>" required>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('managing_unit_name'); ?></div>


                    </div>
                    <div class="col-6 form-group <?= $validation->hasError('contact_person') ? 'is-invalid' : '' ?>">
                        <label for="Contact Person">Contact Person<span class="text-danger">*</span></label>
                        <input type="text" name="contact_person" class="form-control" id="contact_person" placeholder="contact name" value="<?= set_value('contact_person', $contact_person) ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 form-group <?= $validation->hasError('contact_mobile') ? 'is-invalid' : '' ?>">
                        <label for="Contact Mobile">Contact Mobile<span class="text-danger">*</span></label>
                        <input type="text" name="contact_mobile" class="form-control" id="contact_mobile" placeholder="Mobile" maxlength="10" value="<?= set_value('contact_mobile', $contact_mobile) ?>" required>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('contact_mobile'); ?></div>

                    </div>
                    <div class="col-6"></div>
                </div>


            </div>
        </div>
        <div class="dotted-border mx-4 my-4 pd-3">

            <div class="container">
                <h3 class="text-left text-dark my-3" style="font-weight: bold !important;">Center Info</h3>
                <div class="row">
                    <div class="col-6 form-group mt-15 <?= $validation->hasError('unit_id') ? 'is-invalid' : '' ?> ">
                        <input type="radio" id="main_center" name="center_type" value="main_center">
                        <label for="main_center">Main Center</label>
                        <input type="radio" id="sub_center" name="center_type" value="sub_center">
                        <label for="sub_center">Sub Center</label><br>

                    </div>
                    <div id="center_name" class="col-6 form-group mt-15 <?= $validation->hasError('management_unit_type') ? 'is-invalid' : '' ?>">
                        <label for="main_center_name">Main Center name<span class="text-danger">*</span></label>
                        <?php echo form_dropdown('management_unit_type', $management_unit_types, set_value('management_unit_type', $management_unit_type), ['class' => 'form-control', 'id' => 'main_center_name']); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <table id="page_list" class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer" aria-describedby="page_list_info">
                            <thead>
                                <tr>
                                    <th>Equipment</th>
                                    <th>Quantity</th>
                                    <th class="text-right no-sort sorting_disabled" aria-label="Actions">Actions</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr class="new_row">
                                    <td>
                                        <?php echo form_dropdown('equipment_id', $equipments, set_value('equipment_id', $equipment_id), ['class' => 'form-control', 'id' => 'equipment_id']); ?></td>
                                    <td><input type="text" name="tags" id="" class="form-control"></td>
                                    <td><button type="button" id="delete_row" class="btn-sm btn btn-danger btn-remove btn pull-right" onclick="return confirm('Are you sure want to delete')" href=""><i class="fa fa-trash-o"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td><button type="button" class="btn-sm btn btn-primary btn-remove btn pull-right" id="equipment_row" href=""><i class="fa fa-plus"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="dotted-border mx-4 my-4 pd-3">
            <div class="container">
                <h3 class="text-left text-dark my-3" style="font-weight: bold !important;">Date Info</h3>
                <div class="row">
                    <div class="col-6 form-group <?= $validation->hasError('date_estd') ? 'is-invalid' : '' ?>">
                        <label for="Enterprise Establishment">Date of Enterprise Establishment<span class="text-danger">*</span></label>
                        <input type="date" name="date_estd" class="form-control" id="date_estd" placeholder="Date " value="<?= set_value('date_estd', $date_estd) ?>" min="2015-01-01" max="2030-12-31">
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('date_estd'); ?></div>

                    </div>
                    <div class="col-6 form-group <?= $validation->hasError('mou_date') ? 'is-invalid' : '' ?>">
                        <label for="Date of OMU Under OMM">Date of OMU Under OMM<span class="text-danger">*</span></label>
                        <input type="date" name="mou_date" class="form-control" id="mou_unit" placeholder="Date " value="<?= set_value('mou_date', $mou_date) ?>" min="2015-01-01" max="2030-12-31">
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('mou_date'); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dotted-border mx-4 my-4 pd-3">
            <div class="container">
                <h3 class="text-left text-dark my-3" style="font-weight: bold !important;">Budget Info</h3>
                <div class="row">
                    <div class="col-6 form-group <?= $validation->hasError('budget_fin_yr_id') ? 'is-invalid' : '' ?>">
                        <label for="budget finnacial year">Budget Utilized of Financial year<span class="text-danger">*</span></label>
                        <?php echo form_dropdown('budget_fin_yr_id', $budget_fin_yrs, set_value('budget_fin_yr_id', $budget_fin_yr_id), ['class' => 'form-control', 'id' => 'budget_fin_yr_id']); ?>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('budget_fin_yr_id'); ?></div>
                    </div>
                    <div class="col-6 form-group <?= $validation->hasError('unit_budget') ? 'is-invalid' : '' ?>">
                        <label for="Established Unit Budget Head">Established Unit Budget Head<span class="text-danger">*</span></label>
                        <input type="text" name="unit_budget" class="form-control" id="unit_budget" placeholder="Enter Budget " value="<?= set_value('unit_budget', $unit_budget) ?>">
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('unit_budget'); ?></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 form-group <?= $validation->hasError('unit_budget_amount') ? 'is-invalid' : '' ?>">
                        <label for="Budget Utilized in Ruppes">Budget Utilized in Ruppes<span class="text-danger">*</span></label>
                        <input type="text" name="unit_budget_amount" class="form-control" id="unit_budget_amount" placeholder=" Amount" value="<?= set_value('unit_budget_amount', $unit_budget_amount) ?>" required>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('unit_budget_amount'); ?></div>
                    </div>
                    <div class="col-6 form-group <?= $validation->hasError('own_share') ? 'is-invalid' : '' ?>">
                        <label for="total own share">Investment by the WSHG/FPO<span class="text-danger">*</span></label>
                        <input type="text" name="own_share" class="form-control" id="own_share" placeholder="Enter total own share " value="<?= set_value('own_share', $own_share) ?>" required>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('own_share'); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dotted-border mx-4 my-4 pd-3">
            <div class="container">
                <h3 class="text-left text-dark my-3" style="font-weight: bold !important;">Additional Info</h3>
                <div class="row">
                    <div class="col-6">
                        <input type="checkbox" id="is_support_basis_infr" name="is_support_basis_infr" value="is_support_basis_infr" <?php if ($is_support_basis_infr) {
                                                                                                                                            echo 'checked';
                                                                                                                                        } ?>>
                        <label for="is_support_basis_infr"> Is any additional support provided from Govt. ?</label>
                    </div><br><br>

                </div>
                <div class="block" id="budget_utilize">
                    <div class="row">
                        <div class=" col-6 form-group <?= $validation->hasError('purpose_infr_support') ? 'is-invalid' : '' ?>">
                            <label for="Purposeof Addl. infa structure">Type/ Purposeof Addl. infa structure<span class="text-danger">*</span></label>
                            <input type="text" name="purpose_infr_support" class="form-control" id="purpose_infr_support" placeholder="Type/ Purposeof Addl. infa structure " value="<?= set_value('purpose_infr_support', $purpose_infr_support) ?>" required>
                            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('purpose_infr_support'); ?></div>
                        </div>
                        <div class="  col-6 form-group <?= $validation->hasError('addl_budget') ? 'is-invalid' : '' ?>">
                            <label for="exampleInputEmail1">Budget Head Utilised for Addl. infra support<span class="text-danger">*</span></label>
                            <input type="text" name="addl_budget" class="form-control" id="addl_budget" placeholder="Enter Budget " value="<?= set_value('addl_budget', $addl_budget) ?>" required>
                            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('addl_budget'); ?></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class=" col-6 form-group <?= $validation->hasError('support_infr_amount') ? 'is-invalid' : '' ?>">
                            <label for="Budget Ruppes">Budget Utilized in Ruppes<span class="text-danger">*</span></label>
                            <input type="text" name="support_infr_amount" class="form-control" id="support_infr_amount" placeholder=" Amount" value="<?= set_value('support_infr_amount', $support_infr_amount) ?>" required>
                            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('support_infr_amount'); ?></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row ">
                <div class="col-9"></div>
                <div class="col-3 form-group text-right ">
                    <button type="submit" class="btn btn-primary" id="submit">Submit</button>
                    <a href="admin/enterprises/cancel" class="btn btn-danger">Cancel</a>
                </div>
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

            var d_id = $(this).val(); // Declare d_id with var

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

                        var html = '<option value="">Select Block</option>'; // Declare html with var
                        $.each(response.blocks, function(k, v) {
                            html += '<option value="' + v.lgd_code + '">' + v.name + '</option>';
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
        //$('#districts').trigger('change');
        $('#blocks').on('change', function() {
            var b_id = $(this).val();
            $.ajax({
                url: 'admin/enterprises/gps',
                data: {
                    block_id: b_id
                },
                type: 'GET',
                dataType: 'JSON',
                beforeSend: function() {},
                success: function(response) {
                    if (response.gps) {
                        var html = '<option value="">Select GP</option>';
                        $.each(response.gps, function(k, v) {
                            html += '<option value="' + v.lgd_code + '">' + v.name + '</option>';
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
            var g_id = $(this).val();
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
                        var html = '<option value="">Select Villages</option>';
                        $.each(response.villages, function(k, v) {
                            html += '<option value="' + v.lgd_code + '">' + v.name + '</option>';
                        });
                        $('#villages').html(html);
                    }
                },
                error: function() {
                    alert('something went wrong');
                },
                complete: function() {

                }
            });

        });
        // //hide show addl budget
        if ($("#is_support_basis_infr").prop('checked') == true) {
            $('#budget_utilize').show();
        } else {
            $('#budget_utilize').hide();
        }

        $('#is_support_basis_infr').on('change', function() {
            $is_support_basis_infr_checked = $(this).prop('checked');
            if ($is_support_basis_infr_checked) {
                $('#budget_utilize').show();
            } else {
                $('#budget_utilize').hide();
            }
        });
        // $('#is_support_basis_infr').trigger('change');
        //Center choose
        if ($("#main_center").prop("checked")) {
            // do something
            $('#center_name').hide();
        }

        // OR
        if ($("#sub_center").is(":checked")) {
            // do something
            $('#center_name').show();
        }
        $('#sub_center').on('change', function() {
            $sub_center_checked = $(this).prop('checked');
            if ($sub_center_checked) {
                $('#center_name').show();
            } else {
                $('#center_name').hide();
            }
        });
        $('#main_center').on('change', function() {
            $main_center_checked = $(this).prop('checked');
            if ($main_center_checked) {
                $('#center_name').hide();
            }
        });
        $('#sub_center').trigger('change');

        $('#equipment_row').on('click', function(e) {

            var newRow = $('#page_list .new_row').first().clone();
            $('#page_list tr:last').before(newRow);
        });
        //delete equipment row
       

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
        Codebase.helpers(['select2']);
    });
    $(document).ready(function() {
        jQuery.validator.addMethod("lettersonly", function(value, element) {
            return this.optional(element) || /^(?!.*(?:([A-Za-z,])\1{2}))[A-Za-z, ]{3,20}$/.test(value);
        }, "Please enter only letters and spaces.");
        jQuery.validator.addMethod("letters", function(value, element) {
            return this.optional(element) || /^(?!.*(?:([A-Za-z])\1{2}))[A-Za-z ]{3,20}$/.test(value);
        }, "Please enter only letters and spaces.");
        jQuery.validator.addMethod("digitsOnly", function(value, element) {
            return this.optional(element) || /^(?:\+?91|0)?[6789]\d{9}$/.test(value);
        }, "Please enter exactly 10 digits.");
        jQuery.validator.addMethod("decimal", function(value, element) {
            return this.optional(element) || /^\d+\.\d$/.test(value);
        }, "Please enter decimal number ");
        jQuery.validator.addMethod("rupees", function(value, element) {
            return this.optional(element) || /^(16\d{0,14}(?:\.\d{1,2})?|\d+(?:\.\d{1,2})?)$/.test(value);
        }, "Please enter  rupees (ex-12.00) ");
        jQuery.validator.addMethod("ddrequired", function(value, element) {
            return this.optional(element) || (parseFloat(value) > 0);
        }, "* This is a required field");
        jQuery.validator.addMethod("mobile", function(value, element) {
            return this.optional(element) || /([0-9]{11}$)|(^[5-9][0-9]{9}$)/.test(value);
        }, "Please enter a valid mobile number");
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
                    letters: true
                },
                contact_mobile: {
                    required: true,
                    mobile: true
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
                },


            },
            messages: {
                managing_unit_name: {
                    lettersonly: "Please enter only letters and spaces."
                },
                contact_person: {
                    letters: "Please enter only letters and spaces."
                },
                contact_mobile: {
                    mobile: "This is not a valid mobile number "
                },
                unit_budget_amount: {
                    rupees: "Please enter  ruppes (ex-00.00) "
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
</script>
<?php js_end(); ?>