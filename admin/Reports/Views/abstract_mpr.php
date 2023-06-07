<section class="content">

    <?=$filter_panel?>

    <?php if($components): ?>
    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">Report</h3>
            <div class="block-options">
                <a href="<?=$download_url?>" class="btn btn-secondary" data-toggle="tooltip" data-original-title="Download">
                    <i class="si si-cloud-download"></i>
                </a>
            </div>
        </div>
        <div class="block-content block-content-full">
            <div class="tableFixHead">
                <?=$mpr_table?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</section>
<script>

<?php js_start(); ?>
$(function () {
    /*var $th = $('.tableFixHead').find('thead th');
    $('.tableFixHead').on('scroll', function() {
        $th.css('transform', 'translateY('+ this.scrollTop +'px)');
    });*/
});
<?php js_end(); ?>
</script>
