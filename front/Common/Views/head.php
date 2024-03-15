<?php
$base_url = base_url();
$theme_url = theme_url();
$template = service('template');
$user = service('user');

$base_url_constant = constant('BASE_URL');
$theme_url_constant = constant('THEME_URL');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<base href="<?= $base_url ?>">
	<meta charset="UTF-8">
	<?php echo $template->metadata() ?>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="<?php echo e($theme_url('assets/images/icons/favicon.png')); ?>"/>
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e($theme_url('assets/vendor/bootstrap/css/bootstrap.min.css')); ?>">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e($theme_url('assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css')); ?>">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e($theme_url('assets/fonts/linearicons-v1.0.0/icon-font.min.css')); ?>">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e($theme_url('assets/vendor/animate/animate.css')); ?>">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e($theme_url('assets/vendor/css-hamburgers/hamburgers.min.css')); ?>">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e($theme_url('assets/vendor/animsition/css/animsition.min.css')); ?>">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e($theme_url('assets/vendor/select2/select2.min.css')); ?>">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e($theme_url('assets/vendor/daterangepicker/daterangepicker.css')); ?>">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e($theme_url('assets/vendor/slick/slick.css')); ?>">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e($theme_url('assets/vendor/lightbox2/css/lightbox.min.css')); ?>">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e($theme_url('assets/vendor/perfect-scrollbar/perfect-scrollbar.css')); ?>">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e($theme_url('assets/vendor/revolution/css/layers.css')); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo e($theme_url('assets/vendor/revolution/css/navigation.css')); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo e($theme_url('assets/vendor/revolution/css/settings.css')); ?>">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e($theme_url('assets/css/util.css')); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo e($theme_url('assets/css/main.css?v=1')); ?>">

	<script>
		const BASE_URL = "<?= $base_url_constant ?>";
		const THEME_URL = "<?= $theme_url_constant ?>";
	</script>

	<?php echo $template->stylesheets() ?>
</head>
<body class="animsitions">

<!-- Header -->
<header>
	<!-- Header desktop -->
	<div class="wrap-menu-desktop d-none d-md-flex">
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-1">
					<a href="<?= $base_url ?>" class="sppif-logo ml-auto">
					<img src="images/sppif-logo.jpg" alt="SPPIF Logo"></a>
				</div>
				<div class="col-sm-6">
					<div class="logo-wrapper d-flex h-100">
					<a href="<?= $base_url ?>" class="align-self-center">
					<h2>Special Programme for Promotion of Integrated Farming</h2>
					<!--<img src="images/sppif-text-logo.png" class="img-fluid" alt="IMG-LOGO">-->
