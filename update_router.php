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

if(empty($data->jwt)){
    http_response_code(505);
    echo json_encode(array("message" => "Access token is required."));
    return;
}
try {
    $decodedToken = JWT::decode($jwt, $key, array('HS256'));
    if(empty($decodedToken)){
      http_response_code(506);
      echo json_encode(array("message" => "Invalid access token."));
      return ;
    }
}catch (Exception $e){
    http_response_code(506);
    echo json_encode(array(
        "message" => "Access denied .",
        "error" => $e->getMessage()
    ));
    return;
}
if(isset($data->sap_id)){
    http_response_code(501);
    echo json_encode(array("message" => "Sap Id is required."));
    return;
}
if(isset($data->internet_host_name)){
    http_response_code(502);
    echo json_encode(array("message" => "Host name is required."));
    return;
}
if(empty($data->client_ip_address)){
    http_response_code(503);
    echo json_encode(array("message" => "Ip address is required."));
    return;
}
if(isset($data->mac_address)){
    http_response_code(504);
    echo json_encode(array("message" => "Mac address is required."));
    return;
}


$router=new Router($db);
if(isset($data->mac_address)){
    $router->sap_id = $data->sap_id;
}
if(isset($data->mac_address)){
    $router->internet_host_name = $data->internet_host_name;
}

$router->client_ip_address = $data->client_ip_address;
if(isset($data->mac_address)){
    $router->mac_address = $data->mac_address;
}
if(!$router->ipExists()){
  http_response_code(507);
  echo json_encode(array("message" => "No matched record found for given api.."));
  return;
}

$routerIsUpdated=$router->update();

if($routerIsUpdated){
  http_response_code(200);
  echo json_encode(array( "code"=>200,
                          "status"=>"success",
                          "message"=>"Router added successfully.",
                          "data"=>array(
                                 'sapId'=>$router->sap_id,
                                 'internet_host_name'=>$router->internet_host_name,
                                 'client_ip_address'=>$router->client_ip_address,
                                 'mac_address'=>$router->mac_address
                           )
                        ));
  return;
}
