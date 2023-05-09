<?php
$validation = \Config\Services::validation();
?>
<div class="row">
    <div class="col-xl-12">
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title"><?php echo $text_form; ?></h3>
                <div class="block-options">
                    <button type="submit" data-toggle="tooltip" title="" class="btn btn-danger" form="form-component"><i class="fa fa-save"></i></button>
                    <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="" class="btn btn-primary"><i class="fa fa-reply"></i></a>
                </div>
            </div>
            <div class="block-content">
                <?php echo form_open_multipart('',array('class' => 'form-horizontal', 'id' => 'form-component','role'=>'form')); ?>
                <div class="form-group row">
                    <label class="col-sm-2 control-label" for="input-status">Fund Agency</label>
                    <div class="col-sm-10">
                        <?php echo form_dropdown('fund_agency_id', option_array_values($fund_agencies, 'fund_agency_id', 'fund_agency'), set_value('fund_agency_id', $fund_agency_id), "id='fund_agency_id' class='form-control'")?>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label" for="input-category">Year</label>
                    <div class="col-sm-10">
                        <?php echo form_dropdown('year', option_array_value($years, 'id', 'name'), set_value('year', $year),"id='year' class='form-control'"); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 control-label" for="input-status">District</label>
                    <div class="col-sm-10">
                        <?php echo form_dropdown('district_id', option_array_value($districts, 'id', 'name',['0'=>'Select District']), set_value('district_id', $district_id), "id='district_id' class='form-control'")?>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label" for="input-status">Block</label>
                    <div class="col-sm-10">
                        <?php echo form_dropdown('block_id', array(), set_value('block_id', $block_id), "id='block_id' class='form-control'")?>
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
    
</script>
<?php js_end(); ?>

