<div class="block">
	<div class="block-header block-header-default">
		<h3 class="block-title"><?php echo $heading_title; ?></h3>
		<div class="block-options">
			<button type="button" id="button-add" name="button-add" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></button>
			<button type="button" id="button-delete" name="button-delete" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-district').submit() : false;"><i class="fa fa-trash-o"></i></button>
		</div>
	</div>
	<div class="block-content block-content-full">
		<form id="form-filter" class="form-horizontal">
			<div class="form-layout">
				<div class="row mg-b-25">
					<div class="col-lg-3">
						<div class="form-group mg-b-10-force">
							<label for="filter_district" class="form-control-label">Districts: <span class="tx-danger">*</span></label>
							<?php echo form_dropdown('district_id', option_array_value($districts, 'id', 'name',array('0'=>'Select Districts')), set_value('district_id', ''),"id='filter_district' class='form-control js-select2'"); ?>
						</div>
					</div><!-- col-4 -->
					<div class="col-lg-3">
						<div class="form-group mg-b-10-force">
							<label for="filter_block" class="form-control-label">Block: <span class="tx-danger">*</span></label>
							<?php echo form_dropdown('block_id', array(), set_value('block_id', ''),"id='filter_block' class='form-control js-select2'"); ?>
						</div>
					</div><!-- col-4 -->
					<div class="col-lg-3">
						<div class="form-group mg-b-10-force">
							<label for="filter_gp" class="form-control-label">Grampanchayat: <span class="tx-danger">*</span></label>
							<input type="text" name="name" class="form-control" placeholder="Grampanchayat" id="filter_gp"/>
						</div>
					</div>
					<!-- col-4 -->
					<div class="col-lg-3 center">
						<label for="btn-filter" class="form-control-label">&nbsp;</label>
						<div class="form-layout-footer">
							<button type="button" id="btn-filter" name="btn-filter
