<div class="block">
	<div class="block-header block-header-default">
		<h3 class="block-title"><?php echo $heading_title; ?></h3>
		<div class="block-options">
			<label class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" />
				<span class="custom-control-indicator"></span>
			</label>
			<a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
			<button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"
