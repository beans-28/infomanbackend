<?php
require_once 'database_config/database.php';

echo "<h2>Database Connection Test</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "<p class='success'>Database connection successful!</p>";
        
        $tables = ['evac', 'head', 'fammem'];
        
        foreach ($tables as $table) {
            echo "<h3>Table: $table</h3>";
            
            // Check if table exists
            $stmt = $db->prepare("SHOW TABLES LIKE '$table'");
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                echo "<p class='success'>Table '$table' exists</p>";
                
                // Show table structure
                $stmt = $db->prepare("DESCRIBE $table");
                $stmt->execute();
                
                echo "<h4>Table Structure:</h4>";
                echo "<table>";
                echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . $row['Field'] . "</td>";
                    echo "<td>" . $row['Type'] . "</td>";
                    echo "<td>" . $row['Null'] . "</td>";
                    echo "<td>" . $row['Key'] . "</td>";
                    echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                
                $stmt = $db->prepare("SELECT COUNT(*) as count FROM $table");
                $stmt->execute();
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                echo "<p class='info'>Records in table: $count</p>";
                
                // first 3 records)
                $stmt = $db->prepare("SELECT * FROM $table LIMIT 3");
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    echo "<h4>Sample Data (first 3 records):</h4>";
                    echo "<table>";
                    
                    // Get column names
                    $first_row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($first_row) {
                        echo "<tr>";
                        foreach (array_keys($first_row) as $column) {
                            echo "<th>$column</th>";
                        }
                        echo "</tr>";
                        
                        // Display first row
                        echo "<tr>";
                        foreach ($first_row as $value) {
                            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                        }
                        echo "</tr>";
                        
                        // Display remaining rows
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            foreach ($row as $value) {
                                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                            }
                            echo "</tr>";
                        }
                    }
                    echo "</table>";
                }
                
            } else {
                echo "<p class='error'>Table '$table' does not exist</p>";
            }
            
            echo "<hr>";
        }
        
        // tsting foreign key relatios
        echo "<h3>Foreign Key Relationships</h3>";
        $stmt = $db->prepare("
            SELECT 
                TABLE_NAME,
                COLUMN_NAME,
                CONSTRAINT_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM 
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE 
                REFERENCED_TABLE_SCHEMA = 'new' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo "<table>";
            echo "<tr><th>Table</th><th>Column</th><th>References</th><th>Referenced Column</th></tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row['TABLE_NAME'] . "</td>";
                echo "<td>" . $row['COLUMN_NAME'] . "</td>";
                echo "<td>" . $row['REFERENCED_TABLE_NAME'] . "</td>";
                echo "<td>" . $row['REFERENCED_COLUMN_NAME'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='info'>No foreign key relationships found</p>";
        }
        
    } else {
        echo "<p class='error'>Database connection failed!</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}
?>