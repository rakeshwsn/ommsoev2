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
            <div class="block-content tab-content">
                <div class="tab-pane active" id="general" role="tabpanel">
                   <!-- <div class="form-group row required">
						<label class="col-md-2 control-label" for="input-meta-keywords">District</label>
						<div class="col-md-10">
							<?php echo form_dropdown('district_id', option_array_value($districts, 'id', 'name',array("0"=>"select District")), set_value('district_id', @$district_id),"id='district_id' class='form-control js-select2'"); ?>
                        </div>
					</div>  *?
					<div class="form-group row required">
						<label class="col-md-2 control-label" for="input-meta-keywords">Block</label>
						<div class="col-md-10">
							<?php echo form_dropdown('block_id', option_array_value($blocks, 'id', 'name',array("0"=>"Select Block")), set_value('block_id', @$block_id),"id='block_id' class='form-control select2'"); ?>
                        </div>
					</div>

                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="example-hf-email">Year</label>
                        <div class="col-lg-10 <?=$validation->hasError('year')?'is-invalid':''?>">
                        <select class="form-control" id="" name="caste">
                               
                                <option value="">select</option>
                                <option value="1" <?php if(@$year == "1"){?> selected="selected" <?php }?>>2022-23</option>
                                <option value="2" <?php if(@$year == "2"){?> selected="selected" <?php }?>>2023-24</option>
                                <option value="3" <?php if(@$year == "3"){?> selected="selected" <?php }?>>2024-25</option>
                       </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="example-hf-email">Season</label>
                        <div class="col-lg-10 <?=$validation->hasError('season')?'is-invalid':''?>">
                        <select class="form-control" id="" name="season">
                               
                                <option value="">select</option>
                                <option value="1" <?php if(@$season == "1"){?> selected="selected" <?php }?>>kharif</option>
                                <option value="2" <?php if(@$season == "2"){?> selected="selected" <?php }?>>rabi</option>
                               
                       </select>
                        </div>
                    </div> -->

                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="example-hf-email">GP</label>
                        <div class="col-lg-10 <?=$validation->hasError('gp')?'is-invalid':''?>">
                            <input type="hidden" name="id" id="id" value="<?=$id?>"/>
                            <?php echo form_input(array('class'=>'form-control','name' => 'gp', 'id' => 'gp', 'placeholder'=>'GP','value' => set_value('gp', $gp))); ?>
                            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('gp'); ?></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="example-hf-email">Village</label>
                        <div class="col-lg-10 <?=$validation->hasError('village')?'is-invalid':''?>">
                            <input type="hidden" name="id" id="id" value="<?=$id?>"/>
                            <?php echo form_input(array('class'=>'form-control','name' => 'village', 'id' => 'village', 'placeholder'=>'village','value' => set_value('village', $village))); ?>
                            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('village'); ?></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="example-hf-email">Name</label>
                        <div class="col-lg-10 <?=$validation->hasError('name')?'is-invalid':''?>">
                            <input type="hidden" name="id" id="id" value="<?=$id?>"/>
                            <?php echo form_input(array('class'=>'form-control','name' => 'name', 'id' => 'name', 'placeholder'=>'Name','value' => set_value('name', $name))); ?>
                            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('name'); ?></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="example-hf-email">Spouse Name</label>
                        <div class="col-lg-10 <?=$validation->hasError('spouse_name')?'is-invalid':''?>">
                            <input type="hidden" name="id" id="id" value="<?=$id?>"/>
                            <?php echo form_input(array('class'=>'form-control','name' => 'spouse_name', 'id' => 'spouse_name', 'placeholder'=>'spouse name','value' => set_value('spouse_name', $spouse_name))); ?>
                            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('village'); ?></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="example-hf-email">Gender</label>
                        <div class="col-lg-10 <?=$validation->hasError('spouse_name')?'is-invalid':''?>">
                        <select class="form-control" id="" name="gender">
                               
                               <option value="male" <?php if($gender == "male"){?> selected="selected" <?php }?>>Male</option>
                               <option value="female" <?php if($gender == "female"){?> selected="selected" <?php }?>>Female</option>
                               <option value="others" <?php if($gender == "others"){?> selected="selected" <?php }?>>Others</option>
                        
                       </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="example-hf-email">Caste</label>
                        <div class="col-lg-10 <?=$validation->hasError('spouse_name')?'is-invalid':''?>">
                        <select class="form-control" id="" name="caste">
                               
                               <option value="st" <?php if($caste == "st"){?> selected="selected" <?php }?>>ST</option>
                               <option value="sc" <?php if($caste == "sc"){?> selected="selected" <?php }?>>SC</option>
                               <option value="obc" <?php if($caste == "obc"){?> selected="selected" <?php }?>>OBC</option>
                               <option value="general" <?php if($caste == "general"){?> selected="selected" <?php }?>>GENERAL</option>
                        
                       </select>
                        </div>
                    </div>
                   
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="example-hf-email">Phone No</label>
                        <div class="col-lg-10 <?=$validation->hasError('phone_no')?'is-invalid':''?>">
                            <span>phone no must be 10 character and Number only(with out adding +91)</span>
                            <input type="hidden" name="id" id="id" value="<?=$id?>"/>
                            <?php echo form_input(array('class'=>'form-control','name' => 'phone_no', 'id' => 'phone_no', 'placeholder'=>'Phone','value' => set_value('phone_no', $phone_no))); ?>
                            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('phone_no'); ?></div>
                            <span id="phonevalid"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="example-hf-email">Aadhar No</label>
                        <div class="col-lg-10 <?=$validation->hasError('aadhar_no')?'is-invalid':''?>">
                        <span>Aadhar no must be 12 character and Number only</span>
                            <input type="hidden" name="id" id="id" value="<?=$id?>"/>
                            <?php echo form_input(array('class'=>'form-control','name' => 'aadhar_no', 'id' => 'aadhar_no', 'placeholder'=>'aadhar no','value' => set_value('aadhar_no', $aadhar_no))); ?>
                            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('aadhar_no'); ?></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="example-hf-email">Year Support</label>
                        <div class="col-lg-10 <?=$validation->hasError('year_support')?'is-invalid':''?>">
                        <select class="form-control" id="" name="year_support">
                               
                               <option value="1st" <?php if($year_support == "1st"){?> selected="selected" <?php }?>>1st</option>
                               <option value="2nd" <?php if($year_support == "2nd"){?> selected="selected" <?php }?>>2nd</option>
                               <option value="3rd" <?php if($year_support == "3rd"){?> selected="selected" <?php }?>>3rd</option>
                             
                       </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="example-hf-email">Demonstration Area in Hectare</label>
                        <div class="col-lg-10 <?=$validation->hasError('area_hectare')?'is-invalid':''?>">
                            <input type="hidden" name="id" id="id" value="<?=$id?>"/>
                            <?php echo form_input(array('class'=>'form-control','name' => 'area_hectare', 'id' => 'area_hectare', 'placeholder'=>'Demonstration Area in Hectare','value' => set_value('area_hectare', $area_hectare))); ?>
                            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('area_hectare'); ?></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="example-hf-email">Bank Name</label>
                        <div class="col-lg-10 <?=$validation->hasError('bank_name')?'is-invalid':''?>">
                            <input type="hidden" name="id" id="id" value="<?=$id?>"/>
                            <?php echo form_input(array('class'=>'form-control','name' => 'bank_name', 'id' => 'bank_name', 'placeholder'=>'Bank Name','value' => set_value('bank_name', $bank_name))); ?>
                            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('bank_name'); ?></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="example-hf-email">Account No</label>
                        <div class="col-lg-10 <?=$validation->hasError('bank_name')?'is-invalid':''?>">
                            <input type="hidden" name="id" id="id" value="<?=$id?>"/>
                            <?php echo form_input(array('class'=>'form-control','name' => 'account_no', 'id' => 'account_no', 'placeholder'=>'Account No','value' => set_value('account_no', $account_no))); ?>
                            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('bank_name'); ?></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="example-hf-email">Ifsc Code</label>
                        <div class="col-lg-10 <?=$validation->hasError('ifsc')?'is-invalid':''?>">
                            <input type="hidden" name="id" id="id" value="<?=$id?>"/>
                            <?php echo form_input(array('class'=>'form-control','name' => 'ifsc', 'id' => 'ifsc', 'placeholder'=>'Ifsc Code','value' => set_value('ifsc', $ifsc))); ?>
                            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('ifsc'); ?></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="example-hf-email">Amount</label>
                        <div class="col-lg-10 <?=$validation->hasError('amount')?'is-invalid':''?>">
                            <input type="hidden" name="id" id="id" value="<?=$id?>"/>
                            <?php echo form_input(array('class'=>'form-control','name' => 'amount', 'id' => 'amount', 'placeholder'=>'Amount','value' => set_value('amount', $amount))); ?>
                            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('amount'); ?></div>
                        </div>
                    </div>

                </div>
               
                </div>

            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php js_start(); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('select[name=\'district_id\']').bind('change', function() {
            district_id = $(this).val()
            $.ajax({
                url: '<?php echo admin_url("district/block"); ?>/' + district_id,
                dataType: 'json',
                beforeSend: function() {
                },
                complete: function() {
                    //$('.wait').remove();
                },
                success: function(json) {

                    html = '<option value="0">Select Block</option>';

                    if (json['block'] != '') {
                        for (i = 0; i < json.length; i++) {
                            html += '<option value="' + json[i]['id'] + '"';
                            html += '>' + json[i]['name'] + '</option>';
                        }
                    } else {
                        html += '<option value="0" selected="selected">Select Block</option>';
                    }

                    $('select[name=\'block_id\']').html(html);
                    $('select[name=\'block_id\']').select2();
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        });

        Codebase.helpers([ 'select2']);
    });
    </script>
    <script>
        $(document).ready(function(){

            var phone = /^\d{10}$/ ;
             var aadhar = /^\d{12}$/;
             var accountno = new RegExp(/^[0-9]{9,18}$/) ;
             var ifsc = new RegExp(/^[A-Z]{4}0[A-Z0-9]{6}$/) ;


            var phone_id = $('#phone_no').val();
            var aadhar_id = $('#aadhar_no').val();
            var account_id = $('#account_no').val();
            var ifsc_id = $('#ifsc').val();
            var area_hectare_id = $('#area_hectare').val();
            //console.log(phone_id);

          if(phone.test(phone_id) == false){
               // $('#phonevalid').text("Please ");
                $('#phone_no').css({ 'background': '#f0e5e5' });
                
          }
          
          if(aadhar.test(aadhar_id) == false){
               // $('#phonevalid').text("Please ");
                $('#aadhar_no').css({ 'background': '#f0e5e5' });
                
          }

          if(accountno.test(account_id) == false){
               // $('#phonevalid').text("Please ");
                $('#account_no').css({ 'background': '#f0e5e5' });
                
          }
          if(ifsc.test(ifsc_id) == false){
               // $('#phonevalid').text("Please ");
                $('#ifsc').css({ 'background': '#f0e5e5' });
                
          }

          if(area_hectare_id ===0 || area_hectare_id > 9.99 || area_hectare_id == ''){

            $('#area_hectare').css({ 'background': '#f0e5e5' });
          }



        })
    </script>
<?php js_end(); ?>