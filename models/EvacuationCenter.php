<?php
class EvacuationCenter {
    private $conn;
    private $table_name = "evac";

    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE
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

    // READ
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY Serial_Number";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ
    public function readOne($serial_number) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE Serial_Number = :serial";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':serial', $serial_number);
        $stmt->execute();
        return $stmt;
    }

    // UPDATE
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

    // DELETE 
    public function delete($serial_number) {
        $query = "DELETE FROM " . $this->table_name . " WHERE Serial_Number = :serial";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':serial', $serial_number);
        return $stmt->execute();
    }
}
?>