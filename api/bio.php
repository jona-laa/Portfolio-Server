<?php
include_once './db/database.php';
include_once './classes/bio.php';

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

// Instantiate DB and bio
$database = new Database();
$db = $database->getConnection();
$bio = new Bio($db);



/* API Endpoints
  * @param     {string}     $req_method     Request methos
*/
switch($req_method) {
    
    // GET
    case 'GET':
        
        // Get all or One bio?
        if (!empty($id)) {
            $result = $bio->readOne($id);
        } elseif (!empty($_GET['published'])) {
            $result = $bio->readPublished($_GET['published']);
        } else {
            $result = $bio->read();
        }
        
        $rows = $result->rowCount();
          
        // If any bios found, Return JSON object
        if($rows>0){
        
            $bios_arr=array();
            $bios_arr["bios"]=array();
        
            while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                extract($row);
          
                $bio_item=array(
                    "id" => $id,
                    "heading" => $heading,
                    "bio" => $bio,
                    "img_src" => $img_src,
                    "published" => $published
                );
          
                array_push($bios_arr["bios"], $bio_item);
            }

            http_response_code(200);
            echo json_encode($bios_arr);

        } else {
            http_response_code(404);
            echo json_encode(
                array("code" => 404, "message" => "No Posts Found.")
            );
        }
        
    break;

        
        
    // POST
    case 'POST':
        // Input Data
        $data = json_decode(file_get_contents("php://input"));

        // Only available with access token
        if(!empty($data->token) && $data->token == TOKEN) {
            // Deny req if empty input
            if(
                !empty($data->heading) &&
                !empty($data->bio) &&
                !empty($data->img_src)
                ){
                // set bio property values
                $bio->heading = $data->heading;
                $bio->bio = $data->bio;
                $bio->img_src = $data->img_src;
                if(isset($data->published)) {
                    $bio->published = $data->published;
                }
        
                if($bio->create()) {
                    http_response_code(201);
                        echo json_encode(
                        array("code" => 201, "message" => "New bio created")
                    );
                } else {
                    http_response_code(503);
                    echo json_encode(
                        array("code" => 503, "message" => "Something Went Wrong. Try Again.")
                    );
                }
            } else{
                http_response_code(400);        
                echo json_encode(array("code" => 400, "message" => "Unable to Create Post. Data is Incomplete."));
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
                $bio->id = $data->id;

                if($bio->delete()) {
                    http_response_code(200);
                    echo json_encode(
                        array("code" => 200, "message" => "Post Deleted")
                    );
                } else {
                    http_response_code(503);
                    echo json_encode(
                        array("code" => 503, "message" => "Something Went Wrong. Try Again.")
                    );           
                }
            } else {
                http_response_code(400);        
                echo json_encode(array("code" => 400, "message" => "Unable to Delete Course. Data is Incomplete."));
            }
            // No token - No data for you, mkay?
        } else {
            http_response_code(401);        
            echo json_encode(array("code" => 401, "message" => "Unauthorized Request."));
        }

    break;
    


    // PUT
    case 'PUT':
        // Input Data
        $data = json_decode(file_get_contents("php://input"));

        // Only available with access token
        if(!empty($data->token) && $data->token == TOKEN) {
            // Deny req if empty input
            if(
                !empty($data->id) &&
                !empty($data->heading) &&
                !empty($data->bio) &&
                !empty($data->img_src)
            ){
                    // set bio property values
                    $bio->id = $data->id;
                    $bio->heading = $data->heading;
                    $bio->bio = $data->bio;
                    $bio->img_src = $data->img_src;
                    if(isset($data->published)) {
                        $bio->published = $data->published;
                    }

                    if($bio->update()) {
                        http_response_code(200);
                        echo json_encode(
                            array("code" => 200, "message" => "Post Updated")
                        );
                    } else {
                        http_response_code(503);
                        echo json_encode(
                            array("code" => 503, "message" => "Something Went Wrong. Try Again.")
                        );           
                    }
            } else {
                http_response_code(400);        
                echo json_encode(array("code" => 400, "message" => "Unable to Update Post. Data is Incomplete."));
            }
        // No token - No data for you, mkay?
        } else {
            http_response_code(401);        
            echo json_encode(array("code" => 401, "message" => "Unauthorized Request."));
        }
        
    break;
}
