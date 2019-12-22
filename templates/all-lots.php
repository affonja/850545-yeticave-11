<?php
/**
 * @var string $category_name
 * @var array|null $lots
 * @var string $error
 * @var array $pages
 * @var int $cur_page
 * @var int $category_id
 */
?>

<?= include_template('nav.php', ['categories' => $categories]) ?>
<div class="container">
    <section class="lots">
        <h2>Все лоты в категории <span><?= $category_name; ?></span></h2>
        <?php if (is_array($lots)): ; ?>
            <ul class="lots__list">
                <?php foreach ($lots as $lot) : ?>
                    <?= include_template('lot_preview.php', ['lot' => $lot]); ?>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <h3><?= $error; ?></h3>
        <?php endif; ?>
    </section>
    <?php if (count($pages) > 1) {
        echo include_template('paginator.php', [
            'pages'    => $pages,
            'cur_page' => $cur_page,
            'param'    => 'catid='.$category_id
        ]);
    }; ?>
</div>