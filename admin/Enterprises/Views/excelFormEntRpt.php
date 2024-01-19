<table class="table table-bordered border-dark border-3">

    <thead>
        <tr>
            <th style="background-color:#e3a11e;font-weight: bold; text-align: center; " colspan="<?= count($unit_names)+2 ?>">
                <h4>Enterprise Establishment Report</h4>
            </th>

        </tr>
        <tr>
            <th style="background-color:#1ee3a1;font-weight: bold;text-align: center;" colspan="<?= count($unit_names)+2 ?>">
                <?php
                if ($district_id) {
                    echo 'District: ' . $district_text;
                } else {
                    // If filtering only by district, show the District filter in the row
                    echo 'District ';
                }

                if ($block_id) {
                    echo ' || Block: ' . $block_text;
                }

                if ($year_id) {
                    echo ' || Year: ' . $year_text;
                }

                if ($month_id) {
                    echo ' || Month: ' . $month_text;
                }

                if ($management_unit_type == 'management_unit_type') {
                    echo ' || Unit: ' . $unit_text;
                }
                ?>
            </th>
        </tr>

        <tr>
            <th style="text-align: center; font-weight: bold; background-color:#d1cdc5;" colspan="<?= count($unit_names)+2 ?>">
                Type and number of units
            </th>
        </tr>

        <tr>
            <th style="background-color:#e3a11e">
                <?php if ($district_id) {
                    echo "Blocks";
                } elseif ($block_id) {
                    echo "Grampanchayat";
                } else {
                    echo "Districts";
                }
                ?>
            </th>
            <?php foreach ($unit_names as $unit) : ?>
                <th style="background-color:#e3a11e"><?= $unit->name ?></th>
            <?php endforeach; ?>
            <th style="background-color:#e3a11e">Total</th>
        </tr>
    </thead>
    <tbody>

        <?php if ($block_id) { ?>
            <?php foreach ($gpunits as $gpunit) : ?>
                <tr>
                    <td><?= $gpunit['gp'] ?></td>
                    <?php foreach ($gpunit['g_units'] as $gunit) : ?>
                        <td><?= $gunit ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        <?php } else if ($district_id) { ?>
            <?php foreach ($blockunits as $blockunit) : ?>
                <tr>
                    <td><?= $blockunit['block'] ?></td>
                    <?php foreach ($blockunit['b_units'] as $bunit) : ?>
                        <td><?= $bunit ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        <?php } else { ?>
            <?php foreach ($units as $unit) : ?>
                <tr>
                    <td><?= $unit['district'] ?></td>
                    <?php foreach ($unit['units'] as $eunit) : ?>
                        <td><?= $eunit ?></td>
                    <?php endforeach; ?>

                </tr>

            <?php endforeach; ?>
        <?php } ?>

    </tbody>

</table>