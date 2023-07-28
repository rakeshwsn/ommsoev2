<?php
$validation = \Config\Services::validation();
?>
<?php echo form_open_multipart('', 'id="form-assign"'); ?>
    <div class="row">

        <div class="col-12">
            <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title"><?php echo $text_form; ?></h3>
                    <div class="block-options">
                        <button type="submit" form="form-assign" class="btn btn-primary">Save</button>
                        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="Cancel" class="btn btn-primary">Cancel</a>
                    </div>
                </div>
                <div class="block-content">
                    <form action="<?=base_url()?>" class="form-horizontal" role="form" method="post" id="roletype">
                        <table id="" class="table table-striped table-bordered table-hover dataTable no-footer">
                            <thead>
                            <tr>
                                <th class="col-lg-2" rowspan="2">Module Name</th>
                                <th class="col-lg-10" colspan="6">Permissions</th>

                            </tr>
                            <tr>
                                <th class="col-lg-2">Index</th>
                                <th class="col-lg-1">Add</th>
                                <th class="col-lg-1">Edit</th>
                                <th class="col-lg-1">Delete</th>
                                <th class="col-lg-1">View</th>
                                <th class="col-lg-6">Miscellaneous</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $prePermission=['index','add','edit','delete','view','mis'];
                                foreach($gpermission as $module=>$permissions){?>
                                    <tr>
                                        
                                        <td data-title="Module Name">
                                            <?php echo $module; ?>
                                        </td>
                                        <?php foreach($prePermission as $action){?>
                                            <td data-title="">
                                            <?php
                                                if(isset($permissions[$action])) {
                                                    foreach($permissions[$action] as $menu){?>
                                                        <div class="custom-control-inline">
                                                            <label class="css-control css-control-primary css-checkbox">
                                                            <input class="css-control-input" type="checkbox" name='<?=$menu->route?>' value="<?=$menu->id?>" <?=$menu->active=="yes"?"checked='checked'":""?>>
                                                            <span class="css-control-indicator" for="example-inline-checkbox1"></span> <?=$menu->description?>
                                                            </label>
                                                        </div>
                                                    <?}
                                                }
                                                ?>
                                            </td>
                                        <?}?>
                                    </tr>
                                <?}?>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php echo form_close(); ?>
<?php js_start(); ?>
    <script type="text/javascript"><!--
        $.fn.processCheck = function() {
            var id = $(this).attr('id');
            if ($('input#'+id).is(':checked')) {
                $(this).parents('tr').find('td[data-id="'+id+'"]').find('input').prop('disabled', false);;
                $(this).parents('tr').find('td[data-id="'+id+'"]').find('input').prop('checked', true);;

            } else {
                $(this).parents('tr').find('td[data-id="'+id+'"]').find('input').prop('disabled', true);;
                $(this).parents('tr').find('td[data-id="'+id+'"]').find('input').prop('checked', false);;

            }
        };
        //--></script>
<?php js_end(); ?>