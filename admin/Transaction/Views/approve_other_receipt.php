<!-- Main content -->
    <section class="content">
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title">Summary</h3>
            </div>
            <div class="block-content">
                <table class="table table-bordered table-vcenter">
                    <thead>
                    <tr>
                        <th>District</th>
                        <th>Block</th>
                        <th>Agency Type</th>
                        <th>Month/Year</th>
                        <th>Funding</th>
                        <th>Date Added</th>
                        <th>Txn Type</th>
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
        <div class="block">
            <form method="post" action="">
            <div class="block-content">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Details</h3>
                    <?php if(isset($correction)){ ?>
                    <div class="block-options"><button class="btn btn-primary"><i class="fa fa-save"></i> Save</button></div>
                    <?php } ?>
                </div>
                    <table class="table table-bordered table-vcenter">
                        <thead>
                        <tr>
                            <th>Particulars</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($heads as $head) : ?>
                            <tr>
                                <th><strong><?=$head['name']?></strong></th>
                                <?php if(isset($correction)){ ?>
                                <td>
                                    <input type="text" class="form-control" name="head[<?=$head['id']?>]"
                                           value="<?=$head['value']?>" >
                                </td>
                                <?php } else { ?>
                                <td><?=$head['value']?></td>
                                <?php } ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
            </div>
            </form>
        </div>

    </section>
    <!-- content -->

<?php js_start(); ?>
<script></script>
<?php if(isset($approval)) {
    echo $approve_form;
} ?>

<?php js_end(); ?>
