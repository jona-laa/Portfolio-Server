<?php
include_once './db/database.php';
include_once './classes/job.php';

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



// Instantiate DB and Job
$database = new Database();
$db = $database->getConnection();
$job = new Job($db);



/* API Endpoints
  * @param     {string}     $req_method     Request methos
*/
switch($req_method) {
    
    // GET
    case 'GET':
        // Get all or One job?
        if(isset($id)) {
            $result = $job->readOne($id);
        } else {
            $result = $job->read();
        }
        
        $rows = $result->rowCount();
          
        // If any jobs found, Return JSON object
        if($rows>0){
        
            $jobs_arr=array();
            $jobs_arr["jobs"]=array();
        
            while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                extract($row);
          
                $job_item=array(
                    "id" => $id,
                    "company" => $company,
                    "title" => $title,
                    "date_start" => $date_start,
                    "date_end" => $date_end,
                    "descr" => $descr,
                );
          
                array_push($jobs_arr["jobs"], $job_item);
            }

            http_response_code(200);
            echo json_encode($jobs_arr);

        } else {
            http_response_code(404);
            echo json_encode(
                array("code" => 404, "message" => "No Jobs Found.")
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
                !empty($data->company) &&
                !empty($data->title) &&
                !empty($data->date_start) &&
                !empty($data->date_end) &&
                !empty($data->descr) 
            ){
                // set job property values
                $job->company = $data->company;
                $job->title = $data->title;
                $job->date_start = $data->date_start;
                $job->date_end = $data->date_end;
                $job->descr = $data->descr;
        
                if($job->create()) {
                    http_response_code(201);
                        echo json_encode(
                        array("code" => 201, "message" => "New Job Created")
                    );
                } else {
                    http_response_code(503);
                    echo json_encode(
                        array("code" => 503, "message" => "Something Went Wrong. Try Again.")
                    );
                }
            } else {
                http_response_code(400);        
                echo json_encode(array("code" => 400, "message" => "Unable to Create Job. Data is Incomplete."));
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
                $job->id = $data->id;

                if($job->delete()) {
                    http_response_code(200);
                    echo json_encode(
                        array("code" => 200, "message" => "Job Deleted")
                    );
                } else {
                    http_response_code(503);
                    echo json_encode(
                        array("code" => 503, "message" => "Something Went Wrong. Try Again.")
                    );           
                }
            } else {
                http_response_code(400);        
                echo json_encode(array("code" => 400, "message" => "Unable to Delete Job. Data is Incomplete."));
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
                !empty($data->company) &&
                !empty($data->title) &&
                !empty($data->date_start) &&
                !empty($data->date_end) &&
                !empty($data->descr) 
            ){        
                // set job property values
                $job->id = $data->id;
                $job->company = $data->company;
                $job->title = $data->title;
                $job->date_start = $data->date_start;
                $job->date_end = $data->date_end;
                $job->descr = $data->descr;

                if($job->update()) {
                    http_response_code(200);
                    echo json_encode(
                        array("code" => 200, "message" => "Job Updated")
                    );
                } else {
                    http_response_code(503);
                    echo json_encode(
                        array("code" => 503, "message" => "Something Went Wrong. Try Again.")
                    );           
                }
            } else {
                http_response_code(400);        
                echo json_encode(array("code" => 400, "message" => "Unable to Update Job. Data is Incomplete."));
            }
        // No token - No data for you, mkay?
        } else {
            http_response_code(401);        
            echo json_encode(array("code" => 401, "message" => "Unauthorized Request."));
        }

    break;
}
