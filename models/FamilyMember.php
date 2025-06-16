<?php
class FamilyMember {
    private $conn;
    private $table_name = "fammem";

    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE - Add new family member
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (Family_Member_ID, Family_Members, Relation_to_Family_Head, Age, Sex, 
                  Educational_Attainment, Occupational_Skills, Remarks) 
                 VALUES (:member_id, :name, :relation, :age, :sex, :education, :skills, :remarks)";
        
        $stmt = $this->conn->prepare($query);
        
        foreach($data as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }
        
        return $stmt->execute();
    }

    // READ - Get all family members
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY Family_Member_ID";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // UPDATE - Update family member
    public function update($data) {
        $query = "UPDATE " . $this->table_name . " 
                 SET Family_Members = :name, Relation_to_Family_Head = :relation, 
                     Age = :age, Sex = :sex, Educational_Attainment = :education, 
                     Occupational_Skills = :skills, Remarks = :remarks 
                 WHERE Family_Member_ID = :member_id";
        
        $stmt = $this->conn->prepare($query);
        
        foreach($data as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }
        
        return $stmt->execute();
    }

    // DELETE - Delete family member
    public function delete($member_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE Family_Member_ID = :member_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':member_id', $member_id);
        return $stmt->execute();
    }
}
?>