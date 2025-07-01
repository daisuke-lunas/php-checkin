<style>
.checkinlist-flex {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    width: 100%;
    min-height: 60vh;
}
.checkinlist-left, .checkinlist-right {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    padding: 16px;
    flex: 1 1 320px;
    min-width: 280px;
}
.checkinlist-left {
    max-width: 48%;
}
.checkinlist-right {
    max-width: 48%;
}
@media (max-width: 900px) {
    .checkinlist-flex {
        flex-direction: column;
    }
    .checkinlist-left, .checkinlist-right {
        max-width: 100%;
    }
}
.btn-calc {
    background: #2a7ae2;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 0.4em 1.2em;
    font-size: 1em;
    margin-left: 0.5em;
    cursor: pointer;
    transition: background 0.2s;
}
.btn-calc:hover {
    background: #185fa7;
}
</style>
<div class="checkinlist-flex">
  <div class="checkinlist-left">
    <h2>本日のチェックイン一覧</h2>
    <p>日付: <?= h($today) ?></p>
    <table border="1" style="width:100%; text-align:center;">
        <tr>
            <th>ユーザー名</th>
            <th>チェックイン時刻</th>
            <th>タイプ</th>
            <th></th>
        </tr>
        <?php foreach ($checkins as $checkin): ?>
        <?php
            // チェックイン時刻をJSTでhh:mm:ss表記に変換
            $dt = new DateTime($checkin->check_in_at, new DateTimeZone('UTC'));
            $dt->setTimezone(new DateTimeZone('Asia/Tokyo'));
            $jstTime = $dt->format('H:i');
            // 割引メッセージをhasManyアソシエーションから「先月分」を探して生成
            $lastMonth = date('Ym', strtotime('-1 month'));
            $lastCount = 0;
            if (!empty($checkin->checkin_user_monthly_summary)) {
                foreach ($checkin->checkin_user_monthly_summary as $summary) {
                    if ($summary->yyyymm == $lastMonth && $summary->type === 'in') {
                        $lastCount = (int)($summary->total_count ?? 0);
                        break;
                    }
                }
            }
            $discountMsg = '';
            if ($lastCount >= 5) {
                $discountMsg = '（先月' . $lastCount . '回: ここから200円割引）';
            } elseif ($lastCount >= 2) {
                $discountMsg = '（先月' . $lastCount . '回: ここから100円割引）';
            }
        ?>
        <tr>
            <td><?= h($checkin->user_name) ?></td>
            <td><?= h($jstTime) ?></td>
            <td><?= h($checkin->type) ?></td>
            <td>
                <button class="btn-calc" onclick="showStayInfo('<?= h($checkin->user_name) ?>', '<?= h($checkin->check_in_at) ?>', '<?= h($discountMsg) ?>')">料金計算</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
  </div>
  <div class="checkinlist-right" id="stayInfoBox">
    <h2>滞在時間・料金</h2>
    <div id="stayInfoContent" style="font-size:1.2em; color:#333; min-height:4em;">
      ユーザーを選択してください。
    </div>
    <div style="margin-top: 16px">
      追加料金は5時間分で打ち止め。7時間が最大料金
    </div>
    <div>
      ただし、550円以上お買い上げの場合、追加を1時間減らす。8時間で最大になる
    </div>
    <div style="margin-top:1em; font-size:1.1em;">
      現在時刻: <span id="currentTime"></span>
    </div>
    <script>
    function updateCurrentTime() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const min = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('currentTime').textContent = `${h}:${min}:${s}`;
    }
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);
    </script>
  </div>
</div>
<script>
function showStayInfo(userName, checkinAt, discountMsg = '') {
    // checkinAt: "YYYY-MM-DD HH:MM:SS" (UTC)
    // UTC文字列をDateとして解釈
    const checkinDateUtc = new Date(checkinAt.replace(/-/g, '/'));
    // 現在時刻（JST）
    const now = new Date();
    // UTC→JSTの差分（分）
    const jstOffsetMin = -now.getTimezoneOffset(); // 例: 540
    // チェックイン時刻をJSTに変換
    const checkinDateJst = new Date(checkinDateUtc.getTime() + jstOffsetMin * 60 * 1000);
    // 現在時刻もJST
    const nowJst = now;
    let diffMs = nowJst - checkinDateJst;
    if (diffMs < 0) diffMs = 0;
    const diffMins = Math.floor(diffMs / 60000);
    const hours = Math.floor(diffMins / 60);
    const mins = diffMins % 60;
    // 滞在時間表記
    let stayStr = `${hours}時間${mins}分`;
    // 料金計算
    let price = 1320;
    let extraMins = 0;
    let overStr = '';
    let maxStr = '';
    if (diffMins > 120) {
      const overMins = diffMins - 120;
      const overHours = Math.floor(overMins / 60);
      const overRemainMins = overMins % 60;
      overStr = `（${overHours > 0 ? overHours + '時間' : ''}${overRemainMins}分超過）`;
      stayStr += overStr;
    }
    if (diffMins > 120) {
      extraMins = diffMins - 120;
      let extraMinUnit = extraMins / 30;
      price += Math.ceil(extraMinUnit) * 165;
    }
    // 7時間(420分)で打ち止め 2970円
    if (diffMins >= 420) {
      price = 2970;
      maxStr = '<br><span style="color:red;font-size:0.95em;">※最大料金</span>';
    }
    // 割引額の表記をここに
    let discountHtml = '';
    if (discountMsg) {
      discountHtml = `<br><span style=\"color:#2a7ae2;font-size:0.95em;\">${discountMsg}</span>`;
    }
    document.getElementById('stayInfoContent').innerHTML =
        `<b>${userName}</b><br>滞在時間: <b>${stayStr}</b></br>基本料金: <b>￥${price.toLocaleString()}</b>${maxStr}${discountHtml}`;
}
</script>
