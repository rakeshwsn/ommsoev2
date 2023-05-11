<?php
$user  = service('user');
//printr($user->getId());
?>
<div class="block">
<form id="formfilter">
    <div class="block">
        <div class="block-header block-header-default">
            <!-- <h3 class="block-title">Data Filter</h3> -->
        </div>
        <?php if (isset($_SESSION['errorupload'])) : ?>
    <div class="alert alert-warning alert-dismissible fade show <?php echo $msgclass ?>" role="alert">
        <?php echo $_SESSION['errorupload']; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
<?php endif; ?>
        <div class="block-header block-header-default">
		<h3 class="block-title"><?php echo $heading_title; ?></h3>
		<div class="block-options">
			<a href="<?php echo $addform; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary">Add Incentive</i></a>
            <?php if(!$user->district_id){?>
			<a href="<?php echo $searchview; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-primary">View All Incentive</i></a>
            <?php }?>
		</div>
	</div>
    </div>
    <div class="block">
   
        <div class="block-content block-content-full">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tr>
                       
                            <th>District</th>
                            <th>Block</th>
                            <th>Year</th>
                            <th>Season</th>
                            <th>Filter</th>
                           
                        </tr>
                        <tr>
                        <?php 
                        if($user->district_id){
                           $main = "disabled";
                        } else{
                            $main = "";
                        }
                        ?>
                            <td>
                            <?php echo form_dropdown('district_id', option_array_value($districts, 'id', 'name',array("0"=>"select District")), set_value('district_id', $user->district_id),"id='district_id' class='form-control select2' $main"); ?>
                            </td>
                            <td>
                            <?php echo form_dropdown('block_id', option_array_value($blocks, 'id', 'name',array("0"=>"Select Block")), set_value('block_id', ''),"id='block_id' class='form-control select2'"); ?>
                            </td>
                            <td>
                                <select class="form-control" id="year" name="year" required>
                                <option value="">select</option>
                                <option value="1">2017-18</option>
                                <option value="2">2018-19</option>
                                <option value="3">2020-21</option>
                                <option value="4">2021-22</option>
                                
                                </select>
                            </td>
                            <td>
                                <select class="form-control" id="season" name="season">
                                    <option value="">select</option>
                                    <?php foreach ($seasons as $key=>$_season) { ?>
                                        <option value="<?=$key?>"><?=$_season?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>
                                <button id="btn-filter" class="btn btn-outline btn-primary"><i class="fa fa-filter"></i> Filter</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
</div>
<div class="block">
	<div class="block-content block-content-full">
		<!-- DataTables functionality is initialized with .js-dataTable-full class in js/datatable/be_tables_datatables.min.js which was auto compiled from _es6/datatable/be_tables_datatables.js -->
		<form action="" method="post" enctype="multipart/form-data" id="form-datatable">
			<table id="datatable_list" class="table table-bordered table-striped table-vcenter js-dataTable-full">
				<thead>
					<tr>
						<th style="width: 1px;" class="text-center no-sort"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></th>
						<th>District</th>
						<th>Block</th>
						<th>Year</th>
						<th>Season</th>
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
        $('select[name=\'district_id\']').trigger('change');
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
					var districtid = $('#district_id').val();
					var blockid = $('#block_id').val();
					var year = $('#year').val();
					var seasonSearch = $('#season').val();
					// Append to data
					data['searchBydistrictId'] = districtid;
					data['searchByblockId'] = blockid;
					data['searchByYear'] = year;
					data['searchBySeason'] = seasonSearch;

				},
				type: "post", // method  , by default get

			},
			
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