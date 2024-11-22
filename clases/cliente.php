<?php
require_once("conexion.php");
class Cliente extends conexion {
    protected $strTelefono;
    protected $strNombre;

    public function __construct($telefono = "", $nombre = "") {
        parent::__construct(); 
        $this->strTelefono = $telefono;
        $this->strNombre = $nombre;
    }

    public function guardarCliente() {
        try {
            $sql = "INSERT INTO cliente (nombre, telefono) VALUES (:nombre, :telefono)";
            $stmt = $this->conect()->prepare($sql);
            $stmt->bindParam(':nombre', $this->strNombre);
            $stmt->bindParam(':telefono', $this->strTelefono);
            $stmt->execute();
            return $this->conect()->lastInsertId();
        } catch (PDOException $e) {
            echo "Error al guardar cliente: " . $e->getMessage();
            return false;
        }
    }

    public function obtenerClientes() {
        try {
            $sql = "SELECT * FROM cliente";
            $stmt = $this->conect()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener cliente: " . $e->getMessage();
            return [];
        }
    }
}



