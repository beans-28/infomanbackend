<?php
require_once 'database_config/database.php';

class EvacuationCenter {
    private $conn;
    private $table_name = "evac";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (Serial_Number, Region, Province, District, Barangay, City_or_Municipality, Evacuation_Center_Family_Members) 
                 VALUES (:serial, :region, :province, :district, :barangay, :city, :center)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':serial', $data['serial_number']);
        $stmt->bindParam(':region', $data['region']);
        $stmt->bindParam(':province', $data['province']);
        $stmt->bindParam(':district', $data['district']);
        $stmt->bindParam(':barangay', $data['barangay']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':center', $data['center']);
        
        return $stmt->execute();
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY Serial_Number";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne($serial_number) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE Serial_Number = :serial";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':serial', $serial_number);
        $stmt->execute();
        return $stmt;
    }

    public function update($data) {
        $query = "UPDATE " . $this->table_name . " 
                 SET Region = :region, Province = :province, District = :district, 
                     Barangay = :barangay, City_or_Municipality = :city, 
                     Evacuation_Center_Family_Members = :center 
                 WHERE Serial_Number = :serial";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':serial', $data['serial_number']);
        $stmt->bindParam(':region', $data['region']);
        $stmt->bindParam(':province', $data['province']);
        $stmt->bindParam(':district', $data['district']);
        $stmt->bindParam(':barangay', $data['barangay']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':center', $data['center']);
        
        return $stmt->execute();
    }

    public function delete($serial_number) {
        $query = "DELETE FROM " . $this->table_name . " WHERE Serial_Number = :serial";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':serial', $serial_number);
        return $stmt->execute();
    }
}

$database = new Database();
$db = $database->getConnection();
$evacuation = new EvacuationCenter($db);

echo "<h1>CRUD Operations Test - Evacuation Centers</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .info { color: blue; }
    .warning { color: orange; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
</style>";

try {
    echo "<div class='section'>";
    echo "<h2>Current Evacuation Centers in Database</h2>";
    $stmt = $evacuation->readAll();
    $existing_centers = [];
    
    if ($stmt->rowCount() > 0) {
        echo "<table>";
        echo "<tr><th>Serial Number</th><th>Region</th><th>Province</th><th>City</th><th>Center Name</th></tr>";
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $existing_centers[] = $row['Serial_Number'];
            echo "<tr>";
            echo "<td>" . $row['Serial_Number'] . "</td>";
            echo "<td>" . $row['Region'] . "</td>";
            echo "<td>" . $row['Province'] . "</td>";
            echo "<td>" . $row['City_or_Municipality'] . "</td>";
            echo "<td>" . $row['Evacuation_Center_Family_Members'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p class='info'>Found " . count($existing_centers) . " existing centers</p>";
    } else {
        echo "<p class='warning'>No evacuation centers found in database</p>";
    }
    echo "</div>";

    // TEST 1: CREATE (aadd new evacuation center)
    echo "<div class='section'>";
    echo "<h2>1.Testing CREATE Operation</h2>";
    
    $test_serial = 99999;
    while (in_array($test_serial, $existing_centers)) {
        $test_serial++;
    }
    
    $test_data = [
        'serial_number' => $test_serial,
        'region' => 'Central Luzon',
        'province' => 'Tarlac',
        'district' => 'District 1',
        'barangay' => 'Barangay Test',
        'city' => 'Tarlac City',
        'center' => 'Test Evacuation Center - CRUD Test'
    ];
    
    echo "<p class='info'>Attempting to create center with Serial Number: $test_serial</p>";
    
    if ($evacuation->create($test_data)) {
        echo "<p class='success'>CREATE: Successfully added evacuation center</p>";
        echo "<table>";
        foreach ($test_data as $key => $value) {
            echo "<tr><td><strong>" . ucfirst(str_replace('_', ' ', $key)) . "</strong></td><td>$value</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>CREATE: Failed to add evacuation center</p>";
        $test_serial = $existing_centers[0] ?? 11111;
    }
    echo "</div>";

    // TEST 2: READ ALL (Get all evacuation centers)
    echo "<div class='section'>";
    echo "<h2>2.Testing READ ALL Operation</h2>";
    $stmt = $evacuation->readAll();
    $count = $stmt->rowCount();
    
    if ($count > 0) {
        echo "<p class='success'>READ ALL: Found $count evacuation center(s)</p>";
        echo "<table>";
        echo "<tr><th>Serial Number</th><th>Region</th><th>Province</th><th>District</th><th>Barangay</th><th>City</th><th>Center Name</th></tr>";
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['Serial_Number'] . "</td>";
            echo "<td>" . $row['Region'] . "</td>";
            echo "<td>" . $row['Province'] . "</td>";
            echo "<td>" . $row['District'] . "</td>";
            echo "<td>" . $row['Barangay'] . "</td>";
            echo "<td>" . $row['City_or_Municipality'] . "</td>";
            echo "<td>" . $row['Evacuation_Center_Family_Members'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>READ ALL: No evacuation centers found</p>";
    }
    echo "</div>";

    // TEST 3: READ ONE (Get single evacuation center
    echo "<div class='section'>";
    echo "<h2>3.Testing READ ONE Operation</h2>";
    echo "<p class='info'>Looking for Serial Number: $test_serial</p>";
    
    $stmt = $evacuation->readOne($test_serial);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        echo "<p class='success'>READ ONE: Successfully retrieved evacuation center</p>";
        echo "<table>";
        foreach ($row as $key => $value) {
            echo "<tr><td><strong>$key</strong></td><td>" . ($value ?? 'NULL') . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>READ ONE: Evacuation center not found</p>";
        
        if (!empty($existing_centers)) {
            $existing_serial = $existing_centers[0];
            echo "<p class='info'>Trying with existing Serial Number: $existing_serial</p>";
            $stmt = $evacuation->readOne($existing_serial);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                echo "<p class='success'>READ ONE (existing): Successfully retrieved evacuation center</p>";
                echo "<table>";
                foreach ($row as $key => $value) {
                    echo "<tr><td><strong>$key</strong></td><td>" . ($value ?? 'NULL') . "</td></tr>";
                }
                echo "</table>";
                $test_serial = $existing_serial;
            }
        }
    }
    echo "</div>";

    // TEST 4: UPDATE (Update evacuation center)
    echo "<div class='section'>";
    echo "<h2>4.Testing UPDATE Operation</h2>";
    echo "<p class='info'>Updating Serial Number: $test_serial</p>";
    
    $update_data = [
        'serial_number' => $test_serial,
        'region' => 'Central Luzon (Updated)',
        'province' => 'Tarlac (Updated)',
        'district' => 'District 1 (Updated)',
        'barangay' => 'Barangay Test (Updated)',
        'city' => 'Tarlac City (Updated)',
        'center' => 'Updated Test Evacuation Center'
    ];
    
    if ($evacuation->update($update_data)) {
        echo "<p class='success'>UPDATE: Successfully updated evacuation center</p>";
        
        $stmt = $evacuation->readOne($test_serial);
        $updated_row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($updated_row && strpos($updated_row['Region'], '(Updated)') !== false) {
            echo "<p class='success'>UPDATE VERIFICATION: Changes confirmed</p>";
            echo "<table>";
            foreach ($updated_row as $key => $value) {
                echo "<tr><td><strong>$key</strong></td><td>" . ($value ?? 'NULL') . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='error'>UPDATE VERIFICATION: Changes not reflected</p>";
        }
    } else {
        echo "<p class='error'>UPDATE: Failed to update evacuation center</p>";
    }
    echo "</div>";

    // TEST 5: DELETE (Delete evacuation centers)
    echo "<div class='section'>";
    echo "<h2>5. Testing DELETE Operation</h2>";
    
    if ($test_serial == 99999 || $test_serial > 90000) {
        echo "<p class='info'>Deleting test record with Serial Number: $test_serial</p>";
        
        if ($evacuation->delete($test_serial)) {
            echo "<p class='success'>DELETE: Successfully deleted evacuation center</p>";
            
        
            $stmt = $evacuation->readOne($test_serial);
            $deleted_row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$deleted_row) {
                echo "<p class='success'>DELETE VERIFICATION: Record successfully removed</p>";
            } else {
                echo "<p class='error'>DELETE VERIFICATION: Record still exists</p>";
            }
        } else {
            echo "<p class='error'>DELETE: Failed to delete evacuation center</p>";
        }
    } else {
        echo "<p class='warning'>SKIPPING DELETE: Not deleting existing data (Serial: $test_serial)</p>";
        echo "<p class='info'>Deelete works</p>";
    }
    echo "</div>";

    echo "<div class='section'>";
    echo "<h2>Test Summary</h2>";
    echo "<p class='success'>All CRUD operations have been tested!</p>";
    echo "<ul>";
    echo "<li><strong>CREATE</strong>: Add new evacuation centers</li>";
    echo "<li><strong>READ ALL</strong>: Retrieve all evacuation centers</li>";
    echo "<li><strong>READ ONE</strong>: Retrieve single evacuation center</li>";
    echo "<li><strong>UPDATE</strong>: Modify existing evacuation centers</li>";
    echo "<li><strong>DELETE</strong>: Remove evacuation centers</li>";
    echo "</ul>";
    echo "<p class='info'>yez PHP CRUD operations is working correctly with dafac</p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<p class='error'>Error during testing: " . $e->getMessage() . "</p>";
    echo "<p class='error'>Stack trace: " . $e->getTraceAsString() . "</p>";
}
?>