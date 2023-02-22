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


            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php js_start(); ?>
<script type="text/javascript">
    jQuery(function(){
        Codebase.helpers(['select2']);
    });
    
</script>
<?php js_end(); ?>

