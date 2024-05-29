SELECT * 
FROM mensaje 
WHERE chat_id = ? 
ORDER BY timestamp DESC 
LIMIT 1;
