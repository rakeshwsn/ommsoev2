<table class="table table-bordered">
    <tr>
        <td colspan="16" style="text-align: center; font-weight: bold; background-color:#EFA105">Enterprise Data</td>
    </tr>
    <tr>
        <th style="text-align: center; font-weight: bold; background-color:#EFEF05">Slno </th>
        <th style="text-align: center; font-weight: bold; background-color:#EFEF05">Unit Type</th>
        <th style="text-align: center; font-weight: bold; background-color:#EFEF05">District</th>
        <th style="text-align: center; font-weight: bold; background-color:#EFEF05">Block</th>
        <th style="text-align: center; font-weight: bold; background-color:#EFEF05">GP</th>
        <th style="text-align: center; font-weight: bold; background-color:#EFEF05">Village</th>
        <th style="text-align: center; font-weight: bold; background-color:#EFEF05">Established Unit Budget Head </th>
        <th style="text-align: center; font-weight: bold; background-color:#EFEF05">Addl. Support Provided for basic infra. creation</th>
        <th style="text-align: center; font-weight: bold; background-color:#EFEF05">Type/Purpose of Addl. Infra. Support </th>
        <th style="text-align: center; font-weight: bold; background-color:#EFEF05">Budget Head utilised for Addl. Infra Support</th>
        <th style="text-align: center; font-weight: bold; background-color:#EFEF05">Type of Management Unit </th>
        <th style="text-align: center; font-weight: bold; background-color:#EFEF05">Name of Managing Unit</th>
        <th style="text-align: center; font-weight: bold; background-color:#EFEF05">Date of Establishment</th>
        <th style="text-align: center; font-weight: bold; background-color:#EFEF05">Date of MoU</th>
        <th style="text-align: center; font-weight: bold; background-color:#EFEF05">Name of Contact Person </th>
        <th style="text-align: center; font-weight: bold; background-color:#EFEF05">Mobile Number</th>


    </tr>

    <tbody>

        <?php $slno = 0;
        foreach ($entdatas as $entdata) {
            $slno++ ?>
            <tr>
                <td><?= $slno ?></td>
                <td><?= $entdata['unit_name']?></td>
                <td><?= $entdata['districts'] ?></td>
                <td><?= $entdata['blocks'] ?></td>
                <td><?= $entdata['gps'] ?></td>
                <td><?= $entdata['villages'] ?></td>
                <td><?= $entdata['unit_budget'] ?></td>
                <td><?= $entdata['purpose_infr_support'] ?></td>
                <td><?= $entdata['addl_budget'] ?></td>
                <td><?= $entdata['support_infr_amount'] ?></td>
                <td><?= $entdata['management_unit_type'] ?></td>
                <td><?= $entdata['managing_unit_name'] ?></td>
                <td><?= $entdata['date_estd'] ?></td>
                <td><?= $entdata['mou_date'] ?></td>
                <td><?= $entdata['contact_person'] ?></td>
                <td><?= $entdata['contact_mobile'] ?></td>
            </tr>
        <?php     } ?>
    </tbody>

</table>