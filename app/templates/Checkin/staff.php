<div style="text-align:center; margin-top:3em;">
    <h2>スタッフ出退勤</h2>
    <div>
      <h4>今月の出勤記録</h4>
      <?php if (!empty($checkinRecords)): ?>
        <div>
            <?php foreach ($checkinRecords as $rec): ?>
            <?php
              $dt = new DateTime($rec->check_in_at, new DateTimeZone('UTC'));
              $dt->setTimezone(new DateTimeZone('Asia/Tokyo'));
            ?>
            <div style="border:1px solid #ccc; border-radius:6px; padding:10px 14px; margin-bottom:10px; max-width:320px; margin-left:auto; margin-right:auto; background:#fafbfc;">
              <div style="font-weight:bold; color:#2a7ae2;">
              <?= h($dt->format('Y-m-d')) ?> <?= h(strtoupper($rec->type)) ?>
              </div>
              <div style="color:#333; margin-top:4px;">
              <?= h($dt->format('H:i')) ?>
              </div>
            </div>
            <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div style="color:#888;">今月の記録はありません</div>
      <?php endif; ?>
    </div>
    <form id="checkoutForm" method="post" onsubmit="return doCheckout(event);">
        <button type="submit" style="font-size:1.3em; margin-top:8px">退勤</button>
    </form>
    <div id="checkoutResult" style="margin-top:1.5em; font-size:1.1em; color:#2a7ae2;"></div>
    <script>
    async function doCheckout(e) {
        e.preventDefault();
        const resultDiv = document.getElementById('checkoutResult');
        resultDiv.textContent = '';
        try {
            const res = await fetch('/api/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': <?= json_encode($this->request->getAttribute('csrfToken')) ?>
                },
                body: JSON.stringify({})
            });
            const data = await res.json();
            if (res.ok && data.success) {
                resultDiv.textContent = 'お疲れ様でした';
                resultDiv.style.color = '#2a7ae2';
            } else {
                resultDiv.textContent = data.error || 'エラーが発生しました';
                resultDiv.style.color = 'red';
            }
        } catch (err) {
            resultDiv.textContent = '通信エラー: ' + err;
            resultDiv.style.color = 'red';
        }
        return false;
    }
    </script>
    <div style="margin-top:2em;">
        <a href="/checkin/logout">ログアウト</a>
    </div>
</div>
