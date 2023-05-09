
<div class="block">
<?=$filter_panel?>
</div>
<div class="block">
	<div class="block-header block-header-default">
		<h3 class="block-title"><?php echo $heading_title; ?></h3>
		<div class="block-options">
			<a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
			<button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-proceeding').submit() : false;"><i class="fa fa-trash-o"></i></button>
		</div>
	</div>
	<div class="block-content block-content-full">
		<!-- DataTables functionality is initialized with .js-dataTable-full class in js/datatable/be_tables_datatables.min.js which was auto compiled from _es6/datatable/be_tables_datatables.js -->
		<form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-datatable">        		
		<table id="datatable_list" class="table table-bordered table-striped table-vcenter js-dataTable-full">
			<thead>
				<tr>
					<th style="width: 1px;" class="text-center no-sort"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></th>
					<th>Name</th>
                    <th>Date</th>
                    <th>Process</th>
                    <th>Status</th>
					<th class="text-right no-sort">Actions</th>
				</tr>
			</thead>
		</table>
		</form>
	</div>
</div>