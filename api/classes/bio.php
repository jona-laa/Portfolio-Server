<?php
class Bio {
    private $conn;
    private $table_name = "bio";
  
    // Bio Properties
    public $id;
    public $heading;
    public $bio;
    public $img_src;
    public $published;
  
    public function __construct($db){
        $this->conn = $db;
    }



    // Get All Bio
    function read(){
        $query = "SELECT id, heading, bio, img_src, published FROM $this->table_name";
        $result = $this->conn->prepare($query);
        $result->execute();
        return $result;
    }



    // Get One Bio
    function readOne($id){
        $query = "SELECT id, heading, bio, img_src, published FROM $this->table_name WHERE id=$id";        
        $result = $this->conn->prepare($query);
        $result->execute();
        return $result;
    }



    // Get Post for Publishing (If there's more than one readOne is not realiable)
    function readPublished($published){
        $query = "SELECT id, heading, bio, img_src, published FROM $this->table_name WHERE published=$published";        
        $result = $this->conn->prepare($query);
        $result->execute();
        return $result;
    }



    // Create New Bio
    function create() {
       $query = "INSERT INTO 
       $this->table_name
            SET
                heading=:heading, bio=:bio, img_src=:img_src, published=:published";
  
        // Prep that Query statement
        $statement = $this->conn->prepare($query);
    
        // Sanitize data
        $this->heading=htmlspecialchars(strip_tags($this->heading));
        $this->bio=htmlspecialchars(strip_tags($this->bio));
        $this->img_src=htmlspecialchars(strip_tags($this->img_src));
        $this->published=htmlspecialchars(strip_tags($this->published));
    
        // Bind values
        $statement->bindParam(":heading", $this->heading);
        $statement->bindParam(":bio", $this->bio);
        $statement->bindParam(":img_src", $this->img_src);
        $statement->bindParam(":published", $this->published);
    
        if($statement->execute()){
            return true;
        }
    
        return false;
    }



    // Delete a Bio
    function delete($id) {
        $query = "DELETE FROM $this->table_name WHERE id=$id";
        $result = $this->conn->prepare($query);
        $result->execute();    
        return $result;
    }


    
    // Update Bio
    function update() {
        $query = "UPDATE 
            $this->table_name
                SET
                    heading = :heading,
                    bio = :bio,
                    img_src=:img_src,
                    published=:published
                WHERE
                    id = :id";
    
        $statement = $this->conn->prepare($query);
    
        // Sanitize Strings
        $this->id=htmlspecialchars(strip_tags($this->id));
        $this->heading=htmlspecialchars(strip_tags($this->heading));
        $this->bio=htmlspecialchars(strip_tags($this->bio));
        $this->img_src=htmlspecialchars(strip_tags($this->img_src));
        $this->published=htmlspecialchars(strip_tags($this->published));

        // Bind Values
        $statement->bindParam(':id', $this->id);
        $statement->bindParam(':heading', $this->heading);
        $statement->bindParam(':bio', $this->bio);
        $statement->bindParam(":img_src", $this->img_src);
        $statement->bindParam(":published", $this->published);
    
        if($statement->execute()){
            return true;
        }
    
        return false;
    }
}
?>