<ul id="menu" class="nav-main">
	<?php foreach ($menus as $menu) { ?>
	<li class="<?php echo (isset($menu['heading']) && $menu['heading']) ?'nav-main-heading':'';?>" id="<?php echo $menu['id']; ?>">
        <?php if(isset($menu['heading']) && $menu['heading']){?>
            <?php echo $menu['name']; ?>
        <?}else{?>
            <?php if ($menu['href']) {
                $is_active1 = ($menu['href'] && $menu['href'] == $current_uri) ? 'active' : '';
            ?>
            <a href="<?php echo $menu['href']; ?>" class="<?php echo $is_active1; ?>"><i class="<?php echo $menu['icon']; ?> "></i> <span class="sidebar-mini-hide"><?php echo $menu['name']; ?></span></a>
            <?php } else { ?>
                <a class="<?php echo $menu['children']?'nav-submenu':'';?>" data-toggle="<?php echo $menu['children']?'nav-submenu':'';?>" href="#"><i class="<?php echo $menu['icon']; ?> "></i> <span class="sidebar-mini-hide"><?php echo $menu['name']; ?></span></a>
            <?php } ?>
            <?php if ($menu['children']) { ?>
            <ul class="list-unstyled">
                <?php foreach ($menu['children'] as $children_1) {
                    $is_active = ($children_1['href'] && $children_1['href'] == $current_uri) ? 'active' : '';
                ?>
                <li>
                    <?php if ($children_1['href']) { ?>
                    <a href="<?php echo $children_1['href']; ?>" class="<?php echo $is_active; ?>""><?php echo $children_1['name']; ?></a>
                    <?php } else { ?>
                    <a class="parent waves-effect"><?php echo $children_1['name']; ?></a>
                    <?php } ?>
                    <?php if ($children_1['children']) { ?>
                    <ul>
                        <?php foreach ($children_1['children'] as $children_2) { ?>
                        <li>
                            <?php if ($children_2['href']) { ?>
                            <a href="<?php echo $children_2['href']; ?>"><?php echo $children_2['name']; ?></a>
                            <?php } else { ?>
                            <a class="parent waves-effect"><?php echo $children_2['name']; ?></a>
                            <?php } ?>
                            <?php if ($children_2['children']) { ?>
                            <ul>
                                <?php foreach ($children_2['children'] as $children_3) { ?>
                                <li><a href="<?php echo $children_3['href']; ?>"><?php echo $children_3['name']; ?></a></li>
                                <?php } ?>
                            </ul>
                            <?php } ?>
                        </li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                </li>
                <?php } ?>
            </ul>
            <?php } ?>
        <?php } ?>
	</li>
	<?php } ?>
</ul>
