<?php
$validation = \Config\Services::validation();
?>

<?php echo form_open_multipart('', 'id="form-equipment"'); ?>
<div class="row">
	<div class="col-xl-12">

		<div class="block">
			<div class="block-header block-header-default">
				<h3 class="block-title"><?php echo $text_form; ?></h3>
				<div class="block-options">
					<button type="submit" form="form-equipment" class="btn btn-primary">Save</button>

					<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="Cancel" class="btn btn-danger">Cancel</a>
				</div>
			</div>

			<div class="block-content">
				<div class="form-group <?= $validation->hasError('center_type') ? 'is-invalid' : '' ?>">
					<label for="center_type">Center Type</label>
					<select name="center_type" id="center_type" class="form-control">
						<option value="0">Select cenetr type</option>
						<option value="chc" <?= $center_type == "chc" ? 'selected' : ''; ?>>CHC</option>
						<option value="cmsc" <?= $center_type == "cmsc" ? 'selected' : ''; ?>>CMSC</option>
					</select>
					<div class="invalid-feedback animated fadeInDown"><?= $validation->getError('center_type'); ?></div>
				</div>
				<div class="form-group <?= $validation->hasError('name') ? 'is-invalid' : '' ?>">
					<label for="name">Name</label>
					<?php echo form_input(array('class' => 'form-control', 'name' => 'name', 'id' => 'name', 'placeholder' => 'Name', 'value' => set_value('name', $name))); ?>
					<input type="hidden" name="id" value="<?= $id ?>" />
					<div class="invalid-feedback animated fadeInDown"><?= $validation->getError('name'); ?></div>
				</div>
				<div class="form-group <?= $validation->hasError('tag') ? 'is-invalid' : '' ?>">
					<label for="tag">Tag</label>
					<?php echo form_input(array('class' => 'form-control', 'name' => 'tag', 'id' => 'tag', 'placeholder' => 'Give details  of the equipment like (Pumpset (1.5hp)', 'value' => set_value('tag', $tag))); ?>

					<div class="invalid-feedback animated fadeInDown"><?= $validation->getError('tag'); ?></div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>