<ul class="pagination-list">
    <li class="pagination-item pagination-item-prev">
        <?php ($cur_page == 1) ? $class_hide = 'visually-hidden'
            : $class_hide = ''; ?>
        <a <?= "class='$class_hide''"; ?>
                href="/search.php?search=<?= $query; ?>&page=<?= $cur_page
                - 1; ?>">Назад</a></li>
    <?php foreach ($pages as $page): ; ?>
        <?php ($page == $cur_page) ?
            $class_active = 'pagination-item-active'
            : $class_active = ''; ?>
        <li class="pagination-item <?= $class_active; ?>">
            <a href="/search.php?search=<?= $query; ?>&page=<?= $page; ?>"><?= $page; ?></a>
        </li>
    <?php endforeach; ?>
    <li class="pagination-item pagination-item-next">
        <?php ($cur_page == count($pages)) ? $class_hide = 'visually-hidden'
            : $class_hide = ''; ?>
        <a <?= "class='$class_hide''"; ?>
                href="/search.php?search=<?= $query; ?>&page=<?= $cur_page
                + 1; ?>">Вперед</a>
    </li>
</ul>