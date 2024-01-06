<div class="block">
	<div class="block-header block-header-default">
		<h3 class="block-title"><?php echo $heading_title; ?></h3>
		<div class="block-options">
			<a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
			<button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-equipment').submit() : false;"><i class="fa fa-trash-o"></i></button>
		</div>
	</div>
	<div class="block-content block-content-full">
		<form id="form-filter" class="form-horizontal">
			<div class="form-layout">
				<div class="row mg-b-25">
					<div class="col-lg-4">
						<div class="form-group mg-b-10-force">
						<label class="form-control-label">Center type</label>
							<select name="center_type" id="center_type" class="form-control">
								<option value="0">Select cenetr type</option>
								<option value="chc">CHC</option>
								<option value="cmsc">CMSC</option>
							</select>
						</div>
					</div>
					<div class="col-lg-4 center">
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
		<form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-equipment">
			<table id="datatable" class="table table-bordered table-striped table-vcenter js-dataTable-full">
				<thead>
					<tr>
						<th style="width: 1px;" class="text-center no-sort"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></th>
						<th>Equipment Name</th>
						<th>Center Type</th>
						<th class="text-right no-sort">Actions</th>
					</tr>
				</thead>
			</table>
		</form>
	</div>
</div>
<?php js_start(); ?>
<script type="text/javascript">
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
					data.center_type = $('#center_type').val();

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

	function delete_equipment(title, id) {

		gbox.show({
			content: '<h2>Delete Manager</h2>Are you sure you want to delete this Manager?<br><b>' + title,
			buttons: {
				'Yes': function() {
					$.post('<?php echo admin_url('members.delete'); ?>', {
						user_id: id
					}, function(data) {
						if (data.success) {
							gbox.hide();
							$('#member_list').DataTable().ajax.reload();
						} else {
							gbox.show({
								content: 'Failed to delete this Manager.'
							});
						}
					});
				},
				'No': gbox.hide
			}
		});
		return false;
	}
</script>
<?php js_end(); ?>