

<table class="table table-bordered">
    <thead>
        <tr>
            <th colspan="16" style="text-align: center; font-weight: bold; background-color:#EFA105">Enterprise Data</th>
        </tr>
        <tr>
            <th style=" font-weight: bold ;">Slno </th>
            <th style=" font-weight: bold ;">Unit Type</th>
            <th style=" font-weight: bold ;">District</th>
            <th style=" font-weight: bold ;">Block</th>
            <th style=" font-weight: bold ;">GP</th>
            <th style=" font-weight: bold ;">Village</th>
            <th style=" font-weight: bold ;">Established Unit Budget Head </th>
            <th style=" font-weight: bold ;">Addl. Support Provided for basic infra. creation</th>
            <th style=" font-weight: bold ;">Type/Purpose of Addl. Infra. Support </th>
            <th style=" font-weight: bold ;">Budget Head utilised for Addl. Infra Support</th>
            <th style=" font-weight: bold ;">Type of Management Unit </th>
            <th style=" font-weight: bold ;">Name of Managing Unit</th>
            <th style=" font-weight: bold ;">Date of Establishment</th>
            <th style=" font-weight: bold ;">Date of MoU</th>
            <th style=" font-weight: bold ;">Name of Contact Person </th>
            <th style=" font-weight: bold ;">Mobile Number</th>


        </tr>
    </thead>
    <tbody>

        <?php $slno = 0;
        foreach ($entdatas as $entdata) {
            $slno++ ?>
            <tr>
                <td><?= $slno ?></td>
                <td><?= $entdata['unit_name'] ?></td>
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