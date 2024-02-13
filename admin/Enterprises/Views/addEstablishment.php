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
                        <label for="district_id">District<span class="text-danger"></span></label>
                        <?= form_dropdown('district_id', option_array_value($districts, 'id', 'name', array('0' => 'Select Districts')), set_value('district_id', $district_id), "id='districts' class='form-control js-select2'"); ?>
                    </div>
                    <div class="col-6 form-group <?= $validation->hasError('block_id') ? 'is-invalid' : '' ?>">
                        <label for="block_id">Block<span class="text-danger"></span></label>
                        <?php
                        echo form_dropdown('block_id', option_array_value($blocks, 'id', 'name', array('0' => 'Select Block')), set_value('block_id', $block_id), "id='blocks' class='form-control js-select2'"); ?>
                    </div>
                </div>

            </div>
        </div>
        <div class="dotted-border mx-4 my-4 p-3">
            <div class="container">
                <h3 class="text-left text-dark my-3" style="font-weight: bold;">Local Info</h3>
                <div class="row">
                    <div class="col-6" id="gp_vlg">
                        <div class="">
                            <input type="checkbox" id="gp_vlg_available" name="address_type" value="1">
                            <label for="gp_vlg_available">If GP and Village is avaliable</label>
                        </div>
                        <br>
                        <div class="gp">
                            <label for="gp_id">GP<span class="text-danger"></span></label>
                            <div class="input-group">
                                <?php
                                echo form_dropdown(
                                    'gp_id',
                                    option_array_value($gps, 'id', 'name', array('0' => 'Select GP')),
                                    set_value('gp_id', $gp_id),
                                    "id='gps' class='form-control js-select2'"
                                ); ?>
                                <div class="input-group-append">
                                    <a href="<?= $add_gp_url ?>" class="btn btn-sm btn-secondary" id="btn-add-gp">Add GP</a>
                                </div>
                            </div>


                        </div>
                        <br>
                        <div class="village">

                            <label for="village_id">Village<span class="text-danger"></span></label>
                            <div class="input-group">
                                <?php
                                echo form_dropdown('village_id', option_array_value($villages, 'id', 'name', array('0' => 'Select Village')), set_value('village_id', $village_id), "id='villages' class='form-control js-select2'"); ?>
                                <div class="input-group-append">
                                    <a href="<?= $add_village_url ?>" class="btn btn-sm btn-secondary" id="btn-add-village">Add Village</a>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="col-6" id="dis_adrs">
                        <div class="address_type">
                            <input type="checkbox" id="gp_vlg_not_available" name="address_type" value="1">
                            <label for="gp_vlg_not_available">If GP and Village is not avaliable</label>
                        </div>
                        <br>
                        <div class="address">
                            <label for="address">Address<span class="text-danger"></span></label>
                            <div class="input-group">
                                <textarea name="address" id="address" cols="30" rows="3" class="form-control"></textarea>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="dotted-border mx-4 my-4 pd-3">
            <div class="container">
                <h3 class="text-left text-dark my-3" style="font-weight: bold !important;">Unit Info</h3>
                <div class="row">
                    <div class="col-6 form-group mt-15 <?= $validation->hasError('unit_id') ? 'is-invalid' : '' ?> ">
                        <label for="units">Name/Type of Unit <span class="text-danger"></span></label>
                        <?php
                        $select_attributes = array(
                            'class' => 'form-control js-select2',
                            'id' => 'unit_type',
                        );
                        echo form_dropdown('unit_id', $units, $unit_id, $select_attributes); ?>
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

        <div class="dotted-border mx-4 my-4 pd-3" id="center_info">
            <div class="container">
                <h3 class="text-left text-dark my-3" style="font-weight: bold !important;">Center Info</h3>
                <div class="row">
                    <div class="col-6 form-group mt-15 <?= $validation->hasError('unit_id') ? 'is-invalid' : '' ?>">
                        <input type="radio" id="main_center" name="center_type" value="main_center" <?= $center_type == "main_center" ? 'checked' : ''; ?>>
                        <label for="main_center">Main Center</label>
                        <input type="radio" id="sub_center" name="center_type" value="sub_center" <?= $center_type == "sub_center" ? 'checked' : ''; ?>>
                        <label for="sub_center">Sub Center</label>
                    </div>
                </div>
                <div class="row">
                    <div id="center_name" class="col-6 form-group mt-15 <?= $validation->hasError('') ? 'is-invalid' : '' ?>">
                        <label for="main_center_name">Main Center name<span class="text-danger">*</span></label>
                        <?php
                        echo form_dropdown('main_center_id', option_array_value($main_centers, 'id', 'name', ['0' => 'Select Main Center']), set_value('main_center_id', $main_center_id), ['class' => 'form-control', 'id' => 'main_center_list']); ?>
                        <div id="center_message" class="text-danger mt-3"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <table id="equipments" class="table table-bordered table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th>Equipment</th>
                                    <th>Quantity</th>
                                    <th class="text-right no-sort sorting_disabled" aria-label="Actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($enterpriseequipments)) {
                                    $rows = 0;
                                    foreach ($enterpriseequipments as $equipment) { ?>

                                        <tr>
                                            <td>
                                                <select name="equipments[<?= $rows ?>]" class="form-control">
                                                    <?php foreach ($equipments as $key => $dropdownEquipment) { ?>
                                                        <option value="<?= $key ?>" <?= ($key == $equipment->equipment_id) ? 'selected' : '' ?>>
                                                            <?= $dropdownEquipment ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td><input type="text" class="form-control" name="quantity[<?= $rows ?>]" value="<?= $equipment->quantity ?>"></td>
                                            <td>
                                                <button type="button" class="btn-sm btn btn-danger btn-remove btn pull-right" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></button>
                                            </td>

                                        </tr>

                                <?php $rows++;
                                    }
                                } ?>
                                <tr id="footer">
                                    <td colspan="3">
                                        <button type="button" class="btn-sm btn btn-primary btn pull-right" id="equipment_row" href=""><i class="fa fa-plus"></i></button>
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
                    </div>
                    <div class="col-6 form-group <?= $validation->hasError('mou_date') ? 'is-invalid' : '' ?>">
                        <label for="Date of OMU Under OMM">Date of OMU Under OMM<span class="text-danger">*</span></label>
                        <input type="date" name="mou_date" class="form-control" id="mou_unit" placeholder="Date " value="<?= set_value('mou_date', $mou_date) ?>" min="2015-01-01" max="2030-12-31">
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
                    </div>
                    <div class="col-6 form-group <?= $validation->hasError('unit_budget') ? 'is-invalid' : '' ?>">
                        <label for="Established Unit Budget Head">Established Unit Budget Head<span class="text-danger">*</span></label>
                        <input type="text" name="unit_budget" class="form-control" id="unit_budget" placeholder="Enter Budget " value="<?= set_value('unit_budget', $unit_budget) ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 form-group <?= $validation->hasError('unit_budget_amount') ? 'is-invalid' : '' ?>">
                        <label for="Budget Utilized in Ruppes">Budget Utilized in Ruppes<span class="text-danger">*</span></label>
                        <input type="text" name="unit_budget_amount" class="form-control" id="unit_budget_amount" placeholder=" Amount" value="<?= set_value('unit_budget_amount', $unit_budget_amount) ?>" required>
                    </div>
                    <div class="col-6 form-group <?= $validation->hasError('own_share') ? 'is-invalid' : '' ?>">
                        <label for="total own share">Investment by the WSHG/FPO<span class="text-danger">*</span></label>
                        <input type="text" name="own_share" class="form-control" id="own_share" placeholder="Enter total own share " value="<?= set_value('own_share', $own_share) ?>" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="dotted-border mx-4 my-4 pd-3">
            <div class="container">
                <h3 class="text-left text-dark my-3" style="font-weight: bold !important;">Additional Info</h3>
                <div class="row">
                    <div class="col-6">
                        <input type="checkbox" id="is_support_basis_infr" name="is_support_basis_infr" value="1" <?php echo ($is_support_basis_infr == 1) ? 'checked' : ''; ?>>
                        <label for="is_support_basis_infr"> Is any additional support provided from Govt. ?</label>
                    </div>
                    <br><br>

                </div>
                <div class="block" id="budget_utilize">
                    <div class="row">
                        <div class=" col-6 form-group <?= $validation->hasError('purpose_infr_support') ? 'is-invalid' : '' ?>">
                            <label for="Purposeof Addl. infa structure">Type/ Purposeof Addl. infa structure<span class="text-danger">*</span></label>
                            <input type="text" name="purpose_infr_support" class="form-control" id="purpose_infr_support" placeholder="Type/ Purposeof Addl. infa structure " value="<?= set_value('purpose_infr_support', $purpose_infr_support) ?>">
                        </div>
                        <div class="  col-6 form-group <?= $validation->hasError('addl_budget') ? 'is-invalid' : '' ?>">
                            <label for="exampleInputEmail1">Budget Head Utilised for Addl. infra support<span class="text-danger">*</span></label>
                            <input type="text" name="addl_budget" class="form-control" id="addl_budget" placeholder="Enter Budget " value="<?= set_value('addl_budget', $addl_budget) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class=" col-6 form-group <?= $validation->hasError('support_infr_amount') ? 'is-invalid' : '' ?>">
                            <label for="Budget Ruppes">Budget Utilized in Ruppes<span class="text-danger">*</span></label>
                            <input type="text" name="support_infr_amount" class="form-control" id="support_infr_amount" placeholder=" Amount" value="<?= set_value('support_infr_amount', $support_infr_amount) ?>">
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row ">
            <div class="col-9"></div>
            <div class="col-3 form-group text-right ">
                <button type="submit" class="btn btn-primary" id="submit">Submit</button>
                <a href="admin/enterprises/cancel" class="btn btn-danger">Cancel</a>
            </div>
        </div>
    </form>
</div>

<!-- GP Modal -->
<div class="modal" id="modal-gps" tabindex="-1" role="dialog" aria-labelledby="modal-small" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title"></h3>
                </div>
                <div class="block-content">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Village Modal -->
<div class="modal" id="modal-villages" tabindex="-1" role="dialog" aria-labelledby="modal-small" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title"></h3>
                </div>
                <div class="block-content">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php js_start(); ?>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

<script>
    var selectedGP, selectedVillage;
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
                            html += '<option value="' + v.id + '">' + v.name + '</option>';
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
                            html += '<option value="' + v.id + '">' + v.name + '</option>';
                        });
                        $('#gps').html(html);
                    }
                },
                error: function() {
                    alert('something went wrong');
                },
                complete: function() {}
            });
        });

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
                            html += '<option value="' + v.id + '">' + v.name + '</option>';
                        });
                        $('#villages').html(html);
                    }
                },
                error: function() {
                    alert('something went wrong');
                },
                complete: function() {}
            });
        });

        //get main center on center_type change and sub_center is selected
        $('[name="center_type"]').on('change', function() {
            var d_id = $("#districts").val();
            var b_id = $("#blocks").val();
            var u_id = $("#unit_type").val();
            unit_type = $('#unit_type option:selected').text();
            selected_option = $(this).val();

            // If selected option is sub_center and unit_type in ['CHC','CMSC']
            if (selected_option == 'sub_center' && $.inArray(unit_type, ['CHC', 'CMSC']) !== -1) {
                $.ajax({
                    url: 'admin/enterprises/center',
                    data: {
                        district_id: d_id,
                        block_id: b_id,
                        unit_id: u_id,
                    },
                    type: 'GET',
                    dataType: 'JSON',
                    beforeSend: function() {
                        $('#center_message').text('');
                    },
                    success: function(response) {
                        if (response.main_centers && response.main_centers.length > 0) {
                            var html = '<option value="">Select main center name</option>';
                            $.each(response.main_centers, function(k, v) {
                                html += '<option value="' + v.ent_id + '" >' + v.managing_unit_name + ' (' + v.management_unit_type + ')</option>';
                            });
                            $('#main_center_list').empty().html(html);
                        } else {
                            $('#center_message').text(response.message);
                        }
                    },
                    error: function() {
                        alert('something went wrong');
                    },
                    complete: function() {

                    }
                });
            }
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
       
        // if village and gp is avaliable then disable address block
        $('#gp_vlg_available').on('change', function() {
            $gp_vlg_checked = $(this).prop('checked');
            if ($gp_vlg_checked) {
                $('#dis_adrs').hide();
            } else {
                $('#dis_adrs').show();
            }
        });
        //if village and gp not avaliable then enable address block and disable gp and village block
        $('#gp_vlg_not_available').on('change', function() {
            $address_check = $(this).prop('checked');
            if ($address_check) {
                $('#gp_vlg').hide();
            } else {
                $('#gp_vlg').show();
            }
        });

        //hide and show center info

        $('#unit_type').on('change', function() {
            var unit_type = $(this).find('option:selected').text().toLowerCase();
            if (unit_type == "chc" || unit_type == "cmsc") {
                $('#center_info').show();

            } else {
                $('#center_info').hide();
            }
        });


        //populate unit type on document ready
        $('#unit_type').trigger("change");

        //Hide center dropdown if main center is selected
        if ($("#main_center").prop("checked")) {

            $('#main_center_list').hide();
        }

        // Show center dropdown when sub center is checked
        if ($("#sub_center").prop(":checked")) {
            $('#main_center_list').show();
        }

        $('#sub_center').on('change', function() {
            $sub_center_checked = $(this).prop('checked');
            if ($sub_center_checked) {
                $('#main_center_list').show();
            } else {
                $('#main_center_list').hide();
            }
        });

        $('#main_center').on('change', function() {
            $main_center_checked = $(this).prop('checked');
            if ($main_center_checked) {
                $('#main_center_list').hide();
            }
        });

        //budget head will 4.1 after choosing unit type chc
        $('#unit_type,#budget_fin_yr_id').on('change', function() {

            var unit_type = $('#unit_type').find('option:selected').text().toLowerCase();
            var budget_code = $('#budget_fin_yr_id').val();

            if (unit_type === "chc" && budget_code >= 3) {
                $('#unit_budget').val(4.1);
                $('#unit_budget').prop('readonly', true);
            } else if (unit_type === "cmsc" && budget_code >= 3) {
                $('#unit_budget').val('3.1.2');
                $('#unit_budget').prop('readonly', true);
            } else {
                $('#unit_budget').val('');
                $('#unit_budget').prop('readonly', false);
            }

        });

        //add new equipment row
        var rows = <?php echo $rows; ?>;
        $('#equipment_row').on('click', function(e) {
            html = '<tr>';
            html += '<td>';
            html += '<select name="equipments[' + rows + ']" class="form-control">';
            <?php foreach ($equipments as $key => $equipment) { ?>
                html += '<option value="<?= $key ?>"><?= $equipment ?></option>';
            <?php } ?>
            html += '</select>';
            html += '</td>';
            html += '<td>';
            html += '<input type="text" class="form-control" name="quantity[' + rows + ']">'
            html += '</td>';
            html += '<td>';
            html += '<button type="button" class="btn-sm btn btn-danger btn-remove btn pull-right" onclick="return confirm(\'Are you sure?\')" ><i class="fa fa-trash"></i></button>';
            html += '</td>';
            html += '</tr>';

            $('#equipments #footer').before(html);
            rows++;
        });

        //delete equipment row
        $("#equipments").on('click', '.btn-remove', function() {
            $(this).closest('tr').remove();
        });

        //add gp btn click
        $('#btn-add-gp').click(function(e) {
            e.preventDefault();
            //check if block_id is selected
            if ($('#blocks').val() == 0) {
                alert('Please select a block');
                return false;
            }

            block = $('#blocks').val();

            //make ajax request to get gp list
            $.ajax({
                url: 'admin/enterprises/getlgdgps',
                data: {
                    block_id: block
                },
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#modal-gps').modal('show');
                    $('#modal-gps').LoadingOverlay('show');
                },
                success: function(data) {
                    $('#modal-gps').find('.block-title').text(data.title);
                    $('#modal-gps').find('.block-content').html(data.html);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(thrownError);
                },
                complete: function() {
                    $('#modal-gps').LoadingOverlay('hide', true);
                }
            });

        });

        // update select2 on #modal-gps close
        $('#modal-gps').on('hidden.bs.modal', function() {
            //populate gps again
            $('#blocks').trigger('change');

            $('#gps').val(selectedGP);
            $('#gps').trigger('change');
            $('#modal-gps').find('.block-content').html('');
        });

        // update select2 on #modal-villages close
        $('#modal-villages').on('hidden.bs.modal', function() {
            //populate villages again
            $('#gps').trigger('change');

            $('#villages').val(selectedVillage);
            $('#villages').trigger('change');
            $('#modal-villages').find('.block-content').html('');
        });

        //add village btn click
        $('#btn-add-village').click(function(e) {
            e.preventDefault();
            //check if block_id is selected
            if ($('#gps').val() == 0) {
                alert('Please select a GP');
                return false;
            }

            gp_id = $('#gps').val();

            //make ajax request to get gp list
            $.ajax({
                url: 'admin/enterprises/getlgdvillages',
                data: {
                    gp_id: gp_id
                },
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#modal-villages').modal('show');
                    $('#modal-villages').LoadingOverlay('show');
                },
                success: function(data) {
                    $('#modal-villages').find('.block-title').text(data.title);
                    $('#modal-villages').find('.block-content').html(data.html);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(thrownError);
                },
                complete: function() {
                    $('#modal-villages').LoadingOverlay('hide', true);
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
            return this.optional(element) || /^(\d+(\.\d+)*)?$/.test(value);
        }, "Please enter decimal number ");
        jQuery.validator.addMethod("rupees", function(value, element) {
            return this.optional(element) || /^(16\d{0,14}(?:\.\d{1,2})?|\d+(?:\.\d{1,2})?)$/.test(value);
        }, "Please enter rupees (ex-12.00) ");
        jQuery.validator.addMethod("ddrequired", function(value, element) {
            return this.optional(element) || (parseFloat(value) > 0);
        }, "* This is a required field");
        jQuery.validator.addMethod("mobile", function(value, element) {
            return this.optional(element) || /([0-9]{11}$)|(^[5-9][0-9]{9}$)/.test(value);
        }, "Please enter a valid mobile number");
    });

    $(document).ready(function() {
        $("#establishmentform").validate({
            ignore: [],
            rules: {
                managing_unit_name: {
                    required: true,
                    lettersonly: true
                },
                unit_id: {
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
                gp_id: {
                    required: function(element) {
                        return $("#gp_vlg_available").is(":selected");
                    },
                    ddrequired: true
                },
                village_id: {
                    required: function(element) {
                        return $("#gp_vlg_available").is(":selected");
                    },
                    ddrequired: true
                },
                address: {
                    required: function(element) {
                        return $("#gp_vlg_not_available").is(":selected");
                    },
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
                own_share: {
                    required: true,
                    rupees: true
                },
                unit_budget: {
                    required: true,
                    decimal: true
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
                purpose_infr_support: {
                    required: function(element) {
                        return $("#is_support_basis_infr").is(":checked");
                    },
                    lettersonly: true
                },
                addl_budget: {
                    required: function(element) {
                        return $("#is_support_basis_infr").is(":checked");
                    },
                    decimal: true
                },
                support_infr_amount: {
                    required: function(element) {
                        return $("#is_support_basis_infr").is(":checked");
                    },
                    rupees: true
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
                    rupees: "Please enter rupees (ex-00.00) "
                },
                unit_budget: {
                    decimal: "Please enter only decimal numbers."
                },
                purpose_infr_support: {
                    lettersonly: "Please enter only letters and spaces."
                },
                addl_budget: {
                    decimal: "Please enter only decimal numbers."
                },
                support_infr_amount: {
                    rupees: "Please enter rupees (ex-00.00) "
                },
            },
            errorPlacement: function(error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent('.input-group'));
                } else {
                    error.insertAfter(element);
                }
            },
        });
        $("select").on("select2:close", function(e) {
            $(this).valid();
        });
    });
</script>
<?php js_end(); ?>