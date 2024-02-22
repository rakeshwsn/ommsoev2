<!--
added by rakesh N -->
<table class="table">
    <thead>
        <tr style="background-color:#FABC5B;">
            <th scope="col">slno</th>
            <th scope="col">Unit Name</th>
            <th scope="col">WSHG</th>
        </tr>
    </thead>
    <?php $slno=0; foreach ($enterprises as $enterprise)  { $slno++ ?>
        <tbody>

            <tr>
                <td><?= $slno?></td>
                <td><?= $enterprise->unit_name?></td>
                <td><?= $enterprise->total_wshg?></td>
            </tr>

        </tbody>
    <?php }  ?>
</table>