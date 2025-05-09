<?php
require_once __DIR__ . '/../config/conexion.php';

class Finanzas {
    private $conn;
    private $usuario_id = null; // Para futura implementación de usuarios

    public function __construct() {
        $this->conn = conectarDB();
    }

    // Operaciones CRUD
    public function agregarTransaccion($tipo, $monto, $descripcion, $nota = '') {
        $stmt = $this->conn->prepare("INSERT INTO transacciones (usuario_id, tipo, monto, descripcion, nota) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isdss", $this->usuario_id, $tipo, $monto, $descripcion, $nota);
        return $stmt->execute();
    }

    public function eliminarTransaccion($id) {
        $stmt = $this->conn->prepare("DELETE FROM transacciones WHERE id = ? AND (usuario_id IS NULL OR usuario_id = ?)");
        $stmt->bind_param("ii", $id, $this->usuario_id);
        return $stmt->execute();
    }

    public function obtenerTransacciones($tipo = null) {
        $sql = "SELECT * FROM transacciones WHERE (usuario_id IS NULL OR usuario_id = ?)";
        if ($tipo) {
            $sql .= " AND tipo = ?";
        }
        $sql .= " ORDER BY fecha DESC";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($tipo) {
            $stmt->bind_param("is", $this->usuario_id, $tipo);
        } else {
            $stmt->bind_param("i", $this->usuario_id);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Cálculos y reportes
    public function calcularBalance() {
        $ingresos = $this->obtenerTransacciones('ingreso');
        $gastos = $this->obtenerTransacciones('gasto');
        
        $total_ingresos = array_reduce($ingresos, fn($c, $i) => $c + $i['monto'], 0);
        $total_gastos = array_reduce($gastos, fn($c, $g) => $c + $g['monto'], 0);
        
        return [
            'ingresos' => $total_ingresos,
            'gastos' => $total_gastos,
            'balance' => $total_ingresos - $total_gastos
        ];
    }

    // Configuración
    public function obtenerConfiguracion($clave) {
        $stmt = $this->conn->prepare("SELECT valor FROM configuraciones WHERE (usuario_id IS NULL OR usuario_id = ?) AND clave = ?");
        $stmt->bind_param("is", $this->usuario_id, $clave);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['valor'] : null;
    }

    public function verificarAlertaGastos() {
        $limite = (float) $this->obtenerConfiguracion('limite_gastos');
        if (!$limite) return null;
        
        $gastos = array_reduce($this->obtenerTransacciones('gasto'), fn($c, $g) => $c + $g['monto'], 0);
        $moneda = $this->obtenerConfiguracion('moneda') ?? '$';
        
        return $gastos > $limite ? 
            "¡ALERTA! Gastos exceden el límite de {$moneda}{$limite}" : 
            null;
    }

    // Para futura implementación de usuarios
    public function setUsuarioId($id) {
        $this->usuario_id = $id;
    }
}

// Helper functions
function mostrarMensaje($tipo, $mensaje) {
    $_SESSION['mensaje'] = [
        'tipo' => $tipo,
        'texto' => $mensaje
    ];
}

function obtenerMensaje() {
    if (empty($_SESSION['mensaje'])) return null;
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
    return $mensaje;
}
?>