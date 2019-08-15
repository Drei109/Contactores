<?php
class Registro{
 
    // database connection and table name
    private $conn;
    private $table_name = "registro";
 
    // object properties
    public $registro_id;
    public $local_id;
    public $tipo;
    public $fecha_encendido;
    public $fecha_modificacion;
    public $fecha_apagado;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function read(){

        if(isset($this->local_id) && !isset($this->fecha_encendido) && !isset($this->fecha_apagado)){
            $query = "SELECT
                    r.registro_id, r.local_id, r.tipo, r.fecha_encendido, r.fecha_apagado
                FROM
                    " . $this->table_name . " r 
                WHERE
                    r.local_id = :local_id
                ORDER BY
                    r.fecha_encendido ASC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":local_id", $this->local_id);

        }elseif(isset($this->local_id) && isset($this->fecha_encendido) && isset($this->fecha_apagado)){
            $query = "SELECT
                    r.registro_id, r.local_id, r.tipo, r.fecha_encendido, r.fecha_apagado
                FROM
                    " . $this->table_name . " r 
                WHERE
                    r.local_id = :local_id AND
                    fecha_encendido BETWEEN :fecha_encendido AND :fecha_apagado
                ORDER BY
                    r.local_id DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":local_id", $this->local_id);
            $stmt->bindParam(":fecha_encendido", $this->fecha_encendido);
            $stmt->bindParam(":fecha_apagado", $this->fecha_apagado);

        } elseif(!isset($this->local_id) && isset($this->fecha_encendido) && isset($this->fecha_apagado)){
            $query = "SELECT
                    r.registro_id, r.local_id, r.tipo, r.fecha_encendido, r.fecha_apagado
                FROM
                    " . $this->table_name . " r 
                WHERE
                    fecha_encendido BETWEEN :fecha_encendido AND :fecha_apagado
                ORDER BY
                    r.fecha_encendido ASC";
        
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":fecha_encendido", $this->fecha_encendido);
            $stmt->bindParam(":fecha_apagado", $this->fecha_apagado);

        } else{
            // select all query
            $query = "SELECT
            r.registro_id, r.local_id, r.tipo, r.fecha_encendido, r.fecha_apagado
            FROM
            " . $this->table_name . " r 
            ORDER BY
            r.local_id DESC";

            $stmt = $this->conn->prepare($query);
        }
     
        // execute query
        $stmt->execute();
     
        return $stmt;
    }

    function create(){

        $pre_query= "SELECT * FROM " . $this->table_name . " WHERE
                        local_id=:local_id AND DATE(fecha_encendido) = DATE(:fecha_encendido)";
        
        $pre_stmt = $this->conn->prepare($pre_query);

        $pre_stmt->bindParam(":local_id", $this->local_id);
        $pre_stmt->bindParam(":fecha_encendido", $this->fecha_encendido);
        $pre_stmt->execute();

        if($pre_stmt->rowCount() > 0){
            $query = "UPDATE " . $this->table_name . " 
                            SET
                    tipo=:tipo
                WHERE
                    local_id =:local_id AND
                    DATE(fecha_encendido) = DATE(:fecha_encendido)
                    ";
     

            // DATE(fecha_encendido) = DATE_ADD(DATE(NOW()),INTERVAL 1 DAY)

            // prepare query statement
            $stmt = $this->conn->prepare($query);
            
            // bind new values
            $stmt->bindParam(":local_id", $this->local_id);
            $stmt->bindParam(":tipo", $this->tipo);
            $stmt->bindParam(":fecha_encendido", $this->fecha_encendido);
        
            // execute the query
            if($stmt->execute()){
                return true;
            }else{
                return false;   
            }
        } else{
            // query to insert record
            $query = "INSERT INTO " . $this->table_name . "
            SET
                local_id=:local_id, 
                tipo=:tipo, 
                fecha_encendido=:fecha_encendido";

            // prepare query
            $stmt = $this->conn->prepare($query);
            
            // bind values
            $stmt->bindParam(":local_id", $this->local_id);
            $stmt->bindParam(":tipo", $this->tipo);
            $stmt->bindParam(":fecha_encendido", $this->fecha_encendido);

            // execute query
            if($stmt->execute()){
                return true;
            }else{
                return false;   
            }
        }
              
    }

    function update(){
 
        // update query
        $query = "UPDATE
                    " . $this->table_name . " 
                SET
                    tipo=:tipo, 
                    fecha_apagado=:fecha_apagado
                WHERE
                    local_id =:local_id AND
                    DATE(fecha_encendido) = DATE(:fecha_apagado)
                    ";
     

        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
         
        // bind new values
        $stmt->bindParam(":local_id", $this->local_id);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":fecha_apagado", $this->fecha_apagado);
     
        // execute the query
        if($stmt->execute()){
            return true;
        }
     
        return false;
    }
}
