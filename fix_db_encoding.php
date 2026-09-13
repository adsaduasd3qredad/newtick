<?php
$pdo = new PDO("mysql:host=127.0.0.1;dbname=ticket_booking", "root", ""); 

function fixString($val) {
    if (empty($val)) return $val;
    // Check if it looks double encoded (contains 'Ã')
    if (strpos($val, 'Ã') !== false) {
        $fixed = mb_convert_encoding(mb_convert_encoding($val, 'windows-1252', 'utf-8'), 'windows-1252', 'utf-8');
        // fallback to latin1 if windows-1252 failed? Usually windows-1252 covers latin1.
        return $fixed;
    }
    return $val;
}

$tables = [
    'movies' => ['title_th', 'title_en', 'description'],
    'bookings' => ['booker_name'],
    'users' => ['name']
];

foreach ($tables as $table => $columns) {
    echo "Processing $table...\n";
    $stmt = $pdo->query("SELECT id, " . implode(", ", $columns) . " FROM $table");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($rows as $row) {
        $updateNeeded = false;
        $updates = [];
        $params = [];
        
        foreach ($columns as $col) {
            $old = $row[$col];
            $new = fixString($old);
            if ($old !== $new) {
                $updateNeeded = true;
                $updates[] = "$col = ?";
                $params[] = $new;
                echo "  ID {$row['id']} [$col]:\n    OLD: $old\n    NEW: $new\n";
            }
        }
        
        if ($updateNeeded) {
            $params[] = $row['id'];
            $sql = "UPDATE $table SET " . implode(", ", $updates) . " WHERE id = ?";
            $updateStmt = $pdo->prepare($sql);
            $updateStmt->execute($params);
            echo "  -> Updated.\n";
        }
    }
}
echo "Done.\n";

