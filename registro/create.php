<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('America/Lima'); 

// get database connection
include_once '../config/database.php';
 
// instantiate product object
include_once '../objects/registro.php';
 
$database = new Database();
$db = $database->getConnection();
 
$registro = new Registro($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// make sure data is not empty
if(
    !empty($data->local_id)
){
 
    // set product property values
    $registro->local_id = $data->local_id;
    $registro->tipo = $data->tipo;
    $registro->fecha_encendido = date("Y-m-d H:i:s", strtotime("+0 day"));  

    // create the product
    if($registro->create()){
 
        http_response_code(201);
        echo json_encode(array("message" => "Product was created."));
    }
 
    else{
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create product."));
    }
}
else{
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create product. Data is incomplete."));
}
?>