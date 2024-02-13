<style>
    th {
        text-align: center;
        font-weight: bold;
        background-color: #EFEF05;
    }
</style>

<table class="table table-bordered">
    <tr>
        <td colspan="16" style="text-align: center; font-weight: bold; background-color:#EFA105">Enterprise Data</td>
    </tr>
    <tr>
        <th>Slno </th>
        <th>Unit Type</th>
        <th>District</th>
        <th>Block</th>
        <th>GP</th>
        <th>Village</th>
        <th>Established Unit Budget Head </th>
        <th>Addl. Support Provided for basic infra. creation</th>
        <th>Type/Purpose of Addl. Infra. Support </th>
        <th>Budget Head utilised for Addl. Infra Support</th>
        <th>Type of Management Unit </th>
        <th>Name of Managing Unit</th>
        <th>Date of Establishment</th>
        <th>Date of MoU</th>
        <th>Name of Contact Person </th>
        <th>Mobile Number</th>


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