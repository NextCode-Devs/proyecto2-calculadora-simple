<?php
require_once __DIR__ . '/../config/conexion.php';

class Finanzas {
    private $conn;
    private $usuario_id;

    public function __construct($usuario_id) {
        $this->conn = conectarDB();
        $this->usuario_id = $usuario_id;
    }

    // Aquí van tus otros métodos (agregarTransaccion, obtenerTransacciones, etc.)



    // Operaciones CRUD
public function agregarTransaccion($tipo, $monto, $descripcion, $categoria, $fecha_programada = null) {
    $query = "INSERT INTO transacciones (tipo, monto, descripcion, usuario_id, categoria, fecha_programada) 
              VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $this->conn->prepare($query);
    
    if (!$stmt) {
        die("Error en prepare: " . $this->conn->error);
    }

    $stmt->bind_param("sdsiss", $tipo, $monto, $descripcion, $this->usuario_id, $categoria, $fecha_programada);

    return $stmt->execute(); 
}




    public function eliminarTransaccion($id) {
        $stmt = $this->conn->prepare("DELETE FROM transacciones WHERE id = ? AND (usuario_id IS NULL OR usuario_id = ?)");
        $stmt->bind_param("ii", $id, $this->usuario_id);
        return $stmt->execute();
    }

    public function obtenerTransacciones($tipo = null) {
    if ($tipo) {
        $sql = "SELECT * FROM transacciones WHERE usuario_id = ? AND tipo = ? ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $this->usuario_id, $tipo);
    } else {
        $sql = "SELECT * FROM transacciones WHERE usuario_id = ? ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($sql);
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
    $mensajes = [];

    // Alerta 1: Gastaste más que el mes pasado
    $sql = "
    SELECT gasto_mes_actual, gasto_mes_anterior, diferencia, porcentaje_cambio
    FROM vista_comparacion_mensual
    WHERE usuario_id = ?
    ORDER BY anio_actual DESC, mes_actual DESC
    LIMIT 1
";


    $stmt = $this->conn->prepare($sql);
    if (!$stmt) {
        die("Error en prepare: " . $this->conn->error);
    }

    $stmt->bind_param("i", $this->usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();

    if ($fila) {
        if ($fila['gasto_mes_anterior'] !== null && $fila['diferencia'] > 0) {
            $mensajes[] = " Este mes has gastado más que el anterior ({$fila['porcentaje_cambio']}% más). ¡Cuidado con tus finanzas!";
        } elseif ($fila['gasto_mes_anterior'] === null) {
            $mensajes[] = " Aún no hay datos del mes anterior para comparar tus gastos.";
        }
    }

    // Alerta 2: Superas límite de gastos configurado
    $limite = (float) $this->obtenerConfiguracion('limite_gastos');
    if ($limite) {
        $gastos = array_reduce($this->obtenerTransacciones('gasto'), fn($c, $g) => $c + $g['monto'], 0);
        $moneda = $this->obtenerConfiguracion('moneda') ?? '$';
        if ($gastos > $limite) {
            $mensajes[] = " Has excedido tu límite mensual de gastos de {$moneda}{$limite}.";
        }
    }

    return $mensajes ? implode(" ", $mensajes) : null;
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