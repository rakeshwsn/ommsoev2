<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?=base_url()?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="WASSAN IT TEAM">
    <meta name="robots" content="noindex, nofollow">	
    <?php echo $template->metadata() ?>

    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="<?=theme_url('assets/media/favicons/favicon.png');?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo base_url() ?>/themes/default/assets/images/fav_image_logo.png" title="Site Name" alt="Site Name">
    <link rel="apple-touch-icon" sizes="180x180" href="<?=theme_url('assets/media/favicons/apple-touch-icon-180x180.png');?>">
    <!-- END Icons -->

    <!-- Fonts and Codebase framework -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,400i,600,700&display=swap">
    <link rel="stylesheet" id="css-main" href="<?php echo theme_url('assets/css/codebase.min.css');  ?>">
    <link rel="stylesheet" id="css-main" href="<?php echo theme_url('assets/css/custom.css?v=2');  ?>">
    <!-- Controller Defined Stylesheets -->
    <?php echo $template->stylesheets() ?>
    <script src="<?=theme_url('assets/js/codebase.core.min.js');?>"></script>
    <script type="text/javascript">
        var BASE_URL = '<?php echo base_url(); ?>';
        var ADMIN_URL = '<?php echo admin_url(); ?>';
        var THEME_URL = '<?php echo theme_url(); ?>';
    </script>
    <!-- Controller Defined JS Files -->
    <?php echo $template->javascripts() ?>
</head>
<body>
    <?php
    if ($user->isLogged() && $header){	
    ?>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-fixed">
        <nav id="sidebar">
            <!-- Sidebar Content -->
            <div class="sidebar-content">
                <!-- Side Header -->
                <div class="content-header content-header-fullrow px-15">
                    <!-- Mini Mode -->
                    <div class="content-header-section sidebar-mini-visible-b">
                        <!-- Logo -->
                        <span class="content-header-item font-w700 font-size-xl float-left animated fadeIn">
                            <span class="text-dual-primary-dark">c</span><span class="text-primary">b</span>
                        </span>
                        <!-- END Logo -->
                    </div>
                    <!-- END Mini Mode -->

                    <!-- Normal Mode -->
                    <div class="content-header-section text-center align-parent sidebar-mini-hidden">
                        <!-- Close Sidebar, Visible only on mobile screens -->
                        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                        <button type="button" class="btn btn-circle btn-dual-secondary d-lg-none align-v-r" data-toggle="layout" data-action="sidebar_close">
                            <i class="fa fa-times text-danger"></i>
                        </button>
                        <!-- END Close Sidebar -->

                        <!-- Logo -->
                        <div class="content-header-item">
                            <a href="<?=admin_url()?>" class="link-effect font-w700">
                                <?php if ($logo) { ?>
                                    <img width="100%" height="40px" src="<?php echo $logo; ?>" title="<?php echo $site_name; ?>" alt="<?php echo $site_name; ?>"  />
                                <?php } ?>
                                <i class="si si-fire text-primary"></i>
                                <span class="font-size-xl text-dual-primary-dark">OMM Portal</span><span class="font-size-xl text-primary"> 2.0</span>
                            </a>
                        </div>
                        <!-- END Logo -->
                    </div>
                    <!-- END Normal Mode -->
                </div>
                <!-- END Side Header -->

                <!-- Side User -->

                <!-- END Side User -->

                <!-- Side Navigation -->
                <div class="content-side content-side-full">
                    <?=$menu?>
                    <?php if($user->getGroupId()==1){?>
                
