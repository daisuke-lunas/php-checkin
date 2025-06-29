INSERT INTO checkin_user_monthly_summary (
  yyyymm,
  user_id,
  user_ext_id,
  user_name,
  type,
  total_count
)
SELECT
  DATE_FORMAT(c.check_in_at, '%Y%m') AS yyyymm,
  c.user_id,
  u.ext_id AS user_ext_id,
  u.username AS user_name,
  c.type,
  COUNT(*) AS total_count
FROM checkins c
JOIN users u ON c.user_id = u.id
WHERE DATE_FORMAT(c.check_in_at, '%Y%m') = '202506'  -- ← 対象年月を指定
GROUP BY
  yyyymm,
  c.user_id,
  u.ext_id,
  u.username,
  c.type;
