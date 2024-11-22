<?php
require_once("conexion.php");

class Puesto {
    private $pdo;

    public function __construct() {
        $conexion = new conexion();
        $this->pdo = $conexion->conect();
    }

    
    public function inicializarPuestos($totalPuestos = 40) {
        try {
            $sql = "INSERT INTO puestos (id_puesto, estado)
                    SELECT x, 'libre'
                    FROM (SELECT 1 + (ROW_NUMBER() OVER()) AS x FROM (SELECT 1 UNION SELECT 2) t1) t2
                    WHERE x <= :totalPuestos
                    ON DUPLICATE KEY UPDATE estado = 'libre'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':totalPuestos', $totalPuestos, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error al inicializar los puestos: " . $e->getMessage();
        }
    }

    
    public function asignarPuesto($clienteId, $horaEntrada) {
        try {
            
            $sqlCheckCliente = "SELECT id_cliente FROM cliente WHERE id_cliente = :clienteId";
            $stmtCheck = $this->pdo->prepare($sqlCheckCliente);
            $stmtCheck->bindParam(':clienteId', $clienteId);
            $stmtCheck->execute();

            if ($stmtCheck->rowCount() == 0) {
                echo "El cliente con ID $clienteId no existe.";
                return null;
            }

        
            $sql = "SELECT id_puesto FROM puestos WHERE estado = 'libre' LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $puesto = $stmt->fetch();

            if ($puesto) {
                $numeroPuesto = $puesto['id_puesto'];

            
                $sqlUpdate = "UPDATE puestos SET estado = 'ocupado', cliente_id = :clienteId, hora_entrada = :horaEntrada WHERE id_puesto = :numeroPuesto";
                $stmtUpdate = $this->pdo->prepare($sqlUpdate);
                $stmtUpdate->bindParam(':clienteId', $clienteId);
                $stmtUpdate->bindParam(':horaEntrada', $horaEntrada);
                $stmtUpdate->bindParam(':numeroPuesto', $numeroPuesto);
                $stmtUpdate->execute();

                return $numeroPuesto;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            echo "Error al asignar puesto: " . $e->getMessage();
        }
    }

    
    public function liberarPuesto($numeroPuesto) {
        try {
            
            $sql = "UPDATE puestos SET estado = 'libre', cliente_id = NULL, hora_entrada = NULL WHERE id_puesto = :numeroPuesto";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':numeroPuesto', $numeroPuesto);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error al liberar puesto: " . $e->getMessage();
        }
    }

    public function obtenerPuestosLibres() {
        try {
            
            $sql = "SELECT * FROM puestos WHERE estado = 'libre'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener puestos libres: " . $e->getMessage();
        }
    }

    public function obtenerPuestosOcupados() {
        try {

            $sql = "SELECT p.id_puesto, p.estado, c.nombre AS nombre
FROM puestos p
LEFT JOIN cliente c ON p.cliente_id = c.id_cliente
WHERE p.estado = 'Ocupado';";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener puestos libres: " . $e->getMessage();
        }
    }

    public function informacion($clienteId) {

        $sql = "SELECT p.id_puesto, p.estado, c.nombre AS cliente_nombre, v.placa AS vehiculo_placa 
        FROM puestos p
        LEFT JOIN vehiculos v ON p.vehiculo_id = v.id_vehiculo
        LEFT JOIN clientes c ON v.cliente_id = c.id_cliente";

    }


        public function calcularCostoEstacionamiento($placa_salida) {
            $query = "SELECT hora_entrada FROM puestos WHERE id_puesto = :placa AND estado = 'ocupado'";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':placa', $placa_salida);
            $stmt->execute();
        
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($resultado) {
                $hora_entrada = new DateTime($resultado['hora_entrada']);
                $hora_salida = new DateTime(); // Hora actual como hora de salida
        
                $intervalo = $hora_entrada->diff($hora_salida);
                $horas = $intervalo->h;
                $minutos = $intervalo->i;
                $horas_totales = $horas + ($minutos / 60);
                $costo = ceil($horas_totales) * 2; // Costo por hora es $2
        
                $hora_salida_formateada = $hora_salida->format('Y-m-d H:i:s');
                $updateQuery = "UPDATE puestos SET estado = 'libre', hora_entrada = NULL, cliente_id = NULL, hora_salida = :hora_salida WHERE id_puesto = :placa";
                $updateStmt = $this->pdo->prepare($updateQuery);
                $updateStmt->bindParam(':hora_salida', $hora_salida_formateada);
                $updateStmt->bindParam(':placa', $placa_salida);
                $updateStmt->execute();
        
                return $costo;
            }
        
            return false;
        }
        
        }
    


?>
