<?php
class FamilyHead {
    private $conn;
    private $table_name = "head";

    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (Head_of_Fam_ID, Head_Name, Birthdate, Age, Birthplace, Sex, Mother_Maiden_Name, 
                  Occupation, Monthly_Family_Net_Income, ID_Presented, ID_Card_Number, Address, 
                  Contact_Number, FourPs_Beneficiary, Type_of_Ethnicity, Num_of_Older_Persons, 
                  Num_of_Pregnant_and_Lactating_Mothers, Num_of_PWDs_and_with_Medical_Conditions, Serial_Number) 
                 VALUES (:head_id, :name, :birthdate, :age, :birthplace, :sex, :maiden_name, :occupation, 
                         :income, :id_presented, :id_number, :address, :contact, :fourps, :ethnicity, 
                         :older_persons, :pregnant_mothers, :pwds, :serial_number)";
        
        $stmt = $this->conn->prepare($query);
        
        foreach($data as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }
        
        return $stmt->execute();
    }

    // READ
    public function readAll() {
        $query = "SELECT h.*, e.Evacuation_Center_Family_Members 
                 FROM " . $this->table_name . " h 
                 LEFT JOIN evac e ON h.Serial_Number = e.Serial_Number 
                 ORDER BY h.Head_of_Fam_ID";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // UPDAT
    public function update($data) {
        $query = "UPDATE " . $this->table_name . " 
                 SET Head_Name = :name, Birthdate = :birthdate, Age = :age, 
                     Birthplace = :birthplace, Sex = :sex, Mother_Maiden_Name = :maiden_name,
                     Occupation = :occupation, Monthly_Family_Net_Income = :income,
                     ID_Presented = :id_presented, ID_Card_Number = :id_number,
                     Address = :address, Contact_Number = :contact,
                     FourPs_Beneficiary = :fourps, Type_of_Ethnicity = :ethnicity,
                     Num_of_Older_Persons = :older_persons,
                     Num_of_Pregnant_and_Lactating_Mothers = :pregnant_mothers,
                     Num_of_PWDs_and_with_Medical_Conditions = :pwds
                 WHERE Head_of_Fam_ID = :head_id";
        
        $stmt = $this->conn->prepare($query);
        
        foreach($data as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }
        
        return $stmt->execute();
    }

    // DELETE
    public function delete($head_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE Head_of_Fam_ID = :head_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':head_id', $head_id);
        return $stmt->execute();
    }
}
?>