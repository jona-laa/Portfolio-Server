<?php
class Skill {
    private $conn;
    private $table_name = "skills";
  
    // Course Properties
    public $id;
    public $skill;
    public $icon;
  
    public function __construct($db){
        $this->conn = $db;
    }



    // Get All Courses
    function read(){
        $query = "SELECT id, skill, icon FROM $this->table_name";
        $result = $this->conn->prepare($query);
        $result->execute();
        return $result;
    }



    // Get One Course
    function readOne($id){
        $query = "SELECT id, skill, icon FROM $this->table_name WHERE id=$id";        
        $result = $this->conn->prepare($query);
        $result->execute();
        return $result;
    }



    // Create New Course
    function create() {
       $query = "INSERT INTO 
       $this->table_name
            SET
                skill=:skill, icon=:icon";
  
        // Prep that Query yo
        $statement = $this->conn->prepare($query);
    
        // Better Sanitize them Datas
        $this->skill=htmlspecialchars(strip_tags($this->skill));
        $this->icon=htmlspecialchars(strip_tags($this->icon));
    
        // Bind those values my dude
        $statement->bindParam(":skill", $this->skill);
        $statement->bindParam(":icon", $this->icon);
    
        if($statement->execute()){
            return true;
        }
    
        return false;
    }



    // Delete a Course
    function delete($id) {
        $query = "DELETE FROM $this->table_name WHERE id=$id";
        $result = $this->conn->prepare($query);
        $result->execute();    
        return $result;
    }


    
    // Update Course
    function update() {
        $query = "UPDATE 
            $this->table_name
                SET
                    skill = :skill,
                    icon = :icon
                WHERE
                    id = :id";
    
        $statement = $this->conn->prepare($query);
    
        // Sanitize Them Stings, Son 
        $this->skill=htmlspecialchars(strip_tags($this->skill));
        $this->icon=htmlspecialchars(strip_tags($this->icon));
        $this->id=htmlspecialchars(strip_tags($this->id));

        // Bind Values
        $statement->bindParam(':skill', $this->skill);
        $statement->bindParam(':icon', $this->icon);
        $statement->bindParam(':id', $this->id);
    
        if($statement->execute()){
            return true;
        }
    
        return false;
    }
}
?>