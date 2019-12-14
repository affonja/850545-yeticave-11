<?= include_template('nav.php', ['categories' => $categories]) ?>
<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?php foreach ($bets as $bet): ?>
            <?php $timer = get_timer_state($bet, $user_id, $win_bets); ?>
            <tr class="rates__item
            <?= $timer['class']; ?>">
                <td class=" rates__info">
                    <div class="rates__img">
                        <img src="<?= $bet['img']; ?>" width="54" height="40"
                             alt="<?= $bet['name']; ?>">
                    </div>
                    <h3 class="rates__title"><a
                                href="/lot.php/?id=<?= $bet['lot_id']; ?>"><?= $bet['name']; ?></a>
                    </h3>
                </td>
                <td class="rates__category">
                    <?= $bet['category']; ?>
                </td>
                <td class="rates__timer">
                    <div class="timer <?= $timer['state']; ?>">
                        <?= $timer['message']; ?>
                    </div>
                </td>
                <td class="rates__price">
                    <?= price_format($bet['sum'])
                    .' р'; ?>
                </td>
                <td class="rates__time">
                    <?= $bet['time_back']; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
</main>