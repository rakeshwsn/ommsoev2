<div class="block">
	<div class="block-header block-header-default">
		<h3 class="block-title"><?= $heading_title; ?></h3>
	</div>
	<div class="block-content block-content-full">
		<table id="block-coverage" class="table table-bordered table-striped table-vcenter">
            <thead>
                <tr>
                    <th rowspan="2">Block</th>
                        <?php foreach ($heading as $crop => $practices) : ?>
                    <th colspan="<?= count($practices) ?>"><?= $crop ?></th>
                        <?php endforeach; ?>
                    <th class="text-right no-sort">Actions</th>
                </tr>
                <tr>
                    <?php foreach ($heading as $crop => $practices) : ?>
                        <?php foreach ($practices as $practice) : ?>
                            <th><?= $practice ?></th>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($practicedata as $data) {  ?>
                <tr>
                
                    <td><?= $data["block"]; ?></td>
                    <td><?= $data["RAGI_SMI"]; ?></td>
                    <td><?= $data["RAGI_LT"]; ?></td>
                    <td><?= $data["RAGI_LS"]; ?></td>
                    <td><?= $data["LITTLE_MILLET_LT"]; ?></td>
                    <td><?= $data["LITTLE_MILLET_LS"]; ?></td>
                    <td><?= $data["FOXTAIL_MILLET_LT"]; ?></td>
                    <td><?= $data["FOXTAIL_MILLET_LS"]; ?></td>
                    <td><?= $data["SORGHUM_LT"]; ?></td>
                    <td><?= $data["SORGHUM_LS"]; ?></td>
                    <td><?= $data["PEARL_MILLET_LT"]; ?></td>
                    <td><?= $data["PEARL_MILLET_LS"]; ?></td>
                    <td><?= $data["BARNYARD_MILLET_LT"]; ?></td>
                    <td><?= $data["BARNYARD_MILLET_LS"]; ?></td>
                    <td><?= $data["KODO_MILLET_LT"]; ?></td>
                    <td><?= $data["KODO_MILLET_LS"]; ?></td>
                    <td><?= $data["PROSO_MILLET_LT"]; ?></td>
                    <td><?= $data["PROSO_MILLET_LS"]; ?></td>
                    <td><a href="<?= $add; ?>" data-toggle="tooltip" title="<?= $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a></td>
                </tr>
                <?php }  ?>
            </tbody>
        </table>
	</div>
</div>

