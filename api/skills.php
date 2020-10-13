<?php
include_once './db/database.php';
include_once './classes/skill.php';

$http_origin = ORIGIN;

header("Access-Control-Allow-Origin: $http_origin");
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
                array("code" => 404, "message" => "No Skills Found.")
            );
        }
        
    break;

        
        
    // POST
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        // Only available with access token
        if(!empty($data->token) && $data->token == TOKEN) {
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
                        array("code" => 201, "message" => "New Skill Created")
                    );
                } else {
                    http_response_code(503);
                    echo json_encode(
                        array("code" => 503, "message" => "Something Went Wrong. Try Again.")
                    );
                }
            } else{
                http_response_code(400);        
                echo json_encode(array("code" => 400, "message" => "Unable to Create Skill. Data is Incomplete."));
            } 
        // No token - No data for you, mkay?
        } else {
            http_response_code(401);        
            echo json_encode(array("code" => 401, "message" => "Unauthorized Request."));
        }
    
    break;
    
    
    
    // DELETE
    case 'DELETE':
       $data = json_decode(file_get_contents("php://input"));

        // Only available with access token
         if(!empty($data->token) && $data->token == TOKEN) {
            // Deny req if empty input
            if(
                !empty($data->id)
            ){
                // set course property values
                $skill->id = $data->id;

                if($skill->delete()) {
                    http_response_code(200);
                    echo json_encode(
                        array("code" => 200, "message" => "Skill Deleted")
                    );
                } else {
                    http_response_code(503);
                    echo json_encode(
                        array("code" => 503, "message" => "Something Went Wrong. Try Again.")
                    );           
                }
            } else {
                http_response_code(400);        
                echo json_encode(array("code" => 400, "message" => "Unable to Delete Skill. Data is Incomplete."));
            }
            // No token - No data for you, mkay?
        } else {
            http_response_code(401);        
            echo json_encode(array("code" => 401, "message" => "Unauthorized Request."));
        }

    break;


    // PUT
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        // Only available with access token
        if(!empty($data->token) && $data->token == TOKEN) {
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
                        array("code" => 200, "message" => "Skill Updated")
                    );
                } else {
                    http_response_code(503);
                    echo json_encode(
                        array("code" => 503, "message" => "Something Went Wrong. Try Again.")
                    );           
                }
            } else{
                http_response_code(400);        
                echo json_encode(array("code" => 400, "message" => "Unable to Update Skill. Data is Incomplete."));
            }
        // No token - No data for you, mkay?
        } else {
            http_response_code(401);        
            echo json_encode(array("code" => 401, "message" => "Unauthorized Request."));
        }

    break;
}
