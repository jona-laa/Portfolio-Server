<?php
include_once './db/database.php';
include_once './classes/project.php';

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



// Instantiate DB and project
$database = new Database();
$db = $database->getConnection();
$project = new Project($db);



/* API Endpoints
  * @param     {string}     $req_method     Request methos
*/
switch($req_method) {
    
    // GET
    case 'GET':
        // Get all or One project?
        if(isset($id)) {
            $result = $project->readOne($id);
        } else {
            $result = $project->read();
        }
        
        $rows = $result->rowCount();
          
        // If any projects found, Return JSON object
        if($rows>0){
        
            $projects_arr=array();
            $projects_arr["projects"]=array();
        
            while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                extract($row);
          
                $project_item=array(
                    "id" => $id,
                    "title" => $title,
                    "prj_url" => $prj_url,
                    "descr" => $descr,
                    "img_src" => $img_src,
                );
          
                array_push($projects_arr["projects"], $project_item);
            }

            http_response_code(200);
            echo json_encode($projects_arr);

        } else {
            http_response_code(404);
            echo json_encode(
                array("code" => 404, "message" => "No Projects Found.")
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
                !empty($data->title) &&
                !empty($data->prj_url) &&
                !empty($data->descr) &&
                !empty($data->img_src)
            ){
                // set project property values
                $project->title = $data->title;
                $project->prj_url = $data->prj_url;
                $project->descr = $data->descr;
                $project->img_src = $data->img_src;
        
                if($project->create()) {
                    http_response_code(201);
                        echo json_encode(
                        array("code" => 201, "message" => "New Project Created")
                    );
                } else {
                    http_response_code(503);
                    echo json_encode(
                        array("code" => 503, "message" => "Something Went Wrong. Try again.")
                    );
                }
            } else{
                http_response_code(400);        
                echo json_encode(array("code" => 400, "message" => "Unable to Create Project. Data is Incomplete."));
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
                $project->id = $data->id;

                if($project->delete()) {
                    http_response_code(200);
                    echo json_encode(
                        array("code" => 200, "message" => "Project Deleted")
                    );
                } else {
                    http_response_code(503);
                    echo json_encode(
                        array("code" => 503, "message" => "Something Went Wrong. Try Again.")
                    );           
                }
            } else {
                http_response_code(400);        
                echo json_encode(array("code" => 400, "message" => "Unable to Delete Project. Data is Incomplete."));
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
                !empty($data->title) &&
                !empty($data->prj_url) &&
                !empty($data->descr) &&
                !empty($data->img_src)
            ){
                // set project property values
                $project->id = $data->id;
                $project->title = $data->title;
                $project->prj_url = $data->prj_url;
                $project->descr = $data->descr;
                $project->img_src = $data->img_src;

                if($project->update()) {
                    http_response_code(200);
                    echo json_encode(
                        array("code" => 200, "message" => "Project Updated")
                    );
                } else {
                    http_response_code(503);
                    echo json_encode(
                        array("code" => 503, "message" => "Something Went Wrong. Try Again.")
                    );           
                }
            } else{
                http_response_code(400);        
                echo json_encode(array("code" => 400, "message" => "Unable to Update Project. Data is Incomplete."));
            }
        // No token - No data for you, mkay?
        } else {
            http_response_code(401);        
            echo json_encode(array("code" => 401, "message" => "Unauthorized Request."));
        }
        
    break;
}
