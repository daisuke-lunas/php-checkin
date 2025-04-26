<!DOCTYPE html>
<html>
<head>
    <title>Checkin</title>
    <?= $this->Html->css('style.css') ?>
</head>
<body>
    <h3 id="status">ログインしてください</h3>
    <div id="login-form">
    <?= $this->Form->create(null, ['url' => ['controller' => 'Checkin', 'action' => 'saveCheckin'], 'id' => 'loginForm']) ?>
    <?= $this->Form->control('username', ['label' => 'ユーザー名', 'required' => true]) ?>
    <?= $this->Form->control('password', ['type' => 'password', 'label' => 'パスワード', 'required' => true]) ?>
    <?= $this->Form->button('ログイン') ?>
    <?= $this->Form->end() ?>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const status = document.getElementById('status');
            status.textContent = "処理中...";

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.auth === "OK") {
                    status.textContent = `${data.display_name}さん、いらっしゃいませ`;
                    document.getElementById('login-form').style.display = 'none';
                } else {
                    status.textContent = "認証に失敗しました";
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>