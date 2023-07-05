<?php
$validation = \Config\Services::validation();
?>
<div class="row">
    <div class="col-xl-12">
        <div class="block">
            <div class="block-header block-header-default bg-success">
                <h3 class="block-title"><?= $heading_title; ?></h3>
            </div>
			<div class="block-header-content" style="display:flex;padding:20px 0 20px 0">
				<div class="col-md-3">
                <label>From Date</label>
				<input type="text"  class="form-control" value="31.07.23" readonly>
				</div>
				<div class="col-md-3">
				<label>To Date</label>
				<input type="text" readonly value="31.07.23" class="form-control">
				</div>
				<div class="col-md-2 mt-4">
					<a href="http://ommsoev2.local//templates/area_coverage_template.xlsx" class="btn btn-square btn-info min-width-125 mb-10"><i class="fa fa-download mr-5"></i> Download</a>
				</div>
				<div class="col-md-2 mt-4">
					<form class="dm-uploader" id="uploader">
						<div role="button" class="btn btn-outline btn-warning">
							<i class="fa fa-folder-o fa-fw"></i> Upload Excel
							<input type="file" title="Click to add Files">
						</div>
					</form>	
				</div>		
			</div>
           
        </div>
    </div>
    <div class="col-xl-12">
        <div class="block">
            <div class="block-header block-header-default  bg-primary">
                <h3 class="block-title"> Area Coverage History</h3>
            </div>
			
            <div class="block-content">
                <table class="table table-vcenter text-center">
                    <thead>
                        <tr>
                            <th>Week</th>
                            <th>Total Farmer</th>
                            <th>Total Area</th>
                            <th>Upload Status</th>
                            <th>Date Added</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="display: flex;">
                               
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>	
<?php js_start(); ?>

<script type="text/javascript">
$(document).ready(function() {
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
				
				html = '<option value="">Select Block</option>';
		
				if (json['block'] != '') {
					for (i = 0; i < json['block'].length; i++) {
						html += '<option value="' + json['block'][i]['id'] + '"';

						html += '>' + json['block'][i]['name'] + '</option>';
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
$(function(){
    $('#area_coverage_form').on('shown.bs.modal', function() {
        $("#year_id").select2();
    }); 
})     
    $(function(){
	table=$('#datatable').DataTable({
		"processing": true,
		"serverSide": true,
		"columnDefs": [
			{ targets: 'no-sort', orderable: false }
		],
		"ajax":{
			url :"<?=$datatable_url?>", // json datasource
			type: "post",  // method  , by default get
			data: function ( data ) {
				data.district = $('#filter_district').val();
				data.block = $('#filter_block').val();
				data.grampanchayat = $('#filter_gp').val();
			},
			beforeSend: function(){
				$('.alert-dismissible, .text-danger').remove();
				$("#datatable_wrapper").LoadingOverlay("show");
			},
			complete: function(){
				$("#datatable_wrapper").LoadingOverlay("hide");
			},
			error: function(){  // error handling
				$(".datatable_error").html("");
				$("#datatable").append('<tbody class="datatable_error"><tr><th colspan="5">No data found.</th></tr></tbody>');
				$("#datatable_processing").css("display","none");
				
			},
			dataType:'json'
		}
	});
	$('#btn-filter').click(function(){ //button filter event click
		table.ajax.reload();  //just reload table
	});
	$('#btn-reset').click(function(){ //button reset event click
		$('#form-filter')[0].reset();
		table.ajax.reload();  //just reload table
	});
	
	Codebase.helpers([ 'select2']);
});
</script>
<?php js_end(); ?>    


