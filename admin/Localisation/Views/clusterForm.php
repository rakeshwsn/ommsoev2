<?php
$validation = \Config\Services::validation();
$text_form = htmlspecialchars($text_form);
$cancel = htmlspecialchars($cancel);
$name = htmlspecialchars(set_value('name', $name));
$district_id = htmlspecialchars(set_value('district_id', $district_id));
$block_id = htmlspecialchars(set_value('block_id', $block_id));
$gp_id = htmlspecialchars(set_value('gp_id[]', $gp_id));
$errors = $validation->listErrors();
?>

<?php echo form_open_multipart('', 'id="form-district"'); ?>
<div class="row">
	<div class="col-xl-12">
		
		<div class="block">
			<div class="block-header block-header-default">
				<h3 class="block-title"><?php echo $text_form; ?></h3>
				<div class="block-options">
					<button type="submit" name="save" form="form-district" class="btn btn-primary">Save</button>
					<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="Cancel" class="btn btn-primary">Cancel</a>
			
