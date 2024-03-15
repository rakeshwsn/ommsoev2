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
                    <label class="col-sm-2 control-label" for="input-status">Row Type</label>
                    <div class="col-sm-10">
                        <?php echo form_dropdown('row_type', array('heading'=>'Heading', 'component' => 'Component'), set_value('row_type', $row_type), "id='row_type' class='form-control'")?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 control-label" for="input-category">Category</label>
                    <div class="col-sm-10">
                        <?php echo form_dropdown('category', $categories, set_value('category', $category), "id='category' class='form-control'")?>
                    </div>
                </div>
                <div class="form-group row <?=$validation->hasError('description')?'is-invalid':''?>">
                    <label class="col-sm-2 control-label" for="input-name">Component Name</label>
                    <div class="col-sm-10">
                        <?php echo form_textarea(array('class'=>'form-control','name' => 'description', 'id' => 'description', 'placeholder'=>'Component Name','value' => set_value('description', $description))); ?>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('description'); ?></div>
                    </div>
                </div>
                <div class="form-group row <?=$validation->hasError('slug')?'is-invalid':''?>">
                    <label class="col-sm-2 control-label" for="input-name">Component Slug</label>
                    <div class="col-sm-10">
                        <?php echo form_input(array('class'=>'form-control','name' => 'slug', 'id' => 'slug', 'placeholder'=>'Component slug','value' => set_value('slug', $slug))); ?>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('slug'); ?></div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label" for="input-name">Component Tag Name</label>
                    <div class="col-sm-10">
                        <?php echo form_input(array('class'=>'form-control js-tags-input','name' => 'tags', 'id' => 'tags', 'data-height'=>"34px", 'placeholder'=>'Component tags','value' => set_value('tags', $tags))); ?>
                        <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('tags'); ?></div>
                    </div>
                </div>

            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php js_start(); ?>
<script type="text/javascript">
    jQuery(function(){ Codebase.helpers(['tags-inputs']); });
    $(document).ready(function() {
        $('#description').keyup( function(e) {
            $('#slug').val($(this).val().toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9\-_]/g, ''))
        });
    });
</script>
<script type="text/javascript"><!--
    
    //--></script>
<?php js_end(); ?>

