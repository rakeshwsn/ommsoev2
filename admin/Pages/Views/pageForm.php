<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    $validation = \Config\Services::validation();
    $text_form = 'Form Title';
    $id = 1;
    $title = 'Title';
    $content = 'Content';
    $slug = 'Slug';
    $meta_title = 'Meta Title';
    $meta_keywords = 'Meta Keywords';
    $meta_description = 'Meta Description';
    $status = 'Published';
    $visibilty = 'Public';
    $thumb_feature_image = 'http://example.com/image.jpg';
    $feature_image = 'feature_image.jpg';
    $no_image = 'http://example.com/no-image.jpg';
    $layout = ['layout1' => 'Layout 1', 'layout2' => 'Layout 2'];
    $layouts = $layout;
    $parents = ['parent1' => 'Parent 1', 'parent2' => 'Parent 2'];
    $parent_id = 'parent1';
    $text_image = 'Select Image';
   
