<section class="content">
    <?php if (isset($filter_panel)): ?>
        <?= $filter_panel; ?>
    <?php endif; ?>

    <?php if (count($components) > 0): ?>
        <div class="block block-themed">
            <div class="block-header bg-muted">
                <h3 class="block-title">Report</h3>
                <div class="block-options">
                    <?php if (isset($download_url)): ?>
                        <a href="<?= $download_url; ?>" class="btn btn-secondary" data-toggle="tooltip" title="Download" role="button">
                            <i class="si si-cloud-download"></i> Download
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="block-content block-content-full" style="margin-top: 1rem;">
                <div id="tableFixHead" class="table-responsive table-fix-head">
                    <table class="table table-bordered table-striped table-vcenter">
                        <!-- Add table headers and rows here -->
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>
