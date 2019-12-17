<?php
/**
 * @var array $lot
 * @var array $error_bet
 * @var array $bets,
 * @var int $last_better
 */
?>

<?= include_template('nav.php', ['categories' => $categories]) ?>
<section class="lot-item container">
    <h2><?= $lot['name']; ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?= $lot['img']; ?>" width="730" height="548"
                     alt="<?= $lot['name']; ?>">
            </div>
            <p class="lot-item__category">Категория:
                <span><?= $lot['category']; ?></span></p>
            <p class="lot-item__description"><?= $lot['description']; ?></p>
        </div>
        <div class="lot-item__right">
            <?php $timer = get_timer_state($lot); ?>
            <div class="lot-item__state">
                <div class="lot-item__timer timer <?= $timer['state']; ?>">
                    <?= $timer['message']; ?>                </div>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost">
                            <?php $current_price = $lot['max_bet'] ??
                                $lot['bet_start'];
                            echo price_format($current_price); ?>
                        </span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка
                        <span> <?= price_format($lot['min_next_bet'])
                            .' р'; ?></span>
                    </div>
                </div>
                <?php
                if (
                    !isset($_SESSION['user']) or
                    $timer['class'] !== '' or
                    $lot['owner_id'] === (int)$_SESSION['id'] or
                    $last_better === (int)$_SESSION['id']
                ) {
                    $classname = 'visually-hidden';
                } ?>
                <form class="lot-item__form <?= $classname ?? ''; ?>"
                      action="/lot.php?id=<?= $lot['id']; ?>" method="post"
                      autocomplete="off">
                    <?php $classname = $error_bet === null ? ''
                        : "form__item--invalid"; ?>
                    <p class="lot-item__form-item form__item <?= $classname; ?>">
                        <label for="cost">Ваша ставка</label>
                        <input id="cost" type="text" name="cost"
                               placeholder="<?= price_format($lot['min_next_bet']); ?>">
                        <span class="form__error"><?= $error_bet; ?></span>
                    </p>
                    <button type="submit" class="button">Сделать ставку</button>
                </form>
            </div>
            <div class="history">
                <h3>История ставок (<span><?= count($bets); ?></span>)</h3>
                <table class="history__list">
                    <?php foreach ($bets as $bet): ?>
                        <tr class="history__item">
                            <td class="history__name"><?= $bet['name']; ?></td>
                            <td class="history__price"><?= price_format($bet['sum']); ?></td>
                            <td class="history__time"><?= $bet['time_back'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</section>