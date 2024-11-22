<?php
class conexion{
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $db = "db_parqueadero";

    protected $conect;

    public function __construct(){
        try{
            $connetctionSrting = "mysql:host=".$this ->host.";dbname=".$this->db.";charset=utf8";
            $this ->conect = new PDO($connetctionSrting,$this->user,$this->password);
            $this ->conect->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            
            $this ->conect = "error de conexion";
            echo "error". $e->getMessage();
        }

    }
    
    public function conect(){
        return $this->conect;
        
    }
}




?>