<?php
$validation = \Config\Services::validation();
?>
<?php echo form_open_multipart('', 'id="form-user"'); ?>
<div class="content-heading pt-0">
    <div class="dropdown float-right">
        <button type="submit" form="form-user" class="btn btn-primary">Save</button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="Cancel" class="btn btn-primary">Cancel</a>
    </div>
    <?php echo $text_form; ?>
</div>
<div class="row">
<div class="col-xl-12">
<div class="row">
	<div class="col-xl-12">

        <div class="block">

            <ul class="nav nav-tabs nav-tabs-block" data-toggle="tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="#general">General</a>
                </li>
                <!--<li class="nav-item">
                    <a class="nav-link" href="#assign">Form Assign</a>
                </li>-->
                <!-- <li class="nav-item">
                    <a class="nav-link" href="#account">Account</a>
                </li> -->
            </ul>
            <div class="block-content tab-content">
                <div class="tab-pane active" id="general" role="tabpanel">
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="example-hf-email">Name</label>
                        <div class="col-lg-10 <?=$validation->hasError('user_name')?'is-invalid':''?>">
                            <?php echo form_input(array('class'=>'form-control','name' => 'user_name', 'id' => 'user_name', 'placeholder'=>'Name','value' => set_value('user_name', $user_name))); ?>
                            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('user_name'); ?></div>
                        </div>
                    </div>
					

					<div class="form-group row required">
						<label class="col-md-2 control-label" for="input-email">Email</label>
						<div class="col-md-10 <?=$validation->hasError('user_email')?'is-invalid':''?>">
							<?php echo form_input(array('class'=>'form-control','name' => 'user_email', 'id' => 'input-email', 'placeholder'=>'Email','value' => set_value('user_email', $user_email))); ?>
							<div class="invalid-feedback animated fadeInDown"><?= $validation->getError('user_email'); ?></div>
                        </div>
					</div>
					<div class="form-group row required">
						<label class="col-md-2 control-label" for="input-email">Place</label>
						<div class="col-md-10 <?=$validation->hasError('email')?'is-invalid':''?>">
                        <select class="form-control-sm custom-select px-1 pagesize" title="Select page size" name="user_place">
                                <option selected="selected" >Select</option>
                                <option value="district" <?php echo ($user_place == 'district') ? 'selected' : ''; ?>>District</option>
                                <option value="state" <?php echo ($user_place == 'state') ? 'selected' : ''; ?>>State</option>
                               
                            </select>
                        </div>
					</div>
						
					
                </div>
                

                

            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php js_start(); ?>
<script type="text/javascript"><!--
   
    //--></script>
<?php js_end(); ?>