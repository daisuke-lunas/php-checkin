<h1>いらっしゃいませ！</h1>
<?php if (isset($loginUrl)): ?>
    <a href="<?= h($loginUrl) ?>">
        <?= $this->Html->image('btn_login_base.png', ['alt' => 'LINEでログイン']) ?>
    </a>
<?php else: ?>
    <h2><?= h($message) ?></h2>
    <?php if (!empty($showLogout)): ?>
        <form method="post" action="/logout">
            <button type="submit">ログアウト</button>
        </form>
    <?php endif; ?>
<?php endif; ?>
