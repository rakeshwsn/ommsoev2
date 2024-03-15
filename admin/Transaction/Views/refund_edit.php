<style>
    .w-50p{width:50px;}
</style>
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
                    <th>Agency Type</th>
                    <th>Month/Year</th>
                    <th>Funding</th>
                    <th>Date Added</th>
                    <th>Txn Type</th>
                    <th>Phy</th>
                    <th>Fin</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?=$district?></td>
                    <td><?=$block?></td>
                    <td><?=$agency_type?></td>
                    <td><?=$month?> / <?=$year?></td>
                    <td><?=$fund_agency?></td>
                    <td><?=$date_added?></td>
                    <td><?=$txn_type_text?></td>
                    <td><?=$phy?></td>
                    <td><?=$fin?></td>
                    <?php if(isset($approval) && $approval): ?>
                        <td><button class="btn btn-primary" id="btn-action"><?=$status?></button> </td>
                    <?php else: ?>
                        <td><?=$status?></td>
                    <?php endif; ?>
                </tr>
                <?php if(!empty($remarks)): ?>
                <tr>
                    <td>Remarks:</td>
                    <td colspan="7"><?=$remarks?></td>
                </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">Transaction Details</h3>
        </div>
        <div class="block-content block-content-full">
            <?php if($show_form) { echo form_open('controller/method');} ?>
            <div class="tableFixHead">
                <table class="table custom-table " id="txn-table">
                    <thead>
                    <tr>
                        <th rowspan="2" width="10%">Sl no</th>
                        <th rowspan="2" width="30%">Component</th>
                        <th colspan="2" width="15%">Fund Available Up to Date</th>
                        <th colspan="2" width="15%"><?=$txn_type_text?></th>
                    </tr>
                    <tr>
                        <th>Phy</th>
                        <th>Fin</th>
                        <th>Phy</th>
                        <th>Fin</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?=$components?>
                    </tbody>
                </table>
            </div>
            <?php if($show_form): ?>
            <div class="row">
                <div class="col mt-4">
                    <button type="submit" class="btn btn-alt-primary float-right" id="submit-btn">Submit</button>
                </div>
            </div>
                <?php echo form_close(); ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php js_start(); ?>
<script>
    $(function () {

        $('.mon_phy').keyup(function (e) {
            var ctx = $(this);
            parent = $(ctx).closest('tr');
            parent_id = $(ctx).closest('tr').data('parent');

            //grand total
            grandTotalMonPhy = 0;
            grandTotalCumPhy = 0;
            $('.mon_phy').each(function () {
                monPhy = parseInt($(this).find('input').val(), 10) || 0;
                grandTotalMonPhy += monPhy;

