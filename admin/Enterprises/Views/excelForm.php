<table class="table table-bordered">
    <tr>
        <td colspan="11" style="text-align: center; font-weight: bold; background-color:#EFA105">
            <div>
                <h4 >Enterprise Transaction Machinaries Data</h4>
            </div>
        </td>
    </tr>

    <?php foreach ($trans as $tran) { ?>
        <?php $slno = 1; ?>
        <thead>
            <tr>
                <th colspan="11" style="text-align: center; background-color:#EFEF05; font-weight: bold; "><?= $tran['unit_name'] ?></th>

            </tr>
            <tr>
                <th style="text-align: center; background-color:#848403 ;border: 5px solid black; font-weight: bold;">Shg_id</th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">slno</th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Block</th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">GP</th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Village</th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Name SHG </th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">No. of Days Functional</th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Quintals of Produce processed</th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Charges per Quintal</th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Total Expenditure in the fortnight</th>
                <th style="text-align: center; background-color:#848403;border: 5px solid black; font-weight: bold; ">Total Tourn Over in the fortnight</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tran['enterprises'] as $enterprise) { ?>
                <tr>
                    <td><?= $enterprise->id ?></td>
                    <td><?= $slno++ ?></td>
                    <td><?= $enterprise->block ?></td>
                    <td><?= $enterprise->grampanchayat ?></td>
                    <td><?= $enterprise->villages ?></td>
                    <td><?= $enterprise->shg_name ?></td>
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