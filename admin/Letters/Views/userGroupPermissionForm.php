<?php
$validation = \Config\Services::validation();
?>
<?php echo form_open_multipart('', 'id="form-usergroup"'); ?>
    <div class="row">

        <div class="col-12">
            <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title"><?php echo $text_form; ?></h3>
                    <div class="block-options">
                        <button type="submit" form="form-usergroup" class="btn btn-primary">Save</button>
                        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="Cancel" class="btn btn-primary">Cancel</a>
                    </div>
                </div>
                <div class="block-content">
                    <form action="<?=base_url()?>" class="form-horizontal" role="form" method="post" id="roletype">
                        <table id="" class="table table-striped table-bordered table-hover dataTable no-footer">
                            <thead>
                            <tr>
                                <th class="col-lg-1" rowspan="2">Sl No</th>
                                <th class="col-lg-2" rowspan="2">Module Name</th>
                                <th class="col-lg-9" colspan="5">Permissions</th>

                            </tr>
                            <tr>
                                <th class="col-lg-1">Add</th>
                                <th class="col-lg-1">Edit</th>
                                <th class="col-lg-1">Delete</th>
                                <th class="col-lg-1">View</th>
                                <th class="col-lg-8">Miscellaneous</th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php
                            $permissionTable    = array();
                            $permissionCheckBox = array();
                            $permissionCheckBoxVal = array();
                            $permissionMiscellaneous=array();
                            $prePermission=['add','edit','delete','view'];
                            foreach ($gpermissions as $data) {
                                if(strpos($data->name, '_edit') == false && strpos($data->name, '_view') == false && strpos($data->name, '_delete') == false && strpos($data->name, '_add') == false && strpos($data->name, '_') == false) {

                                    $push['name'] = $data->name;
                                    $push['description'] = $data->description;
                                    $push['status'] = $data->active;

                                    array_push($permissionTable, $push);

                                }else {
                                    $parts = explode('_', $data->name);
                                    if(!in_array($parts[1],$prePermission)){
                                        $permissionMiscellaneous[$parts[0]][]=[
                                            'name'=>$data->name,
                                            'description'=>$data->description,
                                            'status'=>$data->active
                                        ];

                                    }

                                }
                                $permissionCheckBox[ $data->name ] = $data->active;
                                $permissionCheckBoxVal[ $data->name ] = $data->id;

                            }
                            //printr($permissionMiscellaneous);
                            //printr($permissionCheckBox);
                            //exit;
                            ?>
                            <?php
                            $i = 1;
                            foreach($permissionTable as $data) {
                                if(isset($permissionCheckBox[$data['name'].'_add']) && isset($permissionCheckBox[$data['name'].'_edit']) && isset($permissionCheckBox[$data['name'].'_delete']) && isset($permissionCheckBox[$data['name'].'_view'])){
                                    $curd=true;
                                    $colspan=0;
                                }else{
                                    $curd=true;
                                    $colspan=5;
                                }
                                ?>
                                <tr>
                                    <td data-title="#">
                                        <?php
                                        //echo $i;
                                        $status = "";
                                        if(isset($permissionCheckBox[$data['name']])) {

                                            if ($permissionCheckBox[$data['name']]=="yes") {
                                                if ($permissionCheckBoxVal[$data['name']]) {
                                                    echo '<label class="css-control css-control-primary css-checkbox">';
                                                    echo "<input type='checkbox' class='css-control-input' name=".$data['name']." value=".$permissionCheckBoxVal[$data['name']]." checked='checked' id=".$data['name']." onClick='$(this).processCheck();'>";
                                                    echo  '<span class="css-control-indicator"></span>';
                                                    echo  '</label>';
                                                }
                                            } else {
                                                if ($permissionCheckBoxVal[$data['name']]) {
                                                    $status = "disabled";
                                                    echo '<label class="css-control css-control-primary css-checkbox">';
                                                    echo "<input type='checkbox' class='css-control-input' name=".$data['name']." value=".$permissionCheckBoxVal[$data['name']]." id=".$data['name']."  onClick='$(this).processCheck();' >";
                                                    echo  '<span class="css-control-indicator"></span>';
                                                    echo  '</label>';
                                                }
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td data-title="Module Name">
                                        <?php echo $data['description']; ?>
                                    </td>
                                    <?php if($curd){?>
                                    <td data-id="<?=$data['name'];?>" data-title="Add">
                                        <?php
                                        if(isset($permissionCheckBox[$data['name'].'_add'])) {
                                            if ($permissionCheckBox[$data['name'].'_add']=="yes") {
                                                if ($permissionCheckBoxVal[$data['name'].'_add']) {
                                                    echo '<label class="css-control css-control-primary css-checkbox">';
                                                    echo "<input type='checkbox' class='css-control-input' name='".$data['name'].'_add'."' value=".$permissionCheckBoxVal[$data['name'].'_add']." checked='checked' id='".$data['name'].'_add'."' ".$status.">";
                                                    echo  '<span class="css-control-indicator"></span>';
                                                    echo  '</label>';
                                                }
                                            } else {
                                                if ($permissionCheckBoxVal[$data['name'].'_add']) {
                                                    echo '<label class="css-control css-control-primary css-checkbox">';
                                                    echo "<input type='checkbox' class='css-control-input' name='".$data['name'].'_add'."' value=".$permissionCheckBoxVal[$data['name'].'_add']." id='".$data['name'].'_add'."' ".$status.">";
                                                    echo  '<span class="css-control-indicator"></span>';
                                                    echo  '</label>';
                                                }
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td data-id="<?=$data['name'];?>" data-title="Edit">
                                        <?php
                                        if(isset($permissionCheckBox[$data['name'].'_edit'])) {
                                            if ($permissionCheckBox[$data['name'].'_edit']=="yes") {
                                                if ($permissionCheckBoxVal[$data['name'].'_edit']) {
                                                    echo '<label class="css-control css-control-primary css-checkbox">';
                                                    echo "<input type='checkbox' class='css-control-input' name='".$data['name'].'_edit'."' value=".$permissionCheckBoxVal[$data['name'].'_edit']." checked='checked' id='".$data['name'].'_edit'."' ".$status.">";
                                                    echo  '<span class="css-control-indicator"></span>';
                                                    echo  '</label>';
                                                }
                                            } else {
                                                if ($permissionCheckBoxVal[$data['name'].'_edit']) {
                                                    echo '<label class="css-control css-control-primary css-checkbox">';
                                                    echo "<input type='checkbox' class='css-control-input' name='".$data['name'].'_edit'."' value=".$permissionCheckBoxVal[$data['name'].'_edit']." id='".$data['name'].'_edit'."' ".$status.">";
                                                    echo  '<span class="css-control-indicator"></span>';
                                                    echo  '</label>';
                                                }
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td data-id="<?=$data['name'];?>" data-title="Delete">
                                        <?php
                                        if(isset($permissionCheckBox[$data['name'].'_delete'])) {
                                            // echo "delete";
                                            if ($permissionCheckBox[$data['name'].'_delete']=="yes") {
                                                if ($permissionCheckBoxVal[$data['name'].'_delete']) {
                                                    echo '<label class="css-control css-control-primary css-checkbox">';
                                                    echo "<input type='checkbox' class='css-control-input' name='".$data['name'].'_delete'."' value=".$permissionCheckBoxVal[$data['name'].'_delete']." checked='checked' id='".$data['name'].'_delete'."' ".$status.">";
                                                    echo  '<span class="css-control-indicator"></span>';
                                                    echo  '</label>';
                                                }
                                            } else {
                                                if ($permissionCheckBoxVal[$data['name'].'_delete']) {
                                                    echo '<label class="css-control css-control-primary css-checkbox">';
                                                    echo "<input type='checkbox' class='css-control-input' name='".$data['name'].'_delete'."' value=".$permissionCheckBoxVal[$data['name'].'_delete']." id='".$data['name'].'_delete'."' ".$status.">";
                                                    echo  '<span class="css-control-indicator"></span>';
                                                    echo  '</label>';
                                                }
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td data-id="<?=$data['name'];?>" data-title="View">
                                        <?php
                                        if(isset($permissionCheckBox[$data['name'].'_view'])) {
                                            if ($permissionCheckBox[$data['name'].'_view']=="yes") {
                                                if ($permissionCheckBoxVal[$data['name'].'_view']) {
                                                    echo '<label class="css-control css-control-primary css-checkbox">';
                                                    echo "<input type='checkbox' class='css-control-input' name='".$data['name'].'_view'."' value=".$permissionCheckBoxVal[$data['name'].'_view']." checked='checked' id='".$data['name'].'_view'."' ".$status.">";
                                                    echo  '<span class="css-control-indicator"></span>';
                                                    echo  '</label>';
                                                }
                                            } else {
                                                if ($permissionCheckBoxVal[$data['name'].'_view']) {
                                                    echo '<label class="css-control css-control-primary css-checkbox">';
                                                    echo "<input type='checkbox' class='css-control-input' name='".$data['name'].'_view'."' value=".$permissionCheckBoxVal[$data['name'].'_view']." id='".$data['name'].'_view'."' ".$status.">";
                                                    echo  '<span class="css-control-indicator"></span>';
                                                    echo  '</label>';
                                                }
                                            }
                                        }
                                        ?>
                                    </td>
                                    <?}?>
                                    <td data-id="<?=$data['name'];?>" colspan="<?=$colspan?>" data-title="Miscellaneous">
                                        <?php
                                        if(isset($permissionMiscellaneous[$data['name']])){
                                            foreach($permissionMiscellaneous[$data['name']] as $misc){?>
                                                <div class="custom-control custom-checkbox custom-control-inline mb-5">
                                                    <label class="css-control css-control-primary css-checkbox">
                                                    <input class="css-control-input" type="checkbox" name='<?=$misc['name']?>' value="<?=$permissionCheckBoxVal[$misc['name']]?>" <?=$permissionCheckBox[$misc['name']]=="yes"?"checked='checked'":""?> <?=$status?>>
                                                    <span class="css-control-indicator" for="example-inline-checkbox1"></span> <?=$misc['description']?>
                                                    </label>
                                                </div>
                                            <?}
                                        }

                                        ?>
                                    </td>
                                </tr>
                                <?php $i++; } ?>
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