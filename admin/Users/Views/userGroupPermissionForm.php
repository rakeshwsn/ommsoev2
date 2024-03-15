<?php
$validation = \Config\Services::validation();
$formAction = base_url();
$i = 1;
?>

<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title"><?php echo $text_form; ?></h3>
        <div class="block-options">
            <button type="submit" form="form-usergroup" class="btn btn-primary">Save</button>
            <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="Cancel" class="btn btn-primary">Cancel</a>
        </div>
    </div>
    <div class="block-content">
        <?php echo form_open_multipart('', 'id="form-usergroup"'); ?>
        <table id="" class="table table-striped table-bordered table-hover dataTable no-footer">
            <thead>
                <tr>
                    <th class="col-lg-1">Sl No</th>
                    <th class="col-lg-3">Module Name</th>
                    <th class="col-lg-1">Add</th>
                    <th class="col-lg-1">Edit</th>
                    <th class="col-lg-1">Delete</th>
                    <th class="col-lg-1">View</th>
                    <th class="col-lg-1">Download</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $permissionTable = [];
                $permissionTableStatus = [];
                $permissionCheckBox = [];
                $permissionCheckBoxVal = [];

                foreach ($gpermissions as $data) {
                    $permissionCheckBox[$data->name] = $data->active;
                    $permissionCheckBoxVal[$data->name] = $data->id;

                    if (strpos($data->name, '_edit') === false && strpos($data->name, '_view') === false && strpos($data->name, '_delete') === false && strpos($data->name, '_add') === false && strpos($data->name, '_download') === false) {
                        $permissionTable[] = [
                            'name' => $data->name,
                            'description' => $data->description,
                            'status' => $data->active
                        ];
                        $permissionTableStatus[$data->name] = $data->active;
                    }
                }
                ?>
                <?php foreach ($permissionTable as $permission): ?>
                    <tr>
                        <td data-title="#">
                            <?php
                            $checked = isset($permissionTableStatus[$permission['name']]) && $permissionTableStatus[$permission['name']] == 'yes' ? 'checked' : '';
                            $disabled = isset($permissionTableStatus[$permission['name']]) && $permissionTableStatus[$permission['name']] != 'yes' ? 'disabled' : '';
                            ?>
                            <input type="checkbox" name="<?php echo $permission['name']; ?>" value="<?php echo $permissionCheckBoxVal[$permission['name']]; ?>" <?php echo $checked; ?> id="<?php echo $permission['name']; ?>" onclick="processCheck(this);">
                        </td>
                        <td data-title="Module Name">
                            <?php echo $permission['description']; ?>
                        </td>
                        <?php for ($j = 0; $j < 6; $j++): ?>
                            <td data-title="<?php echo uc_words(str_replace('_', ' ', array_keys($permissionCheckBox)[$i + $j])); ?>">
                                <?php
                                $permissionName = array_keys($permissionCheckBox)[$i + $j];
                                $checked = isset($permissionCheckBox[$permissionName]) && $permissionCheckBox[$permissionName] == 'yes' ? 'checked' : '';
                                $disabled = isset($permissionCheckBox[$permissionName]) && $permissionCheckBox[$permissionName] != 'yes' ? 'disabled' : '';
                                ?>
                                <input type="checkbox" name="<?php echo $permissionName; ?>" value="<?php echo $permissionCheckBoxVal[$permissionName]; ?>" <?php echo $checked; ?> id="<?php echo $permissionName; ?>" <?php echo $disabled; ?>>
                            </td>
                        <?php endfor; ?>
                    </tr>
                    <?php $i += 6; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript">
    function processCheck(checkbox) {
        const id = checkbox.id;
        const checkboxes = document.querySelectorAll('input[id^="' + id + '"]');

        checkboxes.forEach((input) => {
            input.checked = checkbox.checked;
            input.disabled = !checkbox.checked;
        });
    }
</script>
