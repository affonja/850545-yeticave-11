<li class="lots__item lot">
    <div class="lot__image">
        <img src="<?= $lot['img']; ?>" width="350" height="260" alt="<?= $lot['name']; ?>">
    </div>
    <div class="lot__info">
        <span class="lot__category"><?= $lot['category']; ?></span>
        <h3 class="lot__title"><a class="text-link" href="pages/lot.html"><?= $lot['name']; ?></a>
        </h3>
        <div class="lot__state">
            <div class="lot__rate">
                <span class="lot__amount">Стартовая цена</span>
                <span class="lot__cost"><?= price_format($lot['price']); ?></span>
            </div>
            <?php $time_remaining = lot_lifetime($lot['enddate']); ?>
            <div class="lot__timer timer <?php if ($time_remaining[0] < 1) {
                echo 'timer--finishing';
            } ?>">
                <?= sprintf("%02d", $time_remaining[0]) . ':' . sprintf("%02d", $time_remaining[1]); ?>
            </div>
        </div>
    </div>
</li>
