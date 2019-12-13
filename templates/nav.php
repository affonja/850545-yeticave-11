<nav class="nav">
    <ul class="nav__list container">
        <?php
        if (is_array($categories)):
            foreach ($categories as $category): ?>
                <li class="nav__item">
                    <a href="/all-lots.php?catid=<?= $category['id']; ?>">
                        <?= $category['name']; ?></a>
                </li>
            <?php endforeach; else: echo $categories; ?>
        <?php endif; ?>
    </ul>
</nav>