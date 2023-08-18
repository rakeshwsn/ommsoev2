<div class="block block-themed">
    <div class="block-header bg-primary-op">
        <h3 class="block-title">Filter</h3>
    </div>
    <form>
        <div class="block">
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-md-2">
                        <label>Year</label>
                        <select class="form-control" id="year_id" name="year_id" required>
                            <?php foreach ($years as $year) { ?>
                                <option value="<?= $year['id'] ?>" <?php if ($year['id'] == $year_id) {
                                    echo 'selected';
                                } ?>>
                                    <?= $year['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Season</label>
                        <select class="form-control" id="season" name="season">
                            <?php foreach ($seasons as $value => $season) { ?>
                                <option value="<?= $value ?>" <?php if ($value == $current_season) {
                                    echo 'selected';
                                } ?>>
                                    <?= $season ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group mg-b-10-force">
                            <label class="form-control-label">Week: <span class="tx-danger">*</span></label>
                            <?= form_dropdown('start_date', $weeks, $week_start_date, "id='filter_week' class='form-control js-select2'"); ?>
                        </div>
                    </div><!-- col-4 -->
                    <div class="col-md-2 mt-4">
                        <button id="btn-filter" class="btn btn-outline btn-primary">
                            <i class="fa fa-filter"></i> Filter</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="block block-themed">
    <div class="block-header bg-muted">
        <h3 class="block-title">Status Report</h3>
    </div>
    <div class="block-content block-content-full">
        <div class="tableFixHead">
            <table class="table custom-table " id="txn-table">
                <thead>
                    <tr class="highlight-heading1">
                        <th>District</th>
                        <th>Total Blocks</th>
                        <th>Total Blocks Approved</th>
                        <th>Remaining</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statuses as $status): ?>
                        <tr>
                            <td>
                                <?= $status['district'] ?>
                            </td>
                            <td>
                                <?= $status['total_blocks'] ?>
                            </td>
                            <td>
                                <?= $status['total_ac_blocks'] ?>
                            </td>
                            <td>
                                <?= $status['remaining'] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>