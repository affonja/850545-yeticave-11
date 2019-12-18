<?php
/**
 * @var array $lot
 * @var string $timer_class
 */
?>

<?php $time_remaining = get_time_remaining($lot['end_time']); ?>
<div class="<?= $timer_class; ?> timer <?= $time_remaining['h'] < 1
    ? 'timer--finishing' : '' ?>">
    <?= sprintf("%02d", $time_remaining['h']).':'
    .sprintf("%02d", $time_remaining['m']); ?>
</div>