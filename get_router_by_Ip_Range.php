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
    $decodedToken = JWT::decode($data->jwt, $key, array('HS256'));
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
if(empty($data->client_ip_startswith)){
    http_response_code(503);
    echo json_encode(array("message" => "Starting Ip is required."));
    return;
}


$router=new Router($db);
//$router->client_ip_address = $data->client_ip_startswith;
$routers=$router->getRouterByIpRange($data->client_ip_startswith);
if($routers)){
  $data=array();
  foreach($routers as $rout){
      $data[]=array(
                  'sap_id'=>$rout->sap_id,
                  'internet_host_name'=>$rout->internet_host_name,
                  'client_ip_address'=>$rout->client_ip_address,
                  'mac_address'=>$rout->mac_address
              );
  }
  http_response_code(200);
  echo json_encode(array('data'=>$data));
  return;
}
