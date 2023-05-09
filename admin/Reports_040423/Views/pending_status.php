<div class="col-12">
    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">Pending Status</h3>
        </div>
        <div class="block-content block-content-full">
            <form>
                <div class="row">
                    <div class="col-md-2">
                        <select class="form-control" id="year" name="year_id">
                            <option value="">Choose Year</option>
                            <?php foreach (getAllYears() as $_year) { ?>
                                <option value="<?= $_year['id'] ?>" <?php if ($_year['id'] == $year_id) {
                                    echo 'selected';
                                } ?>><?= $_year['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" id="month" name="month_id">
                            <option value="">Choose Month</option>
                            <?php foreach (getAllMonths() as $_month) { ?>
                                <option value="<?= $_month['id'] ?>" <?php if ($_month['id'] == $month_id) {
                                    echo 'selected';
                                } ?>><?= $_month['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" id="modulecode" name="modulecode">
                            <option value="">Choose Module</option>
                            <?php foreach ($modules as $module) { ?>
                                <option value="<?= $module['modulecode'] ?>" <?php if ($module['modulecode'] == $modulecode) {
                                    echo 'selected';
                                } ?>><?= $module['module'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php if (isset($districts)): ?>
                        <div class="col-md-2">
                            <select class="form-control" id="district_id" name="district_id">
                                <option value="">Choose District</option>
                                <?php foreach ($districts as $_district) { ?>
                                    <option value="<?= $_district['id'] ?>" <?php if ($_district['id'] == $district_id) {
                                        echo 'selected';
                                    } ?>><?= $_district['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-2">
                        <button class="btn btn-primary"><i class="si si-magnifier"></i> Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="block">
        <div class="block-content block-content-full">
            <table class="table table-striped table-vcenter">
                <thead>
                <tr>
                    <th>Sl No.</th>
                    <th>District</th>
                    <th>Block</th>
                    <th>Agency</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($blocks as $i => $block): ?>
                    <tr>
                        <td><?= ++$i ?></td>
                        <td><?= $block['district'] ?></td>
                        <td><?= $block['block'] ?></td>
                        <td><?= $block['agency'] ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$blocks): ?>
                    <tr>
                        <td colspan="2" class="text-center">Nothing Pending</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>