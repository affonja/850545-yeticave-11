<?php
/**
 * @var array $winner
 * @var int $lot_id
 */
?>
<h1>Поздравляем с победой</h1>
<p>Здравствуйте, <?= $winner['user_name'] ?>></p>
<p>Ваша ставка для лота <a href="http:\\yeticave\lot.php?id=<?= $lot_id ?>">
        <?= $winner['lot_name'] ?></a> победила.</p>
<p>Перейдите по ссылке <a
            href="http:\\yeticave\bets.php?id=<?= $winner['user_id'] ?>">мои
        ставки</a>,
    чтобы связаться с автором объявления</p>
<small>Интернет Аукцион "YetiCave"</small>