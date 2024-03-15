    <?php $filter_panel = $this->load->view('filter_panel', array('filter_data' => $filter_data), true); ?>

    <?php if ($components): ?>
    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">Report</h3>
            <div class="block-options">
                <a href="<?= $download_url ?>" class="btn btn-secondary" data-toggle="tooltip" data-original-title="Download">
                    <i class="si si-cloud-download"></i>
                </a>
            </div>
        </div>
        <div class="block-content block-content-full">
            <div class="tableFixHead">
                <table class="table custom-table table-striped table-hover" id="txn-table" data-order='[[ 0, "asc" ]]' data-order-method='original' data-page-length='50'>
                    <thead>
                        <tr>
                            <th rowspan="3">Sl no</th>
                            <th rowspan="3" data-title="Component">Component</th>
                            <th rowspan="2" colspan="2">Opening Balance</th>
                            <th rowspan="2" colspan="2">Target</th>
                            <th rowspan="1" colspan="6">Allotment received (in lakhs) from DA & FP(O)</th>
                            <th rowspan="1" colspan="4">Expenditure (in lakhs)</th>
                            <th rowspan="2" colspan="2">Unspent Balance upto the month (in lakhs)</th>
                        </tr>
                        <tr>
                            <th colspan="2">As per statement upto prev month</th>
                            <th colspan="2">During the month</th>
                            <th colspan="2">Upto the month</th>
                            <th colspan="2">During the month</th>
                            <th colspan="2">Cumulative Expenditure upto the month</th>
                        </tr>
                        <tr>
                            <th>Phy&nbsp;</th>
                            <th>Fin&nbsp;</th>
                            <th>Phy&nbsp;</th>
                            <th>Fin&nbsp;</th>
                            <th>Phy&nbsp;</th>
                            <th>Fin&nbsp;</th>
                            <th>Phy&nbsp;</th>
                            <th>Fin&nbsp;</th>
                            <th>Phy&nbsp;</th>
                            <th>Fin&nbsp;</th>
                            <th>Phy&nbsp;</th>
                            <th>Fin&nbsp;</th>
                            <th>Phy&nbsp;</th>
                            <th>Fin&nbsp;</th>
                            <th>Phy&nbsp;</th>
                            <th>Fin&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?= $components ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

</section>

<script>
    $(document).ready(function() {
        $('#txn-table').DataTable({
            "init
