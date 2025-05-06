<h1>Checkin system</h1>
<?php if (isset($loginUrl)): ?>
    <a href="<?= h($loginUrl) ?>">
        <?= $this->Html->image('btn_login_base.png', ['alt' => 'LINEでログイン']) ?>
    </a>
<?php else: ?>
    <h2><?= h($message) ?></h2>
<?php endif; ?>
