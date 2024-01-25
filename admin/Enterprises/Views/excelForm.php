<table class="table table-bordered">

    <tr>
        <th>year_id </th>
        <th>dictrict_id</th>
        <th>month_id</th>
        <th>periods</th>
    </tr>
    <tr>
        <td><?= $year_id ?></td>
        <td><?= $district_id ?></td>
        <td><?= $month_id ?></td>
        <td><?= $period ?></td>
    </tr>
    <tr>
        <td colspan="<?php echo (10 + count($columns)); ?>" style="text-align: center; font-weight: bold; background-color:#EFA105">
            <div>
                <h4><?= $heading_title; ?></h4>
            </div>
        </td>
    </tr>
    <?php foreach ($enterprises as $enterprise) { ?>
        <?php $slno = 1; ?>
        <thead>
            <tr>
                <th style="text-align: center; background-color:#EFEF05; font-weight: bold; "><?= $enterprise->unit_id ?></th>
                <th colspan="<?php echo (9 + count($columns)); ?>" style="text-align: center; background-color:#EFEF05; font-weight: bold; "><?= $enterprise->unit_name ?></th>
            </tr>

            <tr>
                <td><?= $enterprise->unit_id ?></td>
                <th style="text-align: center; background-color:#848403 ;border: 5px solid black; font-weight: bold;">ent_id</th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Slno</th>
                <th contenteditable="false" style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Block</th>
                <th>block_id</th>
                <th contenteditable="false" style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">GP</th>
                <th>gp_id</th>
                <th contenteditable="false" style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Village</th>
                <th>village_id</th>
                <th contenteditable="false" style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Name SHG </th>
                <?php foreach ($columns as $key => $column) { ?>
                    <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; "><?= $column ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($enterprises as $enterprise) { ?>
                <tr>
                    <td><?= $enterprise->unit_id ?></td>
                    <td><?= $enterprise->enterprise_id ?></td>
                    <td><?= $slno++ ?></td>
                    <td><?= $enterprise->block ?></td>
                    <td><?= $enterprise->block_id ?></td>
                    <td><?= $enterprise->grampanchayat ?></td>
                    <td><?= $enterprise->gp_id ?></td>
                    <td><?= $enterprise->village ?></td>
                    <td><?= $enterprise->village_id ?></td>
                    <td><?= $enterprise->shg_name ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            <?php } ?>
        </tbody>
    <?php } ?>
</table>