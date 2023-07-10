<?php
$validation = \Config\Services::validation();
?>

<?= form_open_multipart('', 'id="form-district"'); ?>
<div class="row">
	<div class="col-xl-12">
		
		<div class="block">
			<div class="block-header block-header-default">
				<h3 class="block-title"><?= $text_form; ?></h3>
				<div class="block-options">
					<button type="submit" form="form-district" class="btn btn-primary">Save</button>
					<a href="<?= $cancel; ?>" data-toggle="tooltip" title="Cancel" class="btn btn-primary">Cancel</a>
				</div>
			</div>
			
			<div class="block-content">
				<div class="form-group <?=$validation->hasError('district')?'is-invalid':''?>">
					<label for="code">District</label>
					<?= form_dropdown('district_id', option_array_value($districts, 'id', 'name'), set_value('district_id', $district_id),"id='district_id' class='form-control js-select2'"); ?>
					<div class="invalid-feedback animated fadeInDown"><?= $validation->getError('district_id'); ?></div>
							
				</div>
				<div class="form-group <?=$validation->hasError('name')?'is-invalid':''?>">
					<label for="name" >Name</label>
					<?= form_input(array('class'=>'form-control','name' => 'name', 'id' => 'name', 'placeholder'=>'Name','value' => set_value('name', $name))); ?>
					<div class="invalid-feedback animated fadeInDown"><?= $validation->getError('name'); ?></div>		
				</div>	
			</div> 
		</div>
	</div> 
</div>
<?= form_close(); ?>
<?php js_start(); ?>
<script type="text/javascript"><!--
	
//--></script>
<script type="text/javascript"><!--
	
//--></script>

<?php js_end(); ?>