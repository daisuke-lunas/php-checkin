<h1>本日のチェックイン一覧</h1>
<p>日付: <?= h($today) ?></p>
<table border="1">
    <tr>
        <th>ID</th>
        <th>ユーザー名</th>
        <th>チェックイン時刻</th>
        <th>タイプ</th>
    </tr>
    <?php foreach ($checkins as $checkin): ?>
    <tr>
        <td><?= h($checkin->id) ?></td>
        <td><?= h($checkin->user_name) ?></td>
        <td><?= h($checkin->check_in_at) ?></td>
        <td><?= h($checkin->type) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
