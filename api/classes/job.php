<?php
class Job {
    private $conn;
    private $table_name = "work";
  
    // Job Properties
    public $id;
    public $company;
    public $title;
    public $date_start;
    public $date_end;
    public $descr;
  
    public function __construct($db){
        $this->conn = $db;
    }



    // Get All Jobs
    function read(){
        $query = "SELECT id, company, title, date_start, date_end, descr FROM $this->table_name";
        $result = $this->conn->prepare($query);
        $result->execute();
        return $result;
    }



    // Get One Job
    function readOne($id){
        $query = "SELECT id, company, title, date_start, date_end, descr FROM $this->table_name WHERE id=$id";        
        $result = $this->conn->prepare($query);
        $result->execute();
        return $result;
    }



    // Create New Job
    function create() {
       $query = "INSERT INTO 
       $this->table_name
            SET
                company=:company, title=:title, date_start=:date_start, date_end=:date_end, descr=:descr";
  
        // Prepare query statement
        $statement = $this->conn->prepare($query);
    
        // Sanitize data
        $this->company=htmlspecialchars(strip_tags($this->company));
        $this->title=htmlspecialchars(strip_tags($this->title));
        $this->date_start=htmlspecialchars(strip_tags($this->date_start));
        $this->date_end=htmlspecialchars(strip_tags($this->date_end));
        $this->descr=htmlspecialchars(strip_tags($this->descr));
    
        // Bind values
        $statement->bindParam(":company", $this->company);
        $statement->bindParam(":title", $this->title);
        $statement->bindParam(":date_start", $this->date_start);
        $statement->bindParam(":date_end", $this->date_end);
        $statement->bindParam(":descr", $this->descr);
    
        if($statement->execute()){
            return true;
        }
    
        return false;
    }



    // Delete Job
    function delete($id) {
        $query = "DELETE FROM $this->table_name WHERE id=$id";
        $result = $this->conn->prepare($query);
        $result->execute();    
        return $result;
    }


    
    // Update Job
    function update() {
        $query = "UPDATE 
            $this->table_name
                SET
                    company = :company,
                    title = :title,
                    date_start = :date_start,
                    date_end = :date_end,
                    descr = :descr
                WHERE
                    id = :id";
    
        $statement = $this->conn->prepare($query);
    
        // Sanitize data
        $this->id=htmlspecialchars(strip_tags($this->id));
        $this->company=htmlspecialchars(strip_tags($this->company));
        $this->title=htmlspecialchars(strip_tags($this->title));
        $this->date_start=htmlspecialchars(strip_tags($this->date_start));
        $this->date_end=htmlspecialchars(strip_tags($this->date_end));
        $this->descr=htmlspecialchars(strip_tags($this->descr));

        // Bind Values
        $statement->bindParam(':id', $this->id);
        $statement->bindParam(':company', $this->company);
        $statement->bindParam(':title', $this->title);
        $statement->bindParam(':date_start', $this->date_start);
        $statement->bindParam(':date_end', $this->date_end);
        $statement->bindParam(':descr', $this->descr);
    
        if($statement->execute()){
            return true;
        }
    
        return false;
    }
}
?>