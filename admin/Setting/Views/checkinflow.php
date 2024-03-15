<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title float-left"><?php echo $heading_title; ?></h3>
                <div class="panel-tools float-right">
                    <form id="form-setting" class="form-horizontal" method="post" action="<?php echo site_url('settings/save'); ?>">
                        <button type="submit" name="save" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-danger"><i class="fa fa-save"></i></button>
                        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-primary"><i class="fa fa-reply"></i></a>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs tabs" role="tablist">
                    <!-- Tab navigation links -->
                </ul>
                <div class="tab-content">
                    <div class="tab-pane show active" id="phone" role="tabpanel" aria-labelledby="phone-tab">
                        <div class="form-group row">
                            <label for="checkin_phone_label" class="col-sm-3 control-label">Display Text</label>
                            <div class="col-sm-9">
                                <?php echo form_input(array('class'=>'form-control','name' => 'checkin_phone_label', 'id' => 'checkin_phone_label', 'placeholder'=>"Display Text",'value' => $checkin_phone_label)); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="checkin_phone_otp" class="col-sm-3 control-label">OTP Verification</label>
                            <div class="col-sm-9">
                                <?php echo form_checkbox(array('name' => 'checkin_phone_otp', 'id' => 'checkin_phone_otp', 'value' => 'true','checked' => ($checkin_phone_otp == 'true' ? true : false))); ?>
                            </div>
                        </div>
                    </div>
                    <!-- Other tab panels -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php js_start(); ?>
<script type="text/javascript">
    var address_row = <?php echo $address_row; ?>;
    var value_row = <?php echo $value_row; ?>;

    document.addEventListener('DOMContentLoaded', function() {
        var addFieldButton = document.querySelector('.optionbtn');
        addFieldButton.addEventListener('click', addField);

        var addressTable = document.querySelector('#address-table');
        addressTable.addEventListener('click', function(event) {
            if (event.target.matches('.input-group-text i.fa-minus-circle')) {
                var row = event.target.closest('tr');
                row.remove();
            }
        });
    });

    function addField() {
        value_row = 0;
        var html = '<tr id="address-row' + address_row + '">';
        html += '	<td><input type="text" name="checkin_address_field[' + address_row + '][name]" value="" class="form-control" /></td>';
        html += '  	<td><select name="checkin_address_field[' + address_row + '][type]" class="form-control">';
        <?php foreach($fieldTypes as $key=>$value):?>
            html += '    	<option value="<?php echo $key;?>"><?php echo $value;?></option>';
        <?php endforeach;?>
        html += '  </select></td>';
        html += '	<td class="moreoption">';
        html += '		<div class="input-group mb-2" id="value-row' + address_row + value_row +'">';
        html += '   		<input type="text" name="checkin_address_field[' + address_row + '][value][]" value="" class="form-control" />';
        html += '   		<div class="input-group-append">'
        html += '       		<span class="input-group-text"><i class="fa fa-minus-circle"></i></span>';
        html += '    		</div>';
        html += '		</div>';
        html += '		<button type="button" onclick="addValues(this,'+address_row+')" data-toggle="tooltip" title="add value" class="btn btn-danger optionbtn">Add Values</button></td>';
        html += '	<td><input type="checkbox" name="checkin_address_field[' + address_row + '][required]" value="true" class="form-control" /></td>';
        html += '  	<td><button type="button" onclick="$(\'#address-row' + address_row + '\').remove();
