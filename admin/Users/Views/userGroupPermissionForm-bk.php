<?php
$validation = \Config\Services::validation();
$saveButtonId = 'save-button';
$formId = 'roletype';
$formAction = base_url();
?>

<form action="<?= $formAction ?>" class="form-horizontal" role="form" method="post" id="<?= $formId ?>">
    <table id="<?= $formId ?>-table" class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
            <tr>
                <th class="col-lg-1" scope="col">Sl No</th>
                <th class="col-lg-3" scope="col">Module Name</th>
                <th class="col-lg-1" scope="col">Add</th>
                <th class="col-lg-1" scope="col">Edit</th>
                <th class="col-lg-1" scope="col">Delete</th>
                <th class="col-lg-1" scope="col">View</th>
                <th class="col-lg-1" scope="col">Download</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $permissionTable = array();
            $permissionCheckBox = array();
            $permissionCheckBoxVal = array();
            foreach ($gpermissions as $data) {
                if (strpos($data->name, '_edit') === false && strpos($data->name, '_view') === false && strpos($data->name, '_delete') === false && strpos($data->name, '_add') === false && strpos($data->name, '_download') === false) {
                    $push['name'] = $data->name;
                    $push['description'] = $data->description;
                    $push['status'] = $data->active;

                    array_push($permissionTable, $push);

                }
                $permissionCheckBox[$data->name] = $data->active;
                $permissionCheckBoxVal[$data->name] = $data->id;

            }
            ?>
            <?php
            $i = 1;
            foreach ($permissionTable as $data) {
                $rowId = "row-" . $i;
                $checkboxId = $data['name'];
                $checkboxAddId = $data['name'] . '_add';
                $checkboxEditId = $data['name'] . '_edit';
                $checkboxDeleteId = $data['name'] . '_delete';
                $checkboxViewId = $data['name'] . '_view';
                $checkboxDownloadId = $data['name'] . '_download';
            ?>
                <tr id="<?= $rowId ?>" aria-labelledby="<?= $checkboxId ?>-label">
                    <td data-title="#">
                        <input type="checkbox" name="<?= $checkboxId ?>" value="<?= $permissionCheckBoxVal[$checkboxId] ?>" id="<?= $checkboxId ?>" <?= ($permissionCheckBox[$checkboxId] == "yes" ? 'checked' : '') ?> aria-checked="true" required autocomplete="off" form="<?= $formId ?>" list="<?= $formId ?>-datalist" aria-required="true" tabindex="0" title="<?= $data['description'] ?>" aria-label="<?= $data['description'] ?>" onclick="$('#' + this.id).processCheck();">
                        <datalist id="<?= $formId ?>-datalist">
                            <option value="<?= $permissionCheckBoxVal[$checkboxId] ?>" label="<?= $data['description'] ?>"></option>
                        </datalist>
                        <label for="<?= $checkboxId ?>" id="<?= $checkboxId ?>-label"><?= $data['description'] ?></label>
                    </td>
                    <td data-title="Module Name">
                        <?= $data['description'] ?>
                    </td>
                    <td data-title="Add">
                        <input type="checkbox" name="<?= $checkboxAddId ?>" value="<?= isset($permissionCheckBoxVal[$checkboxAddId]) ? $permissionCheckBoxVal[$checkboxAddId] : '' ?>" id="<?= $checkboxAddId ?>" <?= (isset($permissionCheckBox[$checkboxAddId]) && $permissionCheckBox[$checkboxAddId] == "yes" ? 'checked' : '') ?> aria-checked="true" required autocomplete="off" form="<?= $formId ?>" list="<?= $formId ?>-datalist" aria
