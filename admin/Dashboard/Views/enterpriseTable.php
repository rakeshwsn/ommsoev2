<table class="table">
    <thead>
        <tr>
            <th scope="col">slno</th>
            <th scope="col">Unit Name</th>
            <th scope="col">WSHG</th>
            <th scope="col">FPOs</th>
        </tr>
    </thead>
    <?php $slno=0; foreach ($enterprises as $enterprise)  { $slno++ ?>
        <tbody>

            <tr>
                <td><?= $slno?></td>
                <td><?= $enterprise->unit_name?></td>
                <td><?= $enterprise->total_wshg?></td>
                <td><?= $enterprise->total_fpos ?></td>
            </tr>

        </tbody>
    <?php }  ?>
</table>