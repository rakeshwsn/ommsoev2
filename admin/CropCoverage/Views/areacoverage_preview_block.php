<?php
$validation = \Config\Services::validation();
?>
<div class="row">
    <div class="col-xl-12">
        <div class="block">
            <div class="block-header block-header-default bg-success">
                <h3 class="block-title"><?= $heading_title; ?></h3>
            </div>
			<div class="block-header-content" style="display:flex;padding:20px 0 20px 0">
				<div class="col-md-3">
                <label>From Date</label>
				<input type="text"  class="form-control" value="<?=$from_date?>" readonly>
				</div>
				<div class="col-md-3">
				<label>To Date</label>
				<input type="text" readonly value="<?=$to_date?>" class="form-control">
				</div>
			</div>
           
        </div>
    </div>
    <div class="col-xl-12">
        <div class="block">
            <div class="block-header block-header-default  bg-primary">
                <h3 class="block-title"> Area Coverage Preview</h3>
            </div>

            <div class="col-sm-12">
                <table id="block-coverage" class="table table-bordered table-striped table-vcenter table-responsive">
                    <thead>
                        <tr>
                            <th rowspan="3">Block</th>
                            <th rowspan="3">No of GP</th>
                            <th rowspan="3">No. of Farmer Covered (for Nursery and Sowing)</th>
                            <th rowspan="3">SMI - Balance Nursery Raised (for coverage of area in Ha.) (Please
                                write figure post transplantation and damage)</th>
                            <th rowspan="3">LT - Balance Nursery Raised (for coverage of area in Ha.) (Please
                                write figure post transplantation and damage)
                            </th>
                            <th colspan="19">Achievement under demonstration (Area in Hectare)
                            </th>
                            <th rowspan="3">Achievement (Cumulative) Area in hectare</th>
                            <th rowspan="3" class="text-right no-sort">Actions</th>
                        </tr>
                        <tr>
                            <?php foreach ($heading as $crop => $practices): ?>
                                <th colspan="<?= count($practices) ?>"><?= $crop ?></th>
                            <?php endforeach; ?>
                            <th rowspan="2">Total Ragi</th>
                            <th rowspan="2">Total Non-Ragi </th>
                        </tr>
                        <tr>
                            <?php foreach ($heading as $crop => $practices): ?>
                                <?php foreach ($practices as $practice): ?>
                                    <th>
                                        <?= $practice ?>
                                    </th>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($blocks) {?>
                    <?php foreach ($blocks as $block) {?>
                            <tr>
                                <td><?=$block['week']?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="4">Data not available.</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>