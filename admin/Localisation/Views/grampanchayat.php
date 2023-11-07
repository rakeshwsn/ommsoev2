<div class="block">
	<div class="block-header block-header-default">
		<h3 class="block-title">
			<?= $heading_title; ?>
		</h3>
		<div class="block-options">
<<<<<<< HEAD
			<a href="<?= $add; ?>" data-toggle="tooltip" title="<?= $button_add; ?>"
				class="btn btn-primary ajaxaction"><i class="fa fa-plus"></i></a>

=======
			<a href="<?= $add; ?>" data-toggle="tooltip" title="<?= $button_add; ?>" class="btn btn-primary ajaxaction"><i class="fa fa-plus"></i></a>
			<!-- <button type="button" data-toggle="tooltip" title="<?= $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?= $text_confirm; ?>') ? $('#form-grampanchayat').submit() : false;"><i class="fa fa-trash-o"></i></button> -->
>>>>>>> 50bb8638c0fc840fddfbfdff535d571c0b576a5e
		</div>
	</div>
	<div class="block-content block-content-full">
		<form id="form-filter" class="form-horizontal">
			<div class="form-layout">
				<div class="row mg-b-25">
					<div class="col-lg-3">
						<div class="form-group mg-b-10-force">
							<label class="form-control-label">Districts: <span class="tx-danger">*</span></label>
							<?= form_dropdown('district_id', option_array_value($districts, 'id', 'name', array('0' => 'Select Districts')), set_value('district_id', $district_id), "id='filter_district' class='form-control js-select2'" . ($district_id ? " disabled" : "")); ?>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group mg-b-10-force">
							<label class="form-control-label">Block: <span class="tx-danger">*</span></label>
							<?= form_dropdown('block_id', array(), set_value('block_id', $block_id), "id='filter_block' class='form-control js-select2'" . ($block_id ? " disabled" : "")); ?>
						</div>
					</div>

					<div class="col-lg-3">
						<div class="form-group mg-b-10-force">
							<label class="form-control-label">Grampanchayat: <span class="tx-danger">*</span></label>
							<input type="text" name="name" class="form-control" placeholder="Grampanchayat" id="filter_gp" />
						</div>
					</div>

					<div class="col-lg-3 center">
						<label class="form-control-label">&nbsp;</label>
						<div class="form-layout-footer">
							<button type="button" id="btn-filter" class="btn btn-primary">Filter</button>
							<button type="button" id="btn-reset" class="btn btn-secondary">Reset</button>
						</div>
					</div>
				</div>
			</div>
		</form>


		<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form-grampanchayat">
			<table id="datatable" class="table table-bordered table-striped table-vcenter js-dataTable-full">
				<thead>
					<tr>
						<th style="width: 1px;" class="text-center no-sort"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></th>
						<th>GP Name</th>
						<th>District</th>
						<th>Block</th>
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
		var blockId = "<?= $block_id; ?>"
		$('select[name=\'district_id\']').bind('change', function() {
			$.ajax({
				url: '<?= admin_url("district/block"); ?>/' + this.value,
				dataType: 'json',
				beforeSend: function() {
					//$('select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>');
				},
				complete: function() {
					//$('.wait').remove();
				},
				success: function(json) {
					//console.log(blockId);

					html = '<option value="">Select Block</option>';

					if (json != '') {
						for (var i = 0; i < json.length; i++) {

							html += '<option value="' + json[i]['id'] + '"';

							if (blockId == json[i]['id']) {
								html += ' selected';
							}

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
		$('select[name=\'district_id\']').trigger('change');
	});
	$(function() {
		table = $('#datatable').DataTable({
			"processing": true,
			"serverSide": true,
			"columnDefs": [{
				targets: 'no-sort',
				orderable: false
			}],
			"ajax": {
				url: "<?= $datatable_url; ?>", // json datasource
				type: "post", // method  , by default get
				data: function(data) {
					data.district = $('#filter_district').val();
					data.block = $('#filter_block').val();
					data.grampanchayat = $('#filter_gp').val();
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

	function delete_district(title, id) {

		gbox.show({
			content: '<h2>Delete Manager</h2>Are you sure you want to delete this Manager?<br><b>' + title,
			buttons: {
				'Yes': function() {
					$.post('<?= admin_url('members.delete'); ?>', {
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
<<<<<<< HEAD
	//--></script>
=======
	//-->
</script>
>>>>>>> 50bb8638c0fc840fddfbfdff535d571c0b576a5e
<?php js_end(); ?>