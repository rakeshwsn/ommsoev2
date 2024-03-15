<div class="block">
	<div class="block-header block-header-default">
		<h3 class="block-title"><?php echo $heading_title; ?></h3>
		<div class="block-options">
			<label class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" />
				<span class="custom-control-label"></span>
			</label>
			<a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
			<button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-district').submit() : false;"><i class="fa fa-trash-o"></i></button>
		</div>
	</div>
	<div class="block-content block-content-full">
		<form id="form-filter" class="form-horizontal">
			<div class="form-layout">
				<div class="row mg-b-25">
					<div class="col-lg-3">
						<div class="form-group mg-b-10-force">
							<label class="form-control-label" for="filter_district">Districts: <span class="tx-danger">*</span></label>
							<?php echo form_dropdown('district_id', option_array_value($districts, 'id', 'name',array('0'=>'Select Districts')), set_value('district_id', ''),"id='filter_district' class='form-control js-select2'"); ?>
						</div>
		
