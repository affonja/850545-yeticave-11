<?php
/**
 * @var array $contacts
 * @var array $win_bets
 */
?>

<?= include_template('nav.php', ['categories' => $categories]) ?>
<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?php foreach ($lots as $lot): ?>
            <?php
            $time_remaining = get_time_remaining($lot['end_time']);
            $timer = [];
            if ($time_remaining['diff'] === 0) {
                $timer['class'] = 'rates__item--end';
                if (in_array($lot['bet_id'], $win_bets)) {
                    $timer['state'] = 'timer--win';
                    $timer['class'] = 'rates__item--win';
                }
            }
            ?>
            <tr class="rates__item <?= $timer['class'] ?? ''; ?>">
                <td class=" rates__info">
                    <div class="rates__img">
                        <img src="<?= $lot['img']; ?>" width="54" height="40"
                             alt="<?= $lot['name']; ?>">
                    </div>
                    <div>
                        <h3 class="rates__title">
                            <a href="/lot.php/?id=<?= $lot['lot_id']; ?>"><?= $lot['name']; ?></a>
                        </h3>
                        <?php if (isset($timer['state']) and
                            $timer['state'] === 'timer--win'
                        ): ?>
                            <p>
                                <?= $contacts[$lot['lot_id']]; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="rates__category">
                    <?= $lot['category']; ?>
                </td>
                <td class="rates__timer">
                    <?= include_template('timer.php', [
                        'lot'      => $lot,
                        'win_bets' => $win_bets
                    ]); ?>
                </td>
                <td class="rates__price">
                    <?= price_format($lot['sum'])
                    .' р'; ?>
                </td>
                <td class="rates__time">
                    <?= $lot['time_back']; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>