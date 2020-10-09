<?php
class Course {
    private $conn;
    private $table_name = "courses";
  
    // Course Properties
    public $id;
    public $title;
    public $institution;
    public $date_start;
    public $date_end;
    public $descr;
  
    public function __construct($db){
        $this->conn = $db;
    }



    // Get All Courses
    function read(){
        $query = "SELECT id, title, institution, date_start, date_end, descr FROM $this->table_name";
        $result = $this->conn->prepare($query);
        $result->execute();
        return $result;
    }



    // Get One Course
    function readOne($id){
        $query = "SELECT id, title, institution, date_start, date_end, descr FROM $this->table_name WHERE id=$id";        
        $result = $this->conn->prepare($query);
        $result->execute();
        return $result;
    }



    // Create New Course
    function create() {
       $query = "INSERT INTO 
       $this->table_name
            SET
                title=:title, institution=:institution, date_start=:date_start, date_end=:date_end, descr=:descr";
  
        // Prep that Query yo
        $statement = $this->conn->prepare($query);
    
        // Better Sanitize them Datas
        $this->title=htmlspecialchars(strip_tags($this->title));
        $this->institution=htmlspecialchars(strip_tags($this->institution));
        $this->date_start=htmlspecialchars(strip_tags($this->date_start));
        $this->date_end=htmlspecialchars(strip_tags($this->date_end));
        $this->descr=htmlspecialchars(strip_tags($this->descr));
    
        // Bind those values my dude
        $statement->bindParam(":title", $this->title);
        $statement->bindParam(":institution", $this->institution);
        $statement->bindParam(":date_start", $this->date_start);
        $statement->bindParam(":date_end", $this->date_end);
        $statement->bindParam(":descr", $this->descr);
    
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
                    title = :title,
                    institution = :institution,
                    date_start = :date_start,
                    date_end = :date_end,
                    descr = :descr
                WHERE
                    id = :id";
    
        $statement = $this->conn->prepare($query);
    
        // Sanitize Them Stings, Son 
        $this->title=htmlspecialchars(strip_tags($this->title));
        $this->institution=htmlspecialchars(strip_tags($this->institution));
        $this->date_start=htmlspecialchars(strip_tags($this->date_start));
        $this->date_end=htmlspecialchars(strip_tags($this->date_end));
        $this->descr=htmlspecialchars(strip_tags($this->descr));
        $this->id=htmlspecialchars(strip_tags($this->id));

        // Bind Values
        $statement->bindParam(':title', $this->title);
        $statement->bindParam(':institution', $this->institution);
        $statement->bindParam(':date_start', $this->date_start);
        $statement->bindParam(':date_end', $this->date_end);
        $statement->bindParam(':descr', $this->descr);
        $statement->bindParam(':id', $this->id);
    
        if($statement->execute()){
            return true;
        }
    
        return false;
    }
}
?>