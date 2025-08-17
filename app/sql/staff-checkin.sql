SELECT 
    users.id, 
    users.username, 
    checkins.type, 
    DATE_ADD(checkins.check_in_at, INTERVAL 9 HOUR) AS check_in_at_jst
FROM 
    checkins
JOIN 
    users ON checkins.user_id = users.id
WHERE 
    users.user_type = 'staff' 
    AND DATE_FORMAT(checkins.check_in_at, '%Y%m') = '202507'  -- 対象年月を指定
ORDER BY 
    users.id,
    checkins.check_in_at;
