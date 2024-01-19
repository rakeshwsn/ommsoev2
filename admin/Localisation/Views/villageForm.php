<?php
$validation = \Config\Services::validation();
?>

<?php echo form_open_multipart('', 'id="form-grampanchayat"'); ?>
<div class="row">
	<div class="col-xl-12">

		<div class="block">
			<div class="block-header block-header-default">
				<h3 class="block-title"><?php echo $text_form; ?></h3>
				<div class="block-options">
					<button type="submit" form="form-grampanchayat" class="btn btn-primary">Save</button>
					<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="Cancel" class="btn btn-primary">Cancel</a>
				</div>
			</div>

			<div class="block-content">
				<div class="form-group <?= $validation->hasError('district_id') ? 'is-invalid' : '' ?>">
					<label for="code">District</label>
					<?php echo form_dropdown('district_id', $districts, set_value('district_id', $district_id), "id='district_id' class='form-control js-select2'"); ?>
					<div class="invalid-feedback animated fadeInDown"><?= $validation->getError('district'); ?></div>

				</div>
				<div class="form-group <?= $validation->hasError('block_id') ? 'is-invalid' : '' ?>">
					<label for="code">Block</label>
					<?php echo form_dropdown('block_id', $blocks, set_value('block_id', $block_id), "id='block_id' class='form-control js-select2'"); ?>
					<div class="invalid-feedback animated fadeInDown"><?= $validation->getError('block_id'); ?></div>
				</div>
			
				<div class="form-group <?= $validation->hasError('gp_id') ? 'is-invalid' : '' ?>">
					<label for="code">Grampanchayat</label>
					<?php echo form_dropdown('gp_id', $gps, set_value('gp_id', $gp_id), "id='gp_id' class='form-control js-select2'"); ?>
					<div class="invalid-feedback animated fadeInDown"><?= $validation->getError('gp_id'); ?></div>
				</div>
		
				<div class="form-group <?= $validation->hasError('village') ? 'is-invalid' : '' ?>">
					<label for="name">Village Name</label>
					<?= form_input(array('class'=>'form-control','name' => 'name', 'id' => 'name', 'placeholder'=>'Village Name','value' => set_value('village', $village_name))); ?>

					<div class="invalid-feedback animated fadeInDown"><?= $validation->getError('village'); ?></div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<?php js_start(); ?>
<script type="text/javascript">
	$(document).ready(function() {
		//saraswatee code

		$('#district_id').on('change', function() {

			var d_id = $(this).val(); // Declare d_id with var

			$.ajax({
				url: 'admin/district/block',
				data: {
					district_id: d_id

				},

				type: 'GET',
				dataType: 'JSON',
				beforeSend: function() {},
				success: function(response) {

					if (response.blocks) {

						var html = '<option value="">Select Block</option>'; // Declare html with var
						$.each(response.blocks, function(k, v) {
							html += '<option value="' + v.id + '">' + v.name + '</option>';
						});
						$('#block_id').html(html);
					}
				},
				error: function() {
					alert('something went wrong');
				},
				complete: function() {

				}
			});
		});
		$('#block_id').on('change', function() {
			var b_id = $(this).val();
			$.ajax({
				url: 'admin/blocks/grampanchayat',
				data: {
					block_id: b_id
				},
				type: 'GET',
				dataType: 'JSON',
				beforeSend: function() {},
				success: function(response) {
					// console.log(response);

					if (response.gps) {
						var html = '<option value="">Select GP</option>';
						$.each(response.gps, function(k, v) {
							html += '<option value="' + v.id + '">' + v.name + '</option>';
						});
						$('#gp_id').html(html);
					}
				},
				error: function() {
					alert('something went wrong');
				},
				complete: function() {

				}
			});

		});
	});
</script>
<?php js_end(); ?>