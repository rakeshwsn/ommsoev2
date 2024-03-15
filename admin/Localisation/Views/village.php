<div class="block">
	<div class="block-header block-header-default">
		<h3 class="block-title"><?php echo $heading_title; ?></h3>
		<div class="block-options">
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
							<label class="form-control-label">Districts: <span class="tx-danger">*</span></label>
							<?php echo form_dropdown('district_id', option_array_value($districts, 'id', 'name', array('0' => 'Select Districts')), set_value('district_id', ''), "id='filter_district' class='form-control js-select2'"); ?>
						</div>
					</div><!-- col-4 -->
					<div class="col-lg-3">
						<div class="form-group mg-b-10-force">
							<label class="form-control-label">Block: <span class="tx-danger">*</span></label>
							<?php echo form_dropdown('block_id', array(), set_value('block_id', ''), "id='filter_block' class='form-control js-select2'"); ?>
						</div>
					</div><!-- col-4 -->
					<div class="col-lg-3">
						<div class="form-group mg-b-10-force">
							<label class="form-control-label">Grampanchayat: <span class="tx-danger">*</span></label>
							<?php echo form_dropdown('gp_id', array(), set_value('gp_id', ''), "id='filter_grampanchayat' class='form-control js-select2'"); ?>
						</div>
					</div><!-- col-4 -->
					<div class="col-lg-3">
						<div class="form-group mg-b-10-force">
							<label class="form-control-label">Village: <span class="tx-danger">*</span></label>
							<input type="text" name="name" class="form-control" placeholder="Village" id="filter_village" />

						</div>
					</div>
					<!-- col-4 -->
					<div class="col-lg-3 center">
						<label class="form-control-label">&nbsp;</label>
						<div class="form-layout-footer">
							<button type="button" id="btn-filter" class="btn btn-primary">Filter</button>
							<button type="button" id="btn-reset" class="btn btn-secondary">Reset</button>
						</div><!-- form-layout-footer -->
					</div>
				</div><!-- row -->
			</div>
		</form>
		<hr />
		<!-- DataTables functionality is initialized with .js-dataTable-full class in js/district/be_tables_datatables.min.js which was auto compiled from _es6/district/be_tables_datatables.js -->
		<form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-grampanchayat">
			<table id="datatable" class="table table-bordered table-striped table-vcenter js-dataTable-full">
				<thead>
					<tr>
						<th style="width: 1px;" class="text-center no-sort"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></th>
						<th>Village Name</th>
						<th>District</th>
						<th>Block</th>
						<th>GP</th>
						<th class="text-right no-sort">Actions</th>
					</tr>
				</thead>
			</table>
		</form>
	</div>
</div>
<?php js_start(); ?>
<script type="text/javascript">
	$(document).ready(function() {

		//saraswatee code
		$('#filter_district').on('change', function() {

			var d_id = $(this).val(); // Declare d_id with var

			$.ajax({
				url: 'admin/districts/block',
				data: {
					district_id: d_id

				},

				type: 'GET',
				dataType: 'JSON',
				beforeSend: function() {},
				success: function(response) {

					if (response) {

						var html = '<option value="">Select Block</option>'; // Declare html with var
						$.each(response, function(k, v) {
							html += '<option value="' + v.id + '">' + v.name + '</option>';
						});
						$('#filter_block').html(html);
					}
				},
				error: function() {
					alert('something went wrong');
				},
				complete: function() {

				}
			});
		});
		$('#filter_block').on('change', function() {
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
						$('#filter_grampanchayat').html(html);
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
	//
	$(function() {
		table = $('#datatable').DataTable({
			"processing": true,
			"serverSide": true,
			"columnDefs": [{
				targets: 'no-sort',
				orderable: false
			}],
			"ajax": {
				url: "<?= $datatable_url ?>", // json datasource
				type: "post", // method  , by default get
				data: function(data) {
					data.district = $('#filter_district').val();
					data.block = $('#filter_block').val();
					data.grampanchayat = $('#filter_grampanchayat').val();
					data.village = $('#filter_village').val();
				},
				beforeSend: function() {
					$('.alert-dismissible, .text-danger').remove();
					$("#datatable_wrapper").LoadingOverlay("show");
				},
				complete: function() {
					$("#datatable_wrapper").LoadingOverlay("hide");
				},
				error: function() { // error handling
					$(".datatable_error").html("");
					$("#datatable").append('<tbody class="datatable_error"><tr><th colspan="5">No data found.</th></tr></tbody>');
					$("#datatable_processing").css("display", "none");

				},
				dataType: 'json'
			}
		});
		$('#btn-filter').click(function() { //button filter event click
			table.ajax.reload(); //just reload table
		});
		$('#btn-reset').click(function() { //button reset event click
			$('#form-filter')[0].reset();
			table.ajax.reload(); //just reload table
		});

		Codebase.helpers(['select2']);
	});
</script>
<?php js_end(); ?>