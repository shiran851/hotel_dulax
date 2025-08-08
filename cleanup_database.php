<?php
require_once 'config/database.php';

try {
    $db = getDB();
    
    // Drop unnecessary tables
    $tables_to_drop = [
        'room_pricing',
        'package_pricing', 
        'dining_pricing',
        'dining_reports'
    ];
    
    foreach ($tables_to_drop as $table) {
        try {
            $db->exec("DROP TABLE IF EXISTS $table");
            echo "✅ Dropped table: $table<br>";
        } catch (Exception $e) {
            echo "⚠️ Could not drop $table: " . $e->getMessage() . "<br>";
        }
    }
    
    // Remove price column from dining_reservations
    try {
        $db->exec("ALTER TABLE dining_reservations DROP COLUMN price");
        echo "✅ Removed price column from dining_reservations<br>";
    } catch (Exception $e) {
        echo "⚠️ Could not remove price column: " . $e->getMessage() . "<br>";
    }
    
    echo "<br>✅ Database cleanup completed!";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>