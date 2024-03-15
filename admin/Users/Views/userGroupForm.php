<?php
$validation = \Config\Services::validation();
$nameId = 'name';
$descriptionId = 'description';
$agencyId = 'input-agency';
$statusId = 'input-status';
$formId = 'form-usergroup';
$cancelId = 'cancel-button';
?>

<form id="<?php echo $formId; ?>" action="" method="post" autocomplete="off" novalidate>
    <div class="row">
        <div class="col-xl-12">
            <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title"><?php echo $text_form; ?></h3>
                    <div class="block-options">
                        <button type="submit" form="<?php echo $formId; ?>" class="btn btn-primary">Save</button>
                        <button id="<?php echo $cancelId; ?>" name="cancel" type="button" class="btn btn-primary" data-toggle="tooltip" title="Cancel">Cancel</button>
                    </div>
                </div>
                <div class="block-content">
                    <div class="form-group row">
                        <label for="<?php echo $nameId; ?>" class="col-lg-2 col-form-label">Name</label>
                        <div class="col-lg-10 <?=$validation->hasError('name')?'is-invalid':''?>">
                            <input type="hidden" name="id" id="id" value="<?=$id?>"/>
                            <input type="text" class="form-control" id="<?php echo $nameId; ?>" name="name" placeholder="Name" value="<?php echo set_value('name', $name); ?>" autofocus required minlength="1" maxlength="255" aria-describedby="<?php echo $nameId; ?>-error" aria-required="true"/>
                            <div id="<?php echo $nameId; ?>-error" class="invalid-feedback animated fadeInDown"><?= $validation->getError('name'); ?></div>
                        </div>
                    </div>

                    <div class="form-group row required">
                        <label for="<?php echo $descriptionId; ?>" class="col-sm-2 control-label">Designation</label>
                        <div class="col-md-10 <?=$validation->hasError('designation')?'is-invalid':''?>">
                            <input type="text" class="form-control" id="<?php echo $descriptionId; ?>" name="description" placeholder="Description" value="<?php echo set_value('description', $description); ?>" required minlength="1" maxlength="255" aria-describedby="<?php echo $descriptionId; ?>-error" aria-required="true"/>
                            <div id="<?php echo $descriptionId; ?>-error" class="invalid-feedback animated fadeInDown"><?= $validation->getError('description'); ?></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="<?php echo $agencyId; ?>" class="col-sm-2 control-label">Agency</label>
                        <div class="col-md-10">
                            <?php  echo form_dropdown('agency', array('1'=>'Yes','0'=>'No'), set_value('agency',$agency),array('class'=>'form-control','id' => $agencyId)); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="<?php echo $statusId; ?>" class="col-sm-2 control-label">Status</label>
                        <div class="col-md-10">
                            <?php  echo form_dropdown('status', array('1'=>'Enable','0'=>'Disable'), set_value('status',$status),array('class
