<?php
require_once("conexion.php");

class Vehiculo extends conexion {
    protected $strPlaca;
    protected $strMarca;
    protected $strColor;
    protected $intClienteId;

    public function __construct($clienteId = null, $placa = "", $marca = "", $color = "") {
        parent::__construct();
        $this->intClienteId = $clienteId;
        $this->strPlaca = $placa;
        $this->strMarca = $marca;
        $this->strColor = $color;
    }

    public function guardarVehiculo() {
        try {
            $sql = "INSERT INTO vehiculo(cliente, placa, marca, color) VALUES (:clienteId, :placa, :marca, :color)";
            $stmt = $this->conect()->prepare($sql);
            $stmt->bindParam(':clienteId', $this->intClienteId);
            $stmt->bindParam(':placa', $this->strPlaca);
            $stmt->bindParam(':marca', $this->strMarca);
            $stmt->bindParam(':color', $this->strColor);
            $stmt->execute();
            return $this->conect()->lastInsertId();
        } catch (PDOException $e) {
            echo "Error al guardar vehículo: " . $e->getMessage();
            return false;
        }
    }

  
    public function obtenerVehiculos() {
        try {
            $sql = "SELECT * FROM vehiculos";
            $stmt = $this->conect()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener vehículos: " . $e->getMessage();
            return [];
        }
    }

    
    public function obtenerVehiculosPorCliente($clienteId) {
        try {
            $sql = "SELECT * FROM vehiculos WHERE cliente = :clienteId";
            $stmt = $this->conect()->prepare($sql);
            $stmt->bindParam(':clienteId', $clienteId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener vehículos del cliente: " . $e->getMessage();
            return [];
        }
    }
}
