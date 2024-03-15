<div class="block">
    <ul class="nav nav-tabs nav-tabs-block js-tabs-enabled" data-toggle="tabs" role="tablist">
        <?php foreach((array)$menu_groups as $key => $value) : ?>
            <li id="group-<?php echo $value['id']; ?>" class="nav-item">
                <a class="nav-link <?=($value['id']==$menu_group_id)?'active':''?>" href="<?php echo admin_url("menu/{$value['id']}"); ?>"> <?php echo $value['title']; ?> </a>
            </li>
        <?php endforeach; ?>
        <li class="nav-item ml-auto">
            <a class="nav-link" href="<?php echo admin_url('menu/0'); ?>" title="Add menu items">
                <i class="fa fa-plus"></i>
            </a>
        </li>
    </ul>
</div>

<div class="row">
	<div class="col-md-4 <?php echo !$menu_group_id?'disableddiv ':'';?>">
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title">Add menu items</h3>
            </div>
            <div class="block-content">
                <div id="menu-left" class="mb-4">
                    <div id="accordion" role="tablist" aria-multiselectable="true">
                        <div class="block block-bordered block-rounded mb-2">
                            <div class="block-header bg-default" role="tab" id="accordion_h1">
                                <a class="font-w600 text-white collapsed" data-bs-toggle="collapse" data-bs-parent="#accordion" href="#accordion_q1" aria-expanded="false" aria-controls="accordion_q1" aria-label="Toggle pages">Pages</a>
                            </div>
                            <div id="accordion_q1" class="collapse" role="tabpanel" aria-labelledby="accordion_h1" data-bs-parent="#accordion" style="">
                                <div class="block-content">
                                    <?php foreach($pages as $page){?>
                                        <div class="checkbox">
                                            <label class="css-control css-control-primary css-checkbox">
                                                <?php echo form_checkbox(array('name' => 'pages[]', 'value' => $page->id,'checked' => false,'data-name'=>$page->title,'data-slug'=>$page->slug,'class'=>'css-control-input')); ?>
                                                <span class="css-control-indicator"></span> <?php echo $page->title;?>
                                            </label>
                                        </div>
                                    <?php } ?>
                                    <p>
                                        <button type="button" class="btn btn-success waves-effect waves-light selectall">Select All</button>
                                        <button type="button" class="btn btn-light waves-effect float-
