<!-- Main content -->
<section class="content">

    <div class="block block-themed">
        <div class="block-header bg-info">
            <h3 class="block-title">Summary</h3>
        </div>
        <div class="block-content block-content-full">
            <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                <tr>
                    <th>District</th>
                    <th>Block</th>
                    <th>GP</th>
                    <th>Year</th>
                    <th>Season</th>
                    <th>Date Added</th>
                    <th>Week Start</th>
                    <th>Week End</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?=$district?></td>
                    <td><?=$block?></td>
                    <td><?=$gp?></td>
                    <td><?=$year?></td>
                    <td><?=$season?></td>
                    <td><?=$date_added?></td>
                    <td><?=$start_date?></td>
                    <td><?=$end_date?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">Area Coverage Details</h3>
        </div>
        <div class="block-content block-content-full">
            <div class="tableFixHead1">
                <table class="table custom-table " id="basic-table">
                    <tbody>
                        <tr>
                            <td>Farmers Covered</td>
                            <td><input type="text" disabled name="crop_coverage[farmers_covered]" value="<?=$crop_coverage['farmers_covered']?>" class="form-control physical"></td>
                        </tr>
                    </tbody>
                </table>
                <table class="table custom-table " id="basic-table">
                    <tbody>
                        <tr>
                            <td>Nursery</td>
                            <td><input type="text" disabled name="nursery[nursery_raised]" value="<?=$nursery_info['nursery_raised']?>" class="form-control financial"></td>
                        </tr>
                        <tr>
                            <td>Balance SMI</td>
                            <td><input type="text" disabled name="nursery[balance_smi]" value="<?=$nursery_info['balance_smi']?>" class="form-control financial"></td>
                        </tr>
                        <tr>
                            <td>Balance LT</td>
                            <td><input type="text" disabled name="nursery[balance_lt]" value="<?=$nursery_info['balance_lt']?>" class="form-control financial"></td>
                        </tr>
                    </tbody>
                </table>
                <table class="table custom-table " id="crop-table">
                    <thead>
                    <tr>
                        <th rowspan="2">Crop</th>
                        <th colspan="3">Practice</th>
                    </tr>
                    <tr>
                        <th>SMI</th>
                        <th>LT</th>
                        <th>LS</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($crops as $crop): ?>
                            <tr>
                                <td><?=$crop['crop']?></td>
                                <?php foreach ($crop['practices'] as $practice_id => $practice): ?>
                                    <td><input type="text" disabled class="form-control financial"
                                                name="area[<?=$crop['crop_id']?>][<?=$practice_id?>]"
                                               value="<?=$practice['area']?>"></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <table class="table custom-table " id="fuc-table">
                    <thead>
                    <tr>
                        <th>Follow Up Crop</th>
                        <th>Area</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($fups as $fup) { ?>
                    <tr>
                        <td>
                            <?php echo $fup['crop'] ?>
                        </td>
                        <td>
                            <input type="text" disabled class="form-control financial" name="fup[<?=$fup['crop_id']?>]" value="<?php echo $fup['area'] ?>">
                        </td>
                    </tr>
                    <?php } ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td>Total</td>
                        <td id="total-fup"><?=$fups_total?></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>