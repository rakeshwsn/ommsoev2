<?php
$validation = \Config\Services::validation();
?>

<div class="block">
    <ul class="nav nav-tabs nav-tabs-block js-tabs-enabled" data-toggle="tabs" role="tablist">
        <?php foreach((array)$fund_agencies as $key => $value) : ?>
            <li id="group-<?php echo $value['fund_agency_id']; ?>" class="nav-item">
                <a class="nav-link <?=($value['fund_agency_id']==$fund_agency_id)?'active':''?>" href="<?php echo admin_url("components/agencyassign/{$value['fund_agency_id']}"); ?>"> <?php echo $value['fund_agency']; ?> </a>
            </li>
        <?php endforeach; ?>

    </ul>
</div>
    <div class="row">
        <div class="col-12">
            <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Components Agency Assignment</h3>
                    <div class="block-options">
                        <button type="submit" form="form-component-agency" class="btn btn-primary">Save</button>
                        <a href="" data-toggle="tooltip" title="Cancel" class="btn btn-primary">Cancel</a>
                    </div>
                </div>
                <div class="block-content">
                    <?php echo form_open_multipart('',array('class' => 'form-horizontal', 'id' => 'form-component-agency','role'=>'form')); ?>
                        <table id="" class="table table-striped table-bordered table-hover dataTable no-footer">
                            <thead>
                            <tr>
                                
                                <th class="">Number</th>
                                <th class="">Name</th>
                                <?php foreach($agency_types as $agency){?>
                                <th class=""><?=$agency->name?></th>
                                <?}?>
                            </tr>
                            </thead>
                            <tbody>
                                <?=$components?>
                                <!--<?php foreach($components as $component){?>
                                    <?php if($component['row_type']=='heading'){?>
                                    <tr data-id="<?=$component['id']?>">
                                        
                                        <td><b><?=$component['number']?></b></td>
                                        <td><b><?=$component['description']?></b></td>
                                        <?php foreach($agency_types as $agency){?>
                                        <td>
                                            <label class="css-control css-control-primary css-checkbox">
                                                <?php echo form_checkbox(array('class'=>'css-control-input agency-'.$agency->id,'data-agency'=>$agency->id,'name' => 'component['.$component['component_id'].'][agency_id][]', 'id'=>'row_'.$component['id'].'_'.$agency->id, 'value' => $agency->id,'onclick'=>'event.preventDefault();','checked' => (in_array($agency->id, $component['agencies']) ? true : false) )); ?>  
                                                <span class="css-control-indicator"></span>
                                            </label>
                                        </td>
                                        <?}?>
                                    </tr>
                                    <?}else{?>
                                        <tr data-id="<?=$component['id']?>">
                                        
                                        <td><?=$component['number']?></td>
                                        <td><?=$component['description']?></td>
                                        <?php foreach($agency_types as $agency){?>
                                        <td>
                                            <label class="css-control css-control-primary css-checkbox">
                                                <?php echo form_checkbox(array('class'=>'css-control-input agency-'.$agency->id,'data-agency'=>$agency->id,'name' => 'component['.$component['component_id'].'][agency_id][]', 'id'=>'row_'.$component['id'].'_'.$agency->id, 'value' => $agency->id,'checked' => (in_array($agency->id, $component['agencies']) ? true : false) )); ?>  
                                                <span class="css-control-indicator"></span>
                                            </label>
                                        </td>
                                        <?}?>
                                    </tr>
                                    <?}?>

                                    <?php if ( ! empty($component['children'])){
                                        foreach($component['children'] as $child){?>
                                            <tr data-parent="<?=$component['id']?>">
                                                
                                                <td><?=$child['number']?></td>
                                                <td><?=$child['description']?></td>
                                                <?php foreach($agency_types as $agency){?>
                                                <td>
                                                    <label class="css-control css-control-primary css-checkbox">
                                                        <?php echo form_checkbox(array('class'=>'css-control-input agency-'.$agency->id,'data-agency'=>$agency->id,'name' => 'component['.$child['component_id'].'][agency_id][]', 'id'=>'row_'.$component['id'].'_'.$agency->id, 'value' => $agency->id,'checked' => (in_array($agency->id, $child['agencies']) ? true : false) )); ?>
                                                        <span class="css-control-indicator"></span>
                                                    </label>
                                                </td>
                                                <?}?>
                                            </tr>
                                        <?}
                                    }?>
                                <?}?>-->
                            </tbody>
                        </table>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>

<?php js_start(); ?>
    <script type="text/javascript"><!--
        
        $(function(){
            $("input").change(function(e){
                parent_tr=$(this).closest('tr');
                parentId=parent_tr.data('parent');
                classname=$(this).attr('class').split(' ')[1];
                $("tr[data-id="+parentId+"]").find("."+classname).prop('checked',$("tr[data-parent="+parentId+"]").find("."+classname).filter(':checked').length>0);
            })
        
        })


        //--></script>
<?php js_end(); ?>