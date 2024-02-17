<table class="table table-bordered border-dark border-3">
    <table class="minimalistBlack mg-b-10 table-bordered1 table-striped1 border-dark" border="1">
        <thead>
            <tr>
                <th style="background-color:#e3a11e;font-weight: bold; text-align: center; " colspan="10">
                    <h4>MPR Of Enterprises Established</h4>
                </th>
            </tr>
            <tr>
                <?php if ($district_id) { ?>
                    <th colspan="10" style="background-color:#D5F107 ;font-weight: bold; text-align: center; ">
                        District: <?= $district_text; ?> ||
                    <?php  } ?>
                    <?php if ($block_id) { ?>

                        Block: <?= $block_text; ?> ||
                    <?php  } ?>
                    <?php if ($year_id) { ?>

                        Year: <?= $year_name_text; ?> ||
                    <?php  } ?>
                    <?php if ($month_id) { ?>

                        Month: <?= $month_text; ?> ||
                    <?php  } ?>
                    <?php if ($management_unit_type) { ?>

                        Unit: <?= $managementunit_text; ?> </th>
                <?php  } ?>
            </tr>
            <tr class="subhead">
                <th style="background-color:#1ee3a1;font-weight: bold; text-align: center;border: 1px solid black " rowspan="3">Type of Unit </th>
                <th style="background-color:#1ee3a1;font-weight: bold; text-align: center;border: 1px solid black " colspan="3">No. of Functional Unit </th>
                <th style="background-color:#1ee3a1;font-weight: bold; text-align: center;border: 1px solid black " colspan="6">Transaction Data </th>
            </tr>
            <tr class="subhead">
                <th style="background-color:#1ee3a1;font-weight: bold; text-align: center;border: 1px solid black " rowspan="2">Up to Previous Month </th>
                <th style="background-color:#1ee3a1;font-weight: bold; text-align: center;border: 1px solid black " rowspan="2">During the Month </th>
                <th style="background-color:#1ee3a1;font-weight: bold; text-align: center;border: 1px solid black " rowspan="2">Cumulative </th>
                <th style="background-color:#1ee3a1;font-weight: bold; text-align: center;border: 1px solid black " colspan="2">Up to Previous Month </th>
                <th style="background-color:#1ee3a1;font-weight: bold; text-align: center;border: 1px solid black " colspan="2">During the Month </th>
                <th style="background-color:#1ee3a1;font-weight: bold; text-align: center;border: 1px solid black " colspan="2">Cumulative </th>

            </tr>
            <tr class="subhead">
                <th style="background-color:#1ee3a1;font-weight: bold; text-align: center;border: 1px solid black ">Total Turnover (in Rs)</th>
                <th style="background-color:#1ee3a1;font-weight: bold; text-align: center;border: 1px solid black ">Total Income (in Rs)</th>
                <th style="background-color:#1ee3a1;font-weight: bold; text-align: center;border: 1px solid black ">Total Turnover (in Rs)</th>
                <th style="background-color:#1ee3a1;font-weight: bold; text-align: center;border: 1px solid black ">Total Income (in Rs)</th>
                <th style="background-color:#1ee3a1;font-weight: bold; text-align: center;border: 1px solid black ">Total Turnover (in Rs)</th>
                <th style="background-color:#1ee3a1;font-weight: bold; text-align: center;border: 1px solid black ">Total Income (in Rs)</th>
            </tr>

        </thead>
        <tbody>
            <?php foreach ($distwisetxns as $distwisetxn) { ?>
                <tr>
                    <td><?= $distwisetxn['unit_name'] ?></td>
                    <td><?= $distwisetxn['total_units_upto'] ?></td>
                    <td><?= $distwisetxn['total_units_mon'] ?></td>
                    <td><?= $distwisetxn['total_units_cumm'] ?></td>
                    <td><?= $distwisetxn['turnover_upto'] ?></td>
                    <td><?= $distwisetxn['expn_upto'] ?></td>
                    <td><?= $distwisetxn['turnover_mon'] ?></td>
                    <td><?= $distwisetxn['expn_mon'] ?></td>
                    <td><?= $distwisetxn['turnover_cumm'] ?></td>
                    <td><?= $distwisetxn['expn_cumm'] ?></td>
                </tr>
            <?php  } ?>
        </tbody>

    </table>