<section class="content">

    <?=$filter_panel?>

    <?php if($components): ?>
    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">Report</h3>
            <div class="block-options">
                <a href="<?=$download_url?>" class="btn btn-secondary" data-toggle="tooltip" title="Download" role="button" data-original-title="Download">
                    <i class="si si-cloud-download"></i>
                </a>
            </div>
        </div>
        <div class="block-content block-content-full" style="margin-top: 1rem;">
            <div id="tableFixHead" class="tableFixHead" style="min-width: 100%; box
