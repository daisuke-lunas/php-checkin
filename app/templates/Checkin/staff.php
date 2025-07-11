<div style="text-align:center; margin-top:3em;">
    <h2>スタッフ出退勤</h2>
    <div>
      <h4>今月の出勤記録</h4>
      <? // ここに、今月のチェックイン日付と時刻、
       ?>
    </div>
    <form id="checkoutForm" method="post" onsubmit="return doCheckout(event);">
        <button type="submit" style="font-size:1.3em;">退勤</button>
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
