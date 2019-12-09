<?php
/**
 * @var array $lot
 */
?>

<li class="lots__item lot">
    <div class="lot__image">
        <img src="<?= $lot['img']; ?>" width="350" height="260"
             alt="<?= $lot['name']; ?>">
    </div>
    <div class="lot__info">
        <span class="lot__category"><?= $lot['category']; ?></span>
        <h3 class="lot__title"><a class="text-link"
                                  href="/lot.php/?id=<?= $lot['id']; ?>"><?= $lot['name']; ?></a>
        </h3>
        <div class="lot__state">
            <div class="lot__rate">
                <span class="lot__amount">Стартовая цена</span> <!-- 12 ставок -->
                <span class="lot__cost">
                    <?= price_format($lot['bet_start']) // макс ставка
                    .' <b class="rub">р</b>'; ?></span>
            </div>
            <?php $timer = get_timer_state($lot); ?>
            <div class="lot__timer timer
            <?= $timer['state']; ?>">
                  <?= $timer['message']; ?>
            </div>
        </div>
    </div>
</li>
