    <?=$filter_panel?>

    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">Report</h3>
            <div class="block-options">
                <a href="<?=$download_url?>" class="btn btn-secondary" data-toggle="tooltip" data-original-title="Download">
                    <i class="si si-cloud-download"></i>
                </a>
            </div>
        </div>
        <div class="block-content block-content-full">
            <div class="tableFixHead">
                <table class="table custom-table " id="txn-table">
                    <thead>
                    <tr>
                        <th rowspan="2">Component no</th>
                        <th rowspan="2">Indicator</th>
                        <th rowspan="1" colspan="3">Achivement</th>
                    </tr>
                    <tr>
                        <th>Upto the month</th>
                        <th>During the month</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($unit_types as $unit_type): ?>
                            <tr>
                                <th colspan="5"><?=$unit_type['unit_type']?></th>
                            </tr>
                            <?php foreach ($unit_type['components'] as $component): ?>
                                <tr>
                                    <td><?=$component['number']?></td>
                                    <td><?=$component['output_indicator']?></td>
                                    <td><?=$component['ach_upto_mon']?></td>
                                    <td><?=$component['ach_mon']?></td>
                                    <td><?=$component['cummulative']?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
