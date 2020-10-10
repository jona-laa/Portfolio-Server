<?php
include_once './db/database.php';
include_once './classes/bio.php';

header("Access-Control-Allow-Origin: *");
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
                array("code" => 404, "message" => "No bios found.")
            );
        }
        
    break;

        
        
    // POST
    case 'POST':
        // Input Data
        $data = json_decode(file_get_contents("php://input"));

        // Deny req if empty input
        if(
            !empty($data->heading) &&
            !empty($data->bio) &&
            !empty($data->img_src) &&
            isset($data->published)
            ){
            // set bio property values
            $bio->heading = $data->heading;
            $bio->bio = $data->bio;
            $bio->img_src = $data->img_src;
            $bio->published = $data->published;
    
            if($bio->create()) {
                http_response_code(201);
                    echo json_encode(
                    array("code" => 201, "message" => "New bio created")
                );
            } else {
                http_response_code(503);
                echo json_encode(
                    array("code" => 503, "message" => "Something went wrong. Try again.")
                );
            }
        } else{
            http_response_code(400);        
            echo json_encode(array("code" => 400, "message" => "Unable to create bio. Data is incomplete."));
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
            if($bio->delete($id)) {
                http_response_code(200);
                echo json_encode(
                    array("code" => 200, "message" => "bio deleted")
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
        // Input Data
        $data = json_decode(file_get_contents("php://input"));

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
                if(!empty($data->published)) {
                    $bio->published = $data->published;
                }

                if($bio->update()) {
                    http_response_code(200);
                    echo json_encode(
                        array("code" => 200, "message" => "bio updated")
                    );
                } else {
                    http_response_code(503);
                    echo json_encode(
                        array("code" => 503, "message" => "Sever error. Try again.")
                    );           
                }
        } else {
            http_response_code(400);        
            echo json_encode(array("code" => 400, "message" => "Unable to update bio. Data is incomplete."));
        }
        
    break;
}
