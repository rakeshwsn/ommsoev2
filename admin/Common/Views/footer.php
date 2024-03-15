<?php 
$template = service('template');
$user = service('user');
$showOldPortal = $variables['show_old_portal'] ?? false; // Added to avoid undefined variable notice
$headSection = view('partials/head'); // Assuming there is a partial view for the head section
$content = view('partials/content'); // Assuming there is a partial view for the content
$footerScripts = view('partials/footer-scripts'); // Assuming there is a partial view for the footer scripts
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?= $headSection; ?>
</head>
<body class="bg-image" style="background-image: url('<?=theme_url('assets/media/photos/photo34@2x.jpg');?>');">
  <?= $content; ?>

  <footer id="page-footer" class="opacity-0">
    <div class="content py-20 font-size-sm clearfix">
      <div class="float-right">
        Crafted with <i class="fa fa-heart text-pulse"></i> by <a class="font-w600" href="https://www.wassanodisha.in/" target="_blank">WASSAN Odisha IT Team</a>
      </div>
    </div>
  </footer>
</div>

<?= $template->footer_javascript() ?>

<script src="<?=theme_url('assets/js/codebase.app.js');?>"></script>
<script src="<?=theme_url('assets/js/common.js');?>"></script>
<script src="//cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>

<script>
  $(function () {
    if(!<?php echo $showOldPortal; ?>) { // Changed to use PHP echo to insert the variable value
      $('.old-portal-login').hide();
    }
  });

