<div class="block">
	<div class="block-header block-header-default">
		<h3 class="block-title"><?php echo $heading_title; ?></h3>
		<div class="block-options">
			<label class="btn btn-primary" for="select_all">
				<input type="checkbox" id="select_all" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" />
				<i class="fa fa-plus"></i> <?php echo $button_add; ?>
			</label>
			<button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-member').submit() : false;">
				<i class="fa fa-trash-o"></i>
			</button>
		</div>
	</div>
	<div class="block-content block-content-full">
		<form action="<?php echo $delete_url; ?>" method="post" id="form-member">
			<table id="member_list" class="table table-bordered table-striped table-vcenter js-dataTable-full">
				<thead>
					<tr>
						<th style="width: 1px;" class="text-center no-sort"><input type="checkbox" name="selected[]" /></th>
						<th>Name</th>
						<th>Designation</th>
						<th>Status</th>
						<th class="text-right no-sort">Actions</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</form>
	</div>
</div>
<?php js_start(); ?>
<script type="text/javascript"><!--
$(function(){
	$('#member_list').DataTable({
		"processing": true,
		"serverSide": true,
		"columnDefs": [
			{ targets: 'no-sort', orderable: false }
		],
		"ajax":{
			url :"<?=$datatable_url?>", // json datasource
			
