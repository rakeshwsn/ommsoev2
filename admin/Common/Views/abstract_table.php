<table class="table table-striped">
    <thead>
    <tr>
        <th>Sl</th>
        <th>Agency</th>
        <th>Total OB</th>
        <th>Total FR</th>
        <th>Total EX</th>
        <th>Total CB</th>
        <th>Percentage</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $row): ?>
        <tr style="background-color: <?=$row['bg_color']?>">
            <td><?=$row['sl']?></td>
            <td><?=$row['agency']?></td>
            <td><?=$row['ob_in_lakh']?></td>
            <td><?=$row['fr_in_lakh']?></td>
            <td><?=$row['ex_in_lakh']?></td>
            <td><?=$row['cb_in_lakh']?></td>
            <td><?=$row['percentage']?> %</td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>