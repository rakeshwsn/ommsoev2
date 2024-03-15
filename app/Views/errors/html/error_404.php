<!doctype html>
<html <?php language_attributes(); ?> class="no-focus">
<head>
    <base href="<?php echo esc_url( site_url() ); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta itemscope itemtype="http://schema.org/WebPage">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="robots" content="noindex, nofollow">

    <!-- Icons -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo get_template_directory_uri(); ?>/assets/media/favicons/favicon.png" />
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo get_template_directory_uri(); ?>/assets/media/favicons/favicon-192x192.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_template_directory_uri(); ?>/assets/media/favicons/apple-touch-icon-180x180.png" />
    <!-- END Icons -->

    <!-- Stylesheets -->

    <!-- Fonts and Codebase framework -->
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,400i,600,700&display=swap" />
    <link rel="stylesheet" id="css-main" href="<?php echo get_template_directory_uri(); ?>/assets/css/codebase.min.css" type="text/css" media="all" />

    <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
    <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/flat.min.css"> -->
    <!-- END Stylesheets -->

    <!-- Structured Data for Breadcrumb -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [{
            "@type": "ListItem",
            "position": 1,
            "name": "Home",
            "item": "https://example.com/"
        },{
            "@type": "ListItem",
            "position": 2,
            "name": "Error 404",
            "item": "https://example.com/error-404/"
        }]
    }
    </script>
    <!-- END Structured Data for Breadcrumb -->

</head>
<body>

<div id="page-container" class="main-content-boxed">

    <!-- Main Container -->
    <main id="main-container">

        <!-- Page Content -->
        <div class="hero bg-white">
            <div class="hero-inner">
                <div class="content content-full">
                    <div class="py-30 text-center">
                        <div class="display-3 text-danger">
                            <i class="fa fa-warning"></i> 404
                        </div>
                        <h1 class="h2 font-w700 mt-30 mb-10">Oops.. You just found an error page..</h1>
                        <h2 class="h3 font-w400 text-muted mb-50">We are sorry but the page you are looking for was not found..</h2>
                        <a class="btn btn-hero btn-rounded btn-alt-secondary" href="<?php echo previous
