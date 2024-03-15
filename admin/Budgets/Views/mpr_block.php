<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Report</title>
    <?php $this->load->view('header'); ?>
</head>
<body>

<section class="content">

    <?php
    $filter_panel = $this->load->view('filter_panel', array('filter_data' => $filter_data), true);
    $components = empty($components) ? false : $components;
    ?>

    <?php if ($components): ?>
    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">Report</h3>
            <div class="block-options">
                <a href="<?= $download_url ?>" class="btn btn-secondary" data-toggle="tooltip" data-original-title="Download">
                    <i class="si si-cloud-download"></i>
              
