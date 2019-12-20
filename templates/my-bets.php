<?php
/**
 * @var array $categories
 * @var array $lots
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
            $timer = [
                'state'   => '',
                'message' =>
                    sprintf("%02d", $time_remaining['h']).':'
                    .sprintf("%02d", $time_remaining['m']),
                'class'   => ''
            ];
            if ($time_remaining['diff'] === 0) {
                $timer['state'] = 'timer--end';
                $timer['message'] = 'Торги окончены';
                $timer['class'] = 'rates__item--end';
                if (isset($lot['bet_id']) and in_array($lot['bet_id'],
                        $win_bets)
                ) {
                    $timer['state'] = 'timer--win';
                    $timer['message'] = 'Ставка выиграла';
                    $timer['class'] = 'rates__item--win';
                }
            } elseif ($time_remaining['diff'] < 3600) {
                $timer['state'] = 'timer--finishing';
            }
            ?>
            <tr class="rates__item <?= $timer['class']; ?>">
                <td class=" rates__info">
                    <div class="rates__img">
                        <img src="<?= $lot['img']; ?>" width="54" height="40"
                             alt="<?= strip_tags($lot['name']); ?>">
                    </div>
                    <div>
                        <h3 class="rates__title">
                            <a href="/lot.php/?id=<?= $lot['lot_id']; ?>"><?= strip_tags($lot['name']); ?></a>
                        </h3>
                        <?php if ($timer['state'] === 'timer--win'): ?>
                            <p>
                                <?= strip_tags($contacts[$lot['lot_id']]); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="rates__category">
                    <?= $lot['category']; ?>
                </td>
                <td class="rates__timer">
                    <div class="timer <?= $timer['state']; ?>">
                        <?= $timer['message']; ?>
                    </div>
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