<table class="table table-bordered">
    <tr>
        <td>year_id </td>
        <td>dictrict_id</td>
        <td>month_id</td>
        <td>periods</td>
    </tr>
    <tr>
        <td><?= $year_id ?></td>
        <td><?= $district_id ?></td>
        <td><?= $month_id ?></td>
        <td><?= $period ?></td>
    </tr>

    <?php foreach ($units as $unit) { ?>
        <tr>
            <td colspan="<?php echo (10 + count($columns)); ?>" style="text-align: center; font-weight: bold; background-color:#EFA105">
                <div>
                    <h4><?= $unit['heading_title']; ?></h4>
                </div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center; background-color:#848403 ;border: 5px solid black; font-weight: bold;">ent_id</td>
            <td style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Slno</td>
            <td style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Block</td>
            <td>block_id</td>
            <td style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">GP</td>
            <td>gp_id</td>
            <td style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Village</td>
            <td>village_id</td>
            <td style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Name SHG </td>
            <?php foreach ($columns as $key => $column) { ?>
                <td style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; "><?= $column['label'] ?></td>
            <?php } ?>
        </tr>
        <?php $slno = 1; ?>
        <?php foreach ($unit['enterprises'] as $enterprise) { ?>
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
    <?php } ?>
</table>