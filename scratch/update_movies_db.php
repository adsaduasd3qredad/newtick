<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=ticket_booking', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Add is_active if not exists
    $stmt = $pdo->query("SHOW COLUMNS FROM movies LIKE 'is_active'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec('ALTER TABLE movies ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER description');
    }
    
    // Drop status if exists
    $stmt = $pdo->query("SHOW COLUMNS FROM movies LIKE 'status'");
    if ($stmt->rowCount() > 0) {
        $pdo->exec('ALTER TABLE movies DROP COLUMN status');
    }
    
    echo 'Database updated successfully.';
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}

