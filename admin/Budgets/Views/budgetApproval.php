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
                            <?php echo form_dropdown('year', option_array_values($years, 'id', 'name',[''=>'Select Year']), set_value('year', $year),"id='year' class='form-control js-select2'"); ?> 
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group ">
							<label class="control-label" for="input-name">Fund Agency</label>
							<?php echo form_dropdown('fund_agency_id', option_array_values($fundagencies, 'fund_agency_id', 'fund_agency',[''=>'Select Fund Agency']), set_value('fund_agency_id', ''),"id='fund_agency_id' class='form-control js-select2'"); ?>
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
                            echo form_dropdown('district_id', option_array_values($districts, 'id', 'name',['0'=>'Select District']), set_value('district_id', $active_district), $select_attributes); ?>
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
        </div>
    </div>
    <div class="block-content block-content-full">
        <form action="" method="post" enctype="multipart/form-data" id="form-components">
            <table id="datatable" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                <tr>
                    <th style="width: 1px;" class="text-center no-sort"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></th>
                    <th>Fund Agency</th>
                    <th>Year</th>
                    <th>District</th>
                    <th>Total Budget</th>
                    <th>Status</th>
                    <th class="text-right no-sort">Actions</th>
                </tr>
                </thead>
            </table>
        </form>
    </div>
</div>
<?php js_start(); ?>
<script type="text/javascript"><!--
    
    $(function(){
       
        Codebase.helpers([ 'select2']);
    });

    //--></script>
<?php js_end(); ?>