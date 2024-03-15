<?php
$validation = \Config\Services::validation();
?>
<?php echo form_open_multipart('', 'id="form-member"'); ?>
<div class="row">
    <div class="col-xl-12">
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title"><?php echo $text_form; ?></h3>
                <div class="block-options">
                    <button type="submit" form="form-member" class="btn btn-primary">Save</button>
                    <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="Cancel" class="btn btn-primary">Cancel</a>
                </div>
            </div>
            <div class="block-content">
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label" for="example-hf-email">Name</label>
                    <div class="col-lg-10 <?=$validation->hasError('name')?'is-invalid':''?>">
                        <?php echo form_input(array('class'=>'form-control','name' => 'name', 'id' => 'name', 'placeholder'=>'Name','value' => set_value('name', $name))); ?>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('name'); ?></div>
                    </div>
                </div>
                <div class="form-group row required">
                    <label class="col-sm-2 control-label" for="input-member-group">District</label>
                    <div class="col-md-10 <?=$validation->hasError('district_id')?'is-invalid':''?>">
                        <?php echo form_dropdown('district_id', option_array_value($districts, 'id', 'name'), set_value('district_id', $district_id),"id='district_id' class='form-control js-select2'"); ?>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('district_id'); ?></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label" for="example-hf-email">Location</label>
                    <div class="col-lg-10 <?=$validation->hasError('location')?'is-invalid':''?>">
                        <?php echo form_input(array('class'=>'form-control','name' => 'location', 'id' => 'location', 'placeholder'=>'Location','value' => set_value('location', $location))); ?>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('location'); ?></div>
                    </div>
                </div>
                <div class="form-group row required">
                    <label class="col-sm-2 control-label" for="input-member-group">Designation</label>
                    <div class="col-md-10 <?=$validation->hasError('designation')?'is-invalid':''?>">
                        <?php echo form_dropdown('designation', $designations, set_value('designation', $designation),"id='input-designation' class='form-control js-select2'"); ?>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('designation'); ?></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label" for="example-hf-email">Designation Name</label>
                    <div class="col-lg-10 <?=$validation->hasError('designation_name')?'is-invalid':''?>">
                        <?php echo form_input(array('class'=>'form-control','name' => 'designation_name', 'id' => 'designation_name', 'placeholder'=>'Designation Name','value' => set_value('designation_name', $designation_name))); ?>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('designation_name'); ?></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label" for="example-hf-email">Email</label>
                    <div class="col-lg-10 <?=$validation->hasError('email')?'is-invalid':''?>">
                        <?php echo form_input(array('class'=>'form-control','name' => 'email', 'id' => 'email', 'placeholder'=>'Email','value' => set_value('email', $email))); ?>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('email'); ?></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label" for="example-hf-email">Mobile</label>
                    <div class="col-lg-10 <?=$validation->hasError('mobile')?'is-invalid':''?>">
                        <?php echo form_input(array('class'=>'form-control','name' => 'mobile', 'id' => 'mobile', 'placeholder'=>'Mobile','value' => set_value('mobile', $mobile))); ?>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('mobile'); ?></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label" for="example-hf-email">Head of Nodal District</label>
                    <div class="col-lg-10 <?=$validation->hasError('nodal_district')?'is-invalid':''?>">
                        <?php echo form_dropdown('nodal_district[]', option_array_value($districts, 'id', 'name'), set_value('nodal_district[]', $nodal_district),"id='nodal_district' class='form-control js-select2' multiple='multiple'"); ?>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('nodal_district'); ?></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 control-label" for="input-nodal_state">Head of Nodal State</label>
                    <div class="col-md-10">
                        <?php  echo form_dropdown('nodal_state', array('1'=>'Yes','0'=>'No'), set_value('nodal_state',$nodal_state),array('class'=>'form-control','id' => 'input-nodal_state')); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 control-label" for="input-status">Status</label>
                    <div class="col-md-10">
                        <?php  echo form_dropdown('status', array('1'=>'Enable','0'=>'Disable'), set_value('status',$status),array('class'=>'form-control','id' => 'input-status')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php js_start(); ?>
<script type="text/javascript"><!--
    $(function () {
        Codebase.helpers([ 'select2']);
    });
    //--></script>
<?php js_end(); ?>