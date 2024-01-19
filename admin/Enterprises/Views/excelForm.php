<table class="table table-bordered">
    <tr>
        <th>year_id </th>
        <th>dictrict_id</th>
        <th>month_id</th>
        <th>periods</th>
    </tr>
    <tr>
        <td><?= $year_id?></td>
        <td><?= $district_id?></td>
        <td><?=$month_id ?></td>
        <td><?=$period ?></td>
    </tr>
    <tr>
        <td colspan="17" style="text-align: center; font-weight: bold; background-color:#EFA105">
            <div>
                <h4><?= $heading_title; ?></h4>
            </div>
        </td>
    </tr>
    <?php foreach ($trans as $tran) { ?>
        <?php $slno = 1; ?>
        <thead>
            <tr>
                <th style="text-align: center; background-color:#EFEF05; font-weight: bold; "><?= $tran['id'] ?></th>
                <th colspan="16" style="text-align: center; background-color:#EFEF05; font-weight: bold; "><?= $tran['unit_name'] ?></th>
            </tr>
            <tr>
                <td><?= $tran['id'] ?></td>
                <th style="text-align: center; background-color:#848403 ;border: 5px solid black; font-weight: bold;">ent_id</th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Slno</th>
                <th contenteditable="false" style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Block</th>
                <th>block_id</th>
                <th contenteditable="false" style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">GP</th>
                <th>gp_id</th>
                <th contenteditable="false" style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Village</th>
                <th>village_id</th>
                <th contenteditable="false" style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Name SHG </th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">No. of Days Functional</th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Quintals of Produce processed</th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Charges per Quintal</th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Total Expenditure in the fortnight</th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Total Turn Over in the fortnight</th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">No. of times under maintenance</th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">No. of event attend</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tran['enterprises'] as $enterprise) { ?>
                <tr>
                    <td><?= $tran['id'] ?></td>
                    <td><?= $enterprise->id ?></td>
                    <td><?= $slno++ ?></td>
                    <td><?= $enterprise->block ?></td>
                    <td><?= $enterprise->block_id ?></td>
                    <td><?= $enterprise->grampanchayat ?></td>
                    <td><?= $enterprise->gp_id ?></td>
                    <td><?= $enterprise->villages ?></td>
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