<div class="main-container">
    <div class="block">
        <form action="" method="post">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    <?= $heading_title; ?>
                </h3>

            </div>
            <div class="block-content block-content-full">
                <div id="page_list_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="row">

                        <div class="col-4">
                            <label class="form-label">Week From</label>
                            <input type="text">

                        </div>
                        <div class="col-3">
                            <label class="form-label">To</label>
                            <input type="text" name="" id="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-sm-12">
                        <table id="block-coverage"
                            class="table table-bordered table-striped table-vcenter table-responsive">
                            <thead>
                                <tr>
                                    <th rowspan="3">Block</th>
                                    <th rowspan="3">No of GP</th>
                                    <th rowspan="3">No of Villages</th>
                                    <th rowspan="3">No. of Farmer Covered (for Nursery and Sowing)</th>
                                    <th rowspan="3">SMI - Balance Nursery Raised (for coverage of area in Ha.) (Please
                                        write figure post transplantation and damage)</th>
                                    <th rowspan="3">LT - Balance Nursery Raised (for coverage of area in Ha.) (Please
                                        write figure post transplantation and damage)
                                    </th>

                                    <th colspan="9">Achievement under demonstration (Area in Hectare)</th>

                                    <?php foreach ($heading as $crop => $practices): ?>
                                        <th colspan="<?= count($practices) ?>" rowspan="1"><?= $crop ?></th>
                                    <?php endforeach; ?>

                                    <th class="text-right no-sort colspan-2">Actions</th>
                                </tr>

                                <tr>
                                    <?php foreach ($heading as $crop => $practices): ?>
                                        <th colspan="<?= count($practices) + 1 ?>" rowspan="1">Achievement under
                                            demonstration (Area in Hectare)</th>
                                    <?php endforeach; ?>
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
                                <?php foreach ($practicedata as $data) { ?>
                                    <tr>
                                        <td>
                                            <?= $data["block"]; ?>
                                        </td>
                                        <td>
                                            <?= $data["RAGI_SMI"]; ?>
                                        </td>
                                        <td>
                                            <?= $data["RAGI_LT"]; ?>
                                        </td>
                                        <td>
                                            <?= $data["RAGI_LS"]; ?>
                                        </td>
                                        <td>
                                            <?= $data["LITTLE_MILLET_LT"]; ?>
                                        </td>
                                        <td>
                                            <?= $data["LITTLE_MILLET_LS"]; ?>
                                        </td>
                                        <td>
                                            <?= $data["FOXTAIL_MILLET_LT"]; ?>
                                        </td>
                                        <td>
                                            <?= $data["FOXTAIL_MILLET_LS"]; ?>
                                        </td>
                                        <td>
                                            <?= $data["SORGHUM_LT"]; ?>
                                        </td>
                                        <td>
                                            <?= $data["SORGHUM_LS"]; ?>
                                        </td>
                                        <td>
                                            <?= $data["PEARL_MILLET_LT"]; ?>
                                        </td>
                                        <td>
                                            <?= $data["PEARL_MILLET_LS"]; ?>
                                        </td>
                                        <td>
                                            <?= $data["BARNYARD_MILLET_LT"]; ?>
                                        </td>
                                        <td>
                                            <?= $data["BARNYARD_MILLET_LS"]; ?>
                                        </td>
                                        <td>
                                            <?= $data["KODO_MILLET_LT"]; ?>
                                        </td>
                                        <td>
                                            <?= $data["KODO_MILLET_LS"]; ?>
                                        </td>
                                        <td>
                                            <?= $data["PROSO_MILLET_LT"]; ?>
                                        </td>
                                        <td>
                                            <?= $data["PROSO_MILLET_LS"]; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm pull-right">
                                                <a class="btn btn-sm btn-primary"
                                                    href="<?= $edit; ?>?block_id=<?= $data['block_id']; ?>"
                                                    title="<?= $button_edit; ?>"><i class="fa fa-pencil"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </form>
    </div>
</div>