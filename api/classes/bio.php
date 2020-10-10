<?php
class Bio {
    private $conn;
    private $table_name = "bio";
  
    // Bio Properties
    public $id;
    public $heading;
    public $bio;
    public $img_src;
  
    public function __construct($db){
        $this->conn = $db;
    }



    // Get All Bio
    function read(){
        $query = "SELECT id, heading, bio, img_src FROM $this->table_name";
        $result = $this->conn->prepare($query);
        $result->execute();
        return $result;
    }



    // Get One Course
    function readOne($id){
        var_dump($id);
        $query = "SELECT id, heading, bio, img_src FROM $this->table_name WHERE id=$id";        
        $result = $this->conn->prepare($query);
        $result->execute();
        return $result;
    }



    // Get Post for Publishing (If there's more than one readOne is not realiable)
    function readPublished($published){
        $query = "SELECT id, heading, bio, img_src FROM $this->table_name WHERE published=$published";        
        $result = $this->conn->prepare($query);
        $result->execute();
        return $result;
    }



    // Create New Course
    function create() {
       $query = "INSERT INTO 
       $this->table_name
            SET
                heading=:heading, bio=:bio, img_src=:img_src";
  
        // Prep that Query yo
        $statement = $this->conn->prepare($query);
    
        // Better Sanitize them Datas
        $this->heading=htmlspecialchars(strip_tags($this->heading));
        $this->bio=htmlspecialchars(strip_tags($this->bio));
        $this->img_src=htmlspecialchars(strip_tags($this->img_src));
    
        // Bind those values my dude
        $statement->bindParam(":heading", $this->heading);
        $statement->bindParam(":bio", $this->bio);
        $statement->bindParam(":img_src", $this->img_src);
    
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
                    heading = :heading,
                    bio = :bio,
                    img_src=:img_src
                WHERE
                    id = :id";
    
        $statement = $this->conn->prepare($query);
    
        // Sanitize Them Stings, Son 
        $this->id=htmlspecialchars(strip_tags($this->id));
        $this->heading=htmlspecialchars(strip_tags($this->heading));
        $this->bio=htmlspecialchars(strip_tags($this->bio));
        $this->img_src=htmlspecialchars(strip_tags($this->img_src));

        // Bind Values
        $statement->bindParam(':id', $this->id);
        $statement->bindParam(':heading', $this->heading);
        $statement->bindParam(':bio', $this->bio);
        $statement->bindParam(":img_src", $this->img_src);
    
        if($statement->execute()){
            return true;
        }
    
        return false;
    }
}
?>