<?php
class Project {
    private $conn;
    private $table_name = PROJECTS;
  
    // Job Properties
    public $id;
    public $title;
    public $prj_url;
    public $descr;
    public $img_src;
  
    public function __construct($db){
        $this->conn = $db;
    }



    // Get All Jobs
    function read(){
        $query = "SELECT id, title, prj_url, descr, img_src FROM $this->table_name";
        $result = $this->conn->prepare($query);
        $result->execute();
        return $result;
    }



    // Get One Job
    function readOne($id){
        $query = "SELECT id, title, prj_url, descr, img_src FROM $this->table_name WHERE id=$id";        
        $result = $this->conn->prepare($query);
        $result->execute();
        return $result;
    }



    // Create New Job
    function create() {
       $query = "INSERT INTO 
       $this->table_name
            SET
                title=:title, prj_url=:prj_url, descr=:descr, img_src=:img_src";
  
        // Prepare query statement
        $statement = $this->conn->prepare($query);
    
        // Sanitize data
        $this->title=htmlspecialchars(strip_tags($this->title));
        $this->prj_url=htmlspecialchars(strip_tags($this->prj_url));
        $this->descr=htmlspecialchars(strip_tags($this->descr));
        $this->img_src=htmlspecialchars(strip_tags($this->img_src));
    
        // Bind values
        $statement->bindParam(":title", $this->title);
        $statement->bindParam(":prj_url", $this->prj_url);
        $statement->bindParam(":descr", $this->descr);
        $statement->bindParam(":img_src", $this->img_src);
    
        if($statement->execute()){
            return true;
        }
    
        return false;
    }



    // Delete Job
    function delete() {
        $query = "DELETE FROM $this->table_name WHERE id=:id";

        $statement = $this->conn->prepare($query);

        $this->id=htmlspecialchars(strip_tags($this->id));

        $statement->bindParam(':id', $this->id);

        if($statement->execute()){
            return true;
        }
    
        return false;
    }


    
    // Update Job
    function update() {
        $query = "UPDATE 
            $this->table_name
                SET
                    title = :title,
                    prj_url = :prj_url,
                    descr = :descr,
                    img_src = :img_src
                WHERE
                    id = :id";
    
        $statement = $this->conn->prepare($query);
    
        // Sanitize data
        $this->id=htmlspecialchars(strip_tags($this->id));
        $this->title=htmlspecialchars(strip_tags($this->title));
        $this->prj_url=htmlspecialchars(strip_tags($this->prj_url));
        $this->descr=htmlspecialchars(strip_tags($this->descr));
        $this->img_src=htmlspecialchars(strip_tags($this->img_src));

        // Bind Values
        $statement->bindParam(':id', $this->id);
        $statement->bindParam(':title', $this->title);
        $statement->bindParam(':prj_url', $this->prj_url);
        $statement->bindParam(':descr', $this->descr);
        $statement->bindParam(':img_src', $this->img_src);
    
        if($statement->execute()){
            return true;
        }
    
        return false;
    }
}
?>