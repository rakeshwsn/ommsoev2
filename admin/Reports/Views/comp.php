<table class="table table-bordered table-striped table-vcenter js-dataTable-full">
    <thead>
        <tr>
            <th>District</th>
            <th>Block</th>
            <th>Year</th>
            <?php if($periodic): ?>
                <th>Period</th>
            <?php else: ?>
                <th>Month</th>
            <?php endif; ?>
            <th>Fund Agency</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= htmlspecialchars($district) ?></td>
            <td><?= htmlspecialchars($block) ?></td>
            <td><?= htmlspecialchars($fin_year) ?></td>
            <?php if($periodic): ?>
                <td><?= htmlspecialchars($period) ?></td>
            <?php else: ?>
                <td><?= htmlspecialchars($month_name) ?></td>
            <?php endif; ?>
            <td><?= htmlspecialchars($fund_agency) ?></td>
        </tr>
    </tbody>
</table>
