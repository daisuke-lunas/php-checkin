<style>
body {
    font-family: 'Segoe UI', 'Hiragino Kaku Gothic ProN', Meiryo, sans-serif;
    background: #f7f7f7;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 400px;
    margin: 40px auto;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    padding: 24px 16px 32px 16px;
    text-align: center;
}
h1 {
    font-size: 1.7em;
    margin-bottom: 0.5em;
    color: #2a7ae2;
}
h2 {
    font-size: 1.2em;
    color: #333;
    margin: 1em 0 0.5em 0;
}
h4 {
    font-size: 1em;
    color: #666;
    margin: 0.5em 0 1em 0;
}
button {
    background: #2a7ae2;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 0.5em 2em;
    font-size: 1em;
    margin-top: 1em;
    cursor: pointer;
    transition: background 0.2s;
    line-height: 1.3;
    box-sizing: border-box;
}
button:hover {
    background: #185fa7;
}
img {
    max-width: 100%;
    height: auto;
    margin: 1em 0;
    display: block;
}
.btn {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0;
    background: none;
    border: none;
    border-radius: 0;
    box-shadow: none;
    margin: 0 auto 0 auto;
    width: 100%;
    max-width: 320px;
}
.btn img {
    margin: 0;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(42,122,226,0.08);
}
@media (max-width: 500px) {
    .container {
        max-width: 98vw;
        padding: 10vw 2vw 12vw 2vw;
    }
    h1 {
        font-size: 1.2em;
    }
    h2, h4 {
        font-size: 1em;
    }
    button {
        width: 100%;
        font-size: 1em;
        padding: 0.7em 0;
    }
    .btn {
        max-width: 100%;
    }
}
</style>
<div class="container">
    <h1>いらっしゃいませ！</h1>
    <?php if (isset($loginUrl)): ?>
        <a href="<?= h($loginUrl) ?>" class="btn">
            <?= $this->Html->image('btn_login_base.png', ['alt' => 'LINEでログイン']) ?>
        </a>
    <?php else: ?>
        <h2><?= h($message) ?></h2>
        <h4><?= h($monthlyCountMessage) ?></h4>
        <?php if (!empty($showLogout)): ?>
            <form method="post" action="/logout">
                <button type="submit">ログアウト</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>
