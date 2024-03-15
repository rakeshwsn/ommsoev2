<?php
$validation = \Config\Services::validation();
?>
<div class="row">
    <div class="col-xl-12">
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title"><?php echo $text_form; ?></h3>
                <div class="block-options">
                    <button type="submit" name="submit" data-toggle="tooltip" title="Save" class="btn btn-danger" form="form-component"><i class="fa fa-save"></i></button>
                    <label class="btn btn-primary" for="cancel-button">Cancel</label>
                    <a id="cancel-button" href="<?php echo $cancel; ?>" data-toggle="tooltip" title="Cancel" class="d-none"></a>
                </div>
            </div>
            <div class="block-content">
                <?php echo form_open_multipart('',array('class' => 'form-horizontal', 'id' => 'form-component', 'name' => 'form-component', 'autocomplete' => 'off', 'novalidate')) ?>
                <div class="form-group row">
                    <label class="col-sm-2 control-label" for="input-status">Fund Agency</label>
                    <div class="col-sm-10">
                        <?php echo form_dropdown('fund_agency_id', option_array_values($fund_agencies, 'fund_agency_id', 'fund_agency'), set_value('fund_agency_id', $fund_agency_id), array('id' => 'fund_agency_id', 'class' => 'form-control', 'required' => 'required', 'autofocus' => 'autofocus', 'aria-required' => 'true'))?>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label" for="input-category">Year</label>
                    <div class="col-sm-10">
                        <?php echo form_dropdown('year', option_array_value($years, 'id', 'name'), set_value('year', $year), array('id' => 'year', 'class' => 'form-control', 'required' => 'required', 'aria-required' => 'true')) ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 control-label" for="input-status">District</label>
                    <div class="col-sm-10">
                        <?php echo form_dropdown('district_id', option_array_value($districts, 'id', 'name', ['0' => 'Select District']), set_value('district_id', $district_id), array('id' => 'district_id', 'class' => 'form-control', 'required' => 'required', 'aria-required' => 'true')) ?>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label" for="input-status">Block</label>
                    <div class="col-sm-10">
                        <?php echo form_dropdown('block_id', array(), set_value('block_id', $block_id), array('id' => 'block_id', 'class' => 'form-control', 'aria-required' => 'true')) ?>
                    </div>
                </div>

            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php js_start(); ?>
<script type="text/javascript">
    jQuery(function(){
        Codebase.helpers(['select2']);
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
                            if (v.id == '<?php echo $block_id; ?>') {
                                html += ' selected="selected"';
                            }
                            html += '>' + v.name + '</option>';
                        })
                        
                    } else {
                        html += '<option value="0" selected="selected">Select Block</option>';
                    }

                    $('
