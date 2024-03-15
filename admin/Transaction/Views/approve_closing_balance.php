<!-- Main content -->
<section class="content">

    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Summary</h3>
        </div>
        <div class="block-content">
            <table class="table table-bordered table-vcenter">
                <thead class="thead-light">
                <tr>
                    <th>Year</th>
                    <th>Month</th>
                    <th>Agency Type</th>
                    <th>Opening (+)</th>
                    <th>Fund Receipt (+)</th>
                    <th>Misc Transaction (+)</th>
                    <th>Expense (-)</th>
                    <th>Closing</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?php echo $summary['year']; ?></td>
                    <td><?php echo $summary['month']; ?></td>
                    <td><?php echo $summary['agency_type']; ?></td>
                    <td><?php echo $summary['ob']; ?></td>
                    <td><?php echo $summary['fr']; ?></td>
                    <td><?php echo $summary['mt']; ?></td>
                    <td><?php echo $summary['exp']; ?></td>
                    <td><?php echo $summary['bal']; ?></td>
                    <?php if (isset($approval) && $approval): ?>
                        <td><button class="btn btn-primary" id="btn-action" type="button" title="<?php echo $status; ?>">
                                <?php echo $status; ?></button></td>
                    <?php else: ?>
                        <td><?php echo $status; ?></td>
                    <?php endif; ?>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Closing Balance Breakup</h3>
        </div>
        <div class="block-content">
            <table id="closing-balance-breakup" class="table table-bordered table-vcenter">
                <thead class="thead-light">
                <tr>
                    <th style="width:300px;">Particulars</th>
                    <th style="width:300px;">Amount</th>
                    <th>Attachment</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Advance</td>
                    <td><input class="form-control amount" value="<?php echo $advance; ?>" disabled min="0" step="0.01"
                              placeholder="0.00" aria-label="Advance amount" tabindex="-1"></td>
                    <td class="dm-uploader">
                        <?php if ($advance_file_url): ?>
                            <img src="<?php echo $advance_file_url; ?>" alt="Advance attachment">
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Bank (including bank interest)</td>
                    <td><input class="form-control amount" name="bank" value="<?php echo $bank; ?>" disabled min="0"
                              step="0.01" placeholder="0.00" aria-label="Bank amount" tabindex="-1"></td>
                    <td>
                        <?php if ($bank_file_url): ?>
                            <img src="<?php echo $bank_file_url; ?>" alt="Bank attachment">
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Cash</td>
                    <td><input class="form-control amount" name="cash" value="<?php echo $cash; ?>" disabled min="0"
                              step="0.01" placeholder="0.00" aria-label="Cash amount" tabindex="-1"></td>
                    <td class="dm-uploader">
                        <?php if ($cash_file_url): ?>
                            <img src="<?php echo $cash_file_url; ?>" alt="Cash attachment">
                        <?php endif; ?>
                    </td>
                </tr
