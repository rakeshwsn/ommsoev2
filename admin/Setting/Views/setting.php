<?php
$validation = \Config\Services::validation();
$config_site_logo = isset($config_site_logo) ? $config_site_logo : '';
$config_site_icon = isset($config_site_icon) ? $config_site_icon : '';
$config_header_image = isset($config_header_image) ? $config_header_image : '';
$thumb_logo = base_url('uploads/images/' . $config_site_logo);
$thumb_icon = base_url('uploads/images/' . $config_site_icon);
$thumb_header_image = base_url('uploads/images/' . $config_header_image);
$no_image = base_url('uploads/images/no_image.png');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Configuration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" integrity="sha384-pzjw8f+ua7Kw1TIq0v8FqFjcJ6pajs/rfdfs3SO+kD4Ck5BdPtF+to8xMmcke49" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSGFpoO9xmv/+/z7nU7ELJ6EeAZWlCmGKZk4M1RtIDZOt6Xq/YD" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-eMNCOe7tC1doHpGoJtKh7z7lGz7fuP4F8nfdFvAOA6Gg/z6Y5J6XqqyGXYM2ntX5" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js" integrity="sha384-MrFsm+sodUMSWw+KcQgfbdymkU/+IrjNzI5L06febp/Zdnobx93bgs/pMD14Ehdb" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"></script>
</head>
<body>
<div class="container">
    <h1>Configuration</h1>
    <?php echo form_open_multipart('', 'id="config-form"'); ?>
    <div class="form-group">
        <label for="config_site_title">Site Title</label>
        <input type="text" class="form-control" id="config_site_title" name="config_site_title" placeholder="Site Title" value="<?= set_value('config_site_title', isset($config_site_title) ? $config_site_title : ''); ?>">
        <div class="invalid-feedback"><?= $validation->getError('config_site_title'); ?></div>
    </div>
    <div class="form-group">
        <label for="config_site_tagline">Site Tagline</label>
        <input type="text" class="form-control" id="config_site_tagline" name="config_site_tagline" placeholder="Site Tagline" value="<?= set_value('config_site_tagline', isset($config_site_tagline) ? $config_site_tagline : ''); ?>">
        <div class="invalid-feedback"><?= $validation->getError('config_site_tagline'); ?></div>
    </div>
    <div class="form-group">
        <label for="config_site_logo">Site Logo</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <button type="button" class="btn btn-primary" onclick="image_upload('config_site_logo', 'thumb_logo')">Select Image</button>
            </div>
            <div class="custom-file">
                <input type="hidden" name="config_site_logo" value="<?= $config_site_logo; ?>" id="config_site_logo">
                <input type="text" class="form-control" id="thumb_logo" readonly>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="config_site_icon">Site Icon</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <button type="button" class="btn btn-primary" onclick="image_upload('config_site_icon', 'thumb_icon')">Select Image</button>
            </div>
            <div class="custom-file">
                <input type="hidden" name="config_site_icon" value="<?= $config_site_icon; ?>" id="config_site_icon">
                <input type="text" class="form-control" id="thumb_icon" readonly>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="config_header_image">Header Image</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <button type="button" class="btn btn-primary" onclick="
