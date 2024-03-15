<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title" id="approvalModalLabel"></h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <form method="post" id="approveForm">
                        <div class="form-group row">
                            <label class="col-12" for="action">Action</label>
                            <div class="col-lg-12">
                                <select class="form-control" name="status" id="status">
                                    <?php foreach ($statuses as $status): ?>
                                        <option value="<?= htmlspecialchars($status['id']) ?>"><?= htmlspecialchars($status['name']) ?></option>
                                    <?php endforeach; ?>
                              
