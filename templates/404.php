<?= include_template('nav.php', ['categories' => $categories]) ?>
<section class="lot-item container">
    <h2><?= $error['header']; ?></h2>
    <p><?= $error['message']; ?></p>
</section>
