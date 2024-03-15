
<div class="block">
	<div class="block-header block-header-default">
		<h3 class="block-title"><?php echo $heading_title; ?></h3>
		<!-- <div class="block-options">
			<a href="<?php echo $addform; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
			<button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-proceeding').submit() : false;"><i class="fa fa-trash-o"></i></button>
		</div> -->
	</div>
	<div class="block-content block-content-full">
		<!-- DataTables functionality is initialized with .js-dataTable-full class in js/datatable/be_tables_datatables.min.js which was auto compiled from _es6/datatable/be_tables_datatables.js -->
		<form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-datatable">
			<table id="datatable_list" class="table table-bordered table-striped table-vcenter js-dataTable-full">
				<thead>
					<tr>
						<th>Hidden</th>
						<th style="width: 1px;" class="text-center no-sort"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></th>
						<th>Name</th>
						<th>Spouse</th>
						<th>Gender</th>
						<th>Caste</th>
						<th class="text-right no-sort">Actions</th>
					</tr>
				</thead>
			</table>
		</form>
	</div>
</div>


<?php js_start(); ?>


<script type="text/javascript"><!--
    $(document).ready(function() {
        $('select[name=\'district_id\']').bind('change', function() {
            district_id = $(this).val()
            $.ajax({
                url: '<?php echo admin_url("district/block"); ?>/' + district_id,
                dataType: 'json',
                beforeSend: function() {
                },
                complete: function() {
                    //$('.wait').remove();
                },
                success: function(json) {

                    html = '<option value="0">Select Block</option>';

                    if (json['block'] != '') {
                        for (i = 0; i < json.length; i++) {
                            html += '<option value="' + json[i]['id'] + '"';
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

        Codebase.helpers([ 'select2']);
    });
    //--></script>
<script type="text/javascript">
	$(function() {
		$('#datatable_list').DataTable({
			"processing": true,
			"serverSide": true,
			"columnDefs": [
				//{ targets: '_all', orderable: false },
				{
					targets: 0,
					visible: false,
					searchable: false,
				},
			],
			"ajax": {
				url: "<?= $datatable_url ?>", // json datasource
				'data': function(data) {
					// Read values
					//var mainincetiveid = '<?php $mainincetiveid ?>';
					// var districtid = $('#district_id').val();
					// var blockid = $('#block_id').val();
					var year = $('#year').val();
					var seasonSearch = $('#season').val();
					// Append to data
					// data['searchBydistrictId'] = districtid;
					// data['searchByblockId'] = blockid;
					data['mainincetiveid'] = '<?php echo $mainincetiveid ?>';
					//data['searchByYear'] = year;
					//data['searchBySeason'] = seasonSearch;

				},
				type: "post", // method  , by default get

			},
			"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				if (aData[0] == 'true') {
					//console.log("ada");
					$('td', nRow).css('background-color', 'rgb(139, 139, 139)');
					$('td', nRow).css('color', 'white');
				}

			}
		});
	});
</script>

<script>
	$('#btn-filter').on('click', function(e) {
		e.preventDefault();
		$('#datatable_list').DataTable()
			.search(this.value)
			.draw();
	});
</script>




<?php js_end(); ?>