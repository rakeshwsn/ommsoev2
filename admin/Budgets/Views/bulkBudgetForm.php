<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Budget</title>
</head>
<body>
    <div class="row">
        <div class="col-xl-12">
            <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Bulk Budget </h3>
                    <img src="path/to/image.jpg" alt="Image description">
                </div>
                <div class="block-content">
                    <?php echo form_open_multipart('',array('class' => 'form-horizontal', 'id' => 'form-budget','name'=>'form-budget','role'=>'form', 'novalidate')); ?>
                    <div class="budgetplan" <?=$details?"disable-div":""?>>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="input-status">Fund Agency</label>
                            <div class="col-sm-10">
                                <?php echo form_dropdown('fund_agency_id', option_array_values($fund_agencies, 'fund_agency_id', 'fund_agency'), set_value('fund_agency_id', $fund_agency_id), array('id' => 'fund_agency_id', 'class' => 'form-control'));?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="input-category">Year</label>
                            <div class="col-sm-10">
                                <?php echo form_dropdown('year', option_array_value($years, 'id', 'name'), set_value('year', $year), array('id' => 'year', 'class' => 'form-control')); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="input-status">District</label>
                            <div class="col-sm-10">
                                <?php 
                                $select_attributes = array(
                                    'class' => 'form-control js-select2',
                                    'id' => 'district_id',
                                );
                                if ($active_district) {
                                    $select_attributes = array_merge($select_attributes, array('readonly' => 'readonly'));
                                }
                                echo form_dropdown('district_id', option_array_value($districts, 'id', 'name',['0'=>'Select District']), set_value('district_id', $district_id), $select_attributes); ?>
                                <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('district_id'); ?></div>
                            </div>
                        </div>

                        <div class="form-group row <?=$validation->hasError('block_id')?'is-invalid':''?>">
                            <label class="col-sm-2 control-label" for="input-status">Block</label>
                            <div class="col-sm-10">
                                <?php echo form_dropdown('block_id[]', array(), set_value('block_id[]', ''), array('id' => 'block_id', 'class' => 'form-control select2 multiple', 'multiple' => 'multiple', 'tabindex' => '5', 'autocomplete' => 'off', 'placeholder' => 'Select Blocks'));?>
                                <div class="invalid-feedback animated fadeInDown"><?= $validation->
