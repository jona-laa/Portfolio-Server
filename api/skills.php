<?php
include_once './db/database.php';
include_once './classes/skill.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
  
$req_method = $_SERVER['REQUEST_METHOD'];



// Get value of query parameter id
if(isset($_GET['id'])) {
    $id = $_GET['id'];
}



// Instantiate DB and skill
$database = new Database();
$db = $database->getConnection();
$skill = new Skill($db);



/* API Endpoints
  * @param     {string}     $req_method     Request methos
*/
switch($req_method) {
    
    // GET
    case 'GET':
        // Get all or One skill?
        if(isset($id)) {
            $result = $skill->readOne($id);
        } else {
            $result = $skill->read();
        }
        
        $rows = $result->rowCount();
          
        // If any skills found, Return JSON object
        if($rows>0){
        
            $skills_arr=array();
            $skills_arr["skills"]=array();
        
            while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                extract($row);
          
                $skill_item=array(
                    "id" => $id,
                    "skill" => $skill,
                    "icon" => $icon
                );
          
                array_push($skills_arr["skills"], $skill_item);
            }

            http_response_code(200);
            echo json_encode($skills_arr);

        } else {
            http_response_code(404);
            echo json_encode(
                array("code" => 404, "message" => "No skills found.")
            );
        }
        break;

        
        
    // POST
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        // Deny req if empty input
        if(
            !empty($data->skill) &&
            !empty($data->icon)
        ){
            // set skill property values
            $skill->skill = $data->skill;
            $skill->icon = $data->icon;
    
            if($skill->create()) {
                http_response_code(201);
                    echo json_encode(
                    array("code" => 201, "message" => "New skill created")
                );
            } else {
                http_response_code(503);
                echo json_encode(
                    array("code" => 503, "message" => "Something went wrong. Try again.")
                );
            }
        } else{
            http_response_code(400);        
            echo json_encode(array("code" => 400, "message" => "Unable to create skill. Data is incomplete."));
        }
        break;
    
    
    
    // DELETE
    case 'DELETE':
        if(!isset($id)) {
            http_response_code(510);
            echo json_encode(
                array("code" => 510, "message" => "No id was sent")
            );
        } else {
            if($skill->delete($id)) {
                http_response_code(200);
                echo json_encode(
                    array("code" => 200, "message" => "skill deleted")
                );
            } else {
                http_response_code(503);
                echo json_encode(
                    array("code" => 503, "message" => "Sever error. Try again.")
                );
            }
        }
        break;
    


    // PUT
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        // Deny req if empty input
        if(
            !empty($data->id) &&
            !empty($data->skill) &&
            !empty($data->icon)
        ){
            // set skill property values
            $skill->id = $data->id;
            $skill->skill = $data->skill;
            $skill->icon = $data->icon;

            if($skill->update()) {
                http_response_code(200);
                echo json_encode(
                    array("code" => 200, "message" => "skill updated")
                );
            } else {
                http_response_code(503);
                echo json_encode(
                    array("code" => 503, "message" => "Sever error. Try again.")
                );           
            }
        } else{
            http_response_code(400);        
            echo json_encode(array("code" => 400, "message" => "Unable to update skill. Data is incomplete."));
        }
    break;
}
