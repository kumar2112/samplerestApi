<?php
header("Access-Control-Allow-Origin: http://localhost/rest-api/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config/database.php';
include_once 'config/core.php';
include_once 'objects/router.php';

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
// get database connection
$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

$router=new Router($db);
$router->sap_id = $data->sap_id;
$router->internet_host_name = $data->internet_host_name;
$router->client_ip_address = $data->client_ip_address;
$router->mac_address = $data->mac_address;


 if(!empty($router->sap_id) && !empty($router->internet_host_name) && !empty($router->client_ip_address) && !empty($router->mac_address)){
      if($router->ipExists()){
        http_response_code(400);

        // display message: unable to create user
        echo json_encode(array("message" => "Duplicate Ip Address."));
        return;
      }
    $jwt=isset($data->jwt) ? $data->jwt : "";
    if($jwt){

      // if decode succeed, show user details
      try {
         $decoded = JWT::decode($jwt, $key, array('HS256'));
         if(!$decoded){
           http_response_code(401);

           // show error message
           echo json_encode(array("Invalid token"));
           return ;
         }
         // echo "<pre>"
         // print_r($decoded);die();
        //set response code
        $router->create();
        http_response_code(200);
        //
        // display message: user was created
        echo json_encode(array("message" => "Router added successfully."));
      }catch (Exception $e){

          // set response code
          http_response_code(401);

          // show error message
          echo json_encode(array(
              "message" => "Access denied .",
              "error" => $e->getMessage()
          ));
     }
  }else{
    http_response_code(400);
    // display message: unable to create user
    echo json_encode(array("message" => "invalid token."));
  }
}else{
    // set response code
    http_response_code(400);
    // display message: unable to create user
    echo json_encode(array("message" => "please enter valid parameters."));
}
