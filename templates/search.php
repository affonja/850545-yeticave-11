<?= include_template('nav.php', ['categories' => $categories]) ?>
<div class="container">
    <section class="lots">

        <h2>Результаты поиска по запросу «<span><?= $query; ?></span>»</h2>
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
            'pages'    => $pages,
            'cur_page' => $cur_page,
            'query'    => $query
        ]);
    }; ?>
</div>