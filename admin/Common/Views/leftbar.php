<ul class="nav-main ss">
    <?php foreach ($menus as $menu): ?>
        <?php $submenuClass = $menu['children'] ? 'nav-submenu' : ''; ?>
        <li id="<?= $menu['id']; ?>">
            <?php if ($menu['href']): ?>
                <a href="<?= $menu['href']; ?>" class="<?= $submenuClass; ?>" data-toggle="<?= $submenuClass; ?>">
                    <i class="<?= $menu['icon']; ?> "></i>
                    <span class="sidebar-mini-hide"><?= $menu['name']; ?></span>
                </a>
            <?php else: ?>
                <a class="<?= $submenuClass; ?>" data-toggle="<?= $submenuClass; ?>" href="#">
                    <i class="<?= $menu['icon']; ?> "></i>
                    <span class="sidebar-mini-hide"><?= $menu['name']; ?></span>
                </a>
            <?php endif; ?>

            <?php if ($menu['children']): ?>
                <ul class="list-unstyled">
                    <?php foreach ($menu['children'] as $children_1): ?>
                        <?php $childrenSubmenuClass = $children_1['children'] ? 'parent waves-effect' : ''; ?>
                        <li>
                            <?php if ($children_1['href']): ?>
                                <a href="<?= $children_1['href']; ?>" class="<?= $childrenSubmenuClass; ?>"><?= $children_1['name']; ?></a>
                            <?php else: ?>
                                <a class="<?= $childrenSubmenuClass; ?>"><?= $children_1['name']; ?></a>
                            <?php endif; ?>

                            <?php if ($children_1['children']): ?>
                                <ul>
                                    <?php foreach ($children_1['children'] as $children_2): ?>
                                        <?php $childrenSubmenuClass = $children_2['children'] ? 'parent waves-effect' : ''; ?>
                                        <li>
                                            <?php if ($children_2['href']): ?>
                                                <a href="<?= $children_2['href']; ?>" class="<?= $childrenSubmenuClass; ?>"><?= $children_2['name']; ?></a>
                                            <?php else: ?>
                                                <a class="<?= $childrenSubmenuClass; ?>"><?= $children_2['name']; ?></a>
                                            <?php endif; ?>

                                            <?php if ($children_2['children']): ?>
                                                <ul>
                                                    <?php foreach ($children_2['children'] as $children_3): ?>
                                                        <li
