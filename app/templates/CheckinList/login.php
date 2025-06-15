<h1>管理者ログイン</h1>
<?php if (!empty($loginError)): ?>
    <div style="color:red;"><?= h($loginError) ?></div>
<?php endif; ?>
<form method="post">
    <label>ユーザー名: <input type="text" name="username" required></label><br>
    <label>パスワード: <input type="password" name="password" required></label><br>
    <button type="submit">ログイン</button>
</form>
