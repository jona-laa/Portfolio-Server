<?php
include_once './db/database.php';
include_once './classes/course.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
  
$req_method = $_SERVER['REQUEST_METHOD'];



// Get value of query parameter id
if(isset($_GET['id'])) {
    $id = $_GET['id'];
}



// Instantiate DB and Course
$database = new Database();
$db = $database->getConnection();
$course = new Course($db);



/* API Endpoints
  * @param     {string}     $req_method     Request methos
*/
switch($req_method) {
    
    // GET
    case 'GET':
        // Get all or One Course?
        if(isset($id)) {
            $result = $course->readOne($id);
        } else {
            $result = $course->read();
        }
        
        $rows = $result->rowCount();
          
        // If any courses found, Return JSON object
        if($rows>0){
        
            $courses_arr=array();
            $courses_arr["courses"]=array();
        
            while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                extract($row);
          
                $course_item=array(
                    "id" => $id,
                    "title" => $title,
                    "institution" => $institution,
                    "date_start" => $date_start,
                    "date_end" => $date_end,
                    "descr" => $descr
                );
          
                array_push($courses_arr["courses"], $course_item);
            }

            http_response_code(200);
            echo json_encode($courses_arr);

        } else {
            http_response_code(404);
            echo json_encode(
                array("code" => 404, "message" => "No courses found.")
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
                !empty($data->institution) &&
                !empty($data->date_start) &&
                !empty($data->date_end) &&
                !empty($data->descr) 
            ){
                // set course property values
                $course->title = $data->title;
                $course->institution = $data->institution;
                $course->date_start = $data->date_start;
                $course->date_end = $data->date_end;
                $course->descr = $data->descr;
        
                if($course->create()) {
                    http_response_code(201);
                        echo json_encode(
                        array("code" => 201, "message" => "New course created")
                    );
                } else {
                    http_response_code(503);
                    echo json_encode(
                        array("code" => 503, "message" => "Something went wrong. Try again.")
                    );
                }
            } else {
                http_response_code(400);        
                echo json_encode(array("code" => 400, "message" => "Unable to create course. Data is incomplete."));
            }
            // No token - No data for you, mkay?
         } else {
            http_response_code(401);        
            echo json_encode(array("code" => 401, "message" => "Unauthorized request."));
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
                $course->id = $data->id;

                if($course->delete()) {
                    http_response_code(200);
                    echo json_encode(
                        array("code" => 200, "message" => "Course Deleted")
                    );
                } else {
                    http_response_code(503);
                    echo json_encode(
                        array("code" => 503, "message" => "Sever error. Try again.")
                    );           
                }
            } else {
                http_response_code(400);        
                echo json_encode(array("code" => 400, "message" => "Unable to delete course. Data is incomplete."));
            }
            // No token - No data for you, mkay?
        } else {
            http_response_code(401);        
            echo json_encode(array("code" => 401, "message" => "Unauthorized request."));
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
                !empty($data->institution) &&
                !empty($data->date_start) &&
                !empty($data->date_end) &&
                !empty($data->descr) 
            ){
                // set course property values
                $course->id = $data->id;
                $course->title = $data->title;
                $course->institution = $data->institution;
                $course->date_start = $data->date_start;
                $course->date_end = $data->date_end;
                $course->descr = $data->descr;

                if($course->update()) {
                    http_response_code(200);
                    echo json_encode(
                        array("code" => 200, "message" => "Course updated")
                    );
                } else {
                    http_response_code(503);
                    echo json_encode(
                        array("code" => 503, "message" => "Sever error. Try again.")
                    );           
                }
            } else {
                http_response_code(400);        
                echo json_encode(array("code" => 400, "message" => "Unable to update course. Data is incomplete."));
            }
            // No token - No data for you, mkay?
        } else {
            http_response_code(401);        
            echo json_encode(array("code" => 401, "message" => "Unauthorized request."));
        }

    break;
}
