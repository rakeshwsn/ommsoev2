<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title">Filter</h3>
    </div>
    <div class="block-content block-content-full">
        <form id="form-filter" class="form-horizontal">
            <div class="form-layout">
                <div class="row mg-b-25">
                    <div class="col-lg-3">
                        <div class="form-group mg-b-10-force">
                            <label class="form-control-label">Year</label>
                            <?php echo form_dropdown('year', option_array_values($years, 'id', 'name',[''=>'Select Year']), set_value('year', ''),"id='year' class='form-control js-select2'"); ?> 
                        </div>
                    </div>
                    
                    <div class="col-md-3">
						<div class="form-group">
							<label class="control-label" for="input-name">District</label>
                            <?php 
                            $select_attributes = array(
                                'class' => 'form-control js-select2',
                                'id' => 'district_id',
                            );
                            if ($active_district) {
                                $select_attributes = array_merge($select_attributes, array('disabled' => 'disabled'));
                            }
                            echo form_dropdown('district_id', option_array_value($districts, 'id', 'name',['0'=>'Select District']), set_value('district_id', $active_district), $select_attributes); ?>
						</div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group ">
							<label class="control-label" for="input-name">Block</label>
							<?php
                            $select_attributes = array(
                                'class' => 'form-control js-select2',
                                'id' => 'block_id',
                            );
                            if ($active_block) {
                                $select_attributes = array_merge($select_attributes, array('disabled' => 'disabled'));
                            }
                             echo form_dropdown('block_id', array(), set_value('block_id', ''),$select_attributes); ?>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group ">
							<label class="control-label" for="input-name">Fund Agency</label>
							<?php echo form_dropdown('fund_agency_id', option_array_values($fundagencies, 'fund_agency_id', 'fund_agency',[''=>'Select Fund Agency']), set_value('fund_agency_id', ''),"id='fund_agency_id' class='form-control js-select2'"); ?>
						</div>
                    </div>	
                   

                    <div class="col-lg-3 center">
                        <label class="form-control-label">&nbsp;</label>
                        <div class="form-layout-footer">
                            <button type="button" id="btn-filter" class="btn btn-primary">Filter</button>
                            <button type="button" id="btn-reset" class="btn btn-alt-secondary">Reset</button>

                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title"><?php echo $heading_title; ?></h3>
        <div class="block-options">
            <a href="<?php echo $bulkbudegt; ?>" data-toggle="tooltip" title="" class="btn btn-primary">Bulk Budget</a>
            <a href="<?php echo $add; ?>" data-toggle="tooltip" title="" class="btn btn-primary"><i class="fa fa-plus"></i></a>
            <button type="button" data-toggle="tooltip" title="" class="btn btn-danger" onclick="confirm('Are you sure to delete !') ? $('#form-budget').submit() : false;"><i class="fa fa-trash-o"></i></button>
        </div>
    </div>
    <div class="block-content block-content-full">
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-components">
            <table id="datatable" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                <tr>
                    <th style="width: 1px;" class="text-center no-sort"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></th>
                    <th>Fund Agency</th>
                    <th>Year</th>
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
<script type="text/javascript"><!--
     $(document).ready(function() {
        $('select[name=\'district_id\']').bind('change', function() {
            $.ajax({
                url: '<?php echo admin_url("district/block"); ?>/' + this.value,
                dataType: 'json',
                beforeSend: function() {
                    //$('select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>');
                },
                complete: function() {
                    //$('.wait').remove();
                },
                success: function(json) {

                    html = '<option value="0">Select Block</option>';

                    if (json) {
                        $.each(json,function (i,v) {
                            html += '<option value="' + v.id + '">' + v.name + '</option>';
                            html += '<option value="' + v.id + '"';
                            if(v.id=="<?=$active_block?>"){
                                html +='selected = "selected"';
                            }
                            html += '>' + v.name + '</option>';
                        })
                    } else {
                        html += '<option value="">Select Block</option>';
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
        table=$('#datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "columnDefs": [
                { targets: [], visible: false },
            ],
            "ajax":{
                url :"<?=$datatable_url?>", // json datasource
                type: "post",  // method  , by default get
                data: function ( data ) {
                    data.year = $('#year').val();
                    data.district_id = $('#district_id').val();
                    data.block_id = $('#block_id').val();
                    data.fund_agency_id = $('#fund_agency_id').val();
                },
                beforeSend: function(){
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

    //--></script>
<?php js_end(); ?>