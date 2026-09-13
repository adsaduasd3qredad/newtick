<?php
$str = "Ã¢â‚¬Å“Ã Â¸â€ Ã Â¸Â²Ã Â¸Â§Ã Â¹â‚¬Ã Â¸Â«Ã Â¸â„¢Ã Â¸Â·Ã Â¸Â­Ã¢â‚¬Â  Ã Â¸â€žÃ Â¸Â¹Ã Â¹Ë†Ã Â¸Â«Ã Â¸Â¹Ã Â¸â€¢Ã Â¸Â°Ã Â¸Â¥Ã Â¸Â¸Ã Â¸Â¢Ã Â¸Â­Ã Â¸Â§Ã Â¸Â Ã Â¸Â²Ã Â¸Â¨";
echo "1: " . mb_convert_encoding($str, "latin1", "utf-8") . "\n";
echo "2: " . mb_convert_encoding(mb_convert_encoding($str, "latin1", "utf-8"), "latin1", "utf-8") . "\n";
echo "3: " . mb_convert_encoding($str, "windows-1252", "utf-8") . "\n";
$pdo = new PDO("mysql:host=127.0.0.1;dbname=ticket_booking", "root", ""); 
$stmt = $pdo->query("SELECT title_th FROM movies LIMIT 3"); 
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r) {
    echo $r['title_th'] . "\n";
    echo "  L1: " . mb_convert_encoding($r['title_th'], 'latin1', 'utf-8') . "\n";
    echo "  L2: " . mb_convert_encoding(mb_convert_encoding($r['title_th'], 'latin1', 'utf-8'), 'latin1', 'utf-8') . "\n";
    echo "  W1: " . mb_convert_encoding($r['title_th'], 'windows-1252', 'utf-8') . "\n";
    echo "  W2: " . mb_convert_encoding(mb_convert_encoding($r['title_th'], 'windows-1252', 'utf-8'), 'windows-1252', 'utf-8') . "\n";
}

