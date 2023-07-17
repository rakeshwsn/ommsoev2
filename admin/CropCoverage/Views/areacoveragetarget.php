<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title">
            <?= $heading_title; ?>
        </h3>
    </div>
    <div class="block-content block-content-full">
        <div class="row mg-b-25">
            <div class="col-lg-3">
                <div class="form-group mg-b-10-force">
                    <label class="form-control-label">Year: <span class="txt-danger">*
                            <?php $currentYear = date('Y');
                            echo $currentYear;
                            ?>
                        </span></label>

                </div>
            </div><!-- col-4 -->
            <div class="col-lg-3">
                <div class="form-group mg-b-10-force">
                    <label class="form-control-label">Season: <span class="txt-danger">*
                            <?php
                            $seasonPrinted = false;

                            foreach ($practicedata as $pd) {

                                if (!$seasonPrinted) {
                                    echo $pd['season'] . '<br>';
                                    $seasonPrinted = true;
                                }
                                // Rest of your code
                                // ...
                            }
                            ?>
                        </span></label>

                </div>
            </div><!-- col-4 -->
            <table id="block-coverage" class="table table-bordered table-striped table-vcenter table-responsive">
                <thead>
                    <tr>
                        <th rowspan="2">Block</th>
                        <?php foreach ($heading as $crop => $practices): ?>
                            <th colspan="<?= count($practices) ?>"><?= $crop ?></th>
                        <?php endforeach; ?>
                        <th class="text-right no-sort colspan-2">Actions</th>
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