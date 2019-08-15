<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// get database connection
include_once '../config/database.php';
// instantiate product object
include_once '../objects/registro.php';
 
$database = new Database();
$db = $database->getConnection();
 
$registro = new Registro($db);

$registro->local_id = isset($_GET['local_id']) ? $_GET['local_id'] : NULL;
$registro->fecha_encendido = isset($_GET['fecha_encendido']) ? $_GET['fecha_encendido'] : NULL;
$registro->fecha_apagado = isset($_GET['fecha_apagado']) ? $_GET['fecha_apagado'] : NULL;

// query registros
$stmt = $registro->read();
$num = $stmt->rowCount();
 
if($num>0){
 
    // products array
    $registro_arr=array();
    //$registro_arr["registros"]=array();
 
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $registro_item=array(
            "registro_id" => $registro_id,
            "local_id" => $local_id,
            "tipo" => $tipo,
            "fecha_encendido" => $fecha_encendido,
            "fecha_apagado" => $fecha_apagado
        );
 
        array_push($registro_arr, $registro_item);
    }
 
    // set response code - 200 OK
    http_response_code(200);
 
    // show products data in json format
    echo json_encode($registro_arr);
}
else{
 
    // set response code - 404 Not found
    http_response_code(404);
 
    // tell the user no products found
    echo json_encode(
        array("message" => "No products found.")
    );
}
 