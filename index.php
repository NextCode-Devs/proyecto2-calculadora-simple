<?php

session_start();

require_once __DIR__ . '/backend/includes/funciones.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../frontend/html/iniciosesion.html');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$finanzas = new Finanzas($usuario_id);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo'])) {
    $tipo = sanitizar($_POST['tipo']);
    $categoria = isset($_POST['categoria']) ? sanitizar($_POST['categoria']) : '';
    $monto = (float) $_POST['monto'];
    $descripcion = sanitizar($_POST['descripcion']);
    $fecha_programada = !empty($_POST['fecha_programada']) ? $_POST['fecha_programada'] : null;

// Si el tipo es "pago" y no se ingresó fecha, asignar la fecha de hoy
if ($tipo === 'pago' && empty($fecha_programada)) {
    $fecha_programada = date('Y-m-d');
}


    if (in_array($tipo, ['ingreso', 'gasto', 'pago']) && $monto > 0 && !empty($descripcion)) {
        if ($finanzas->agregarTransaccion($tipo, $monto, $descripcion, $categoria, $fecha_programada)) {
            mostrarMensaje('success', 'Transacción agregada correctamente');
        } else {
            mostrarMensaje('danger', 'Error al agregar transacción');
        }
    }
}

// Eliminar transacción
if (isset($_GET['eliminar'])) {
    $id = (int) $_GET['eliminar'];
    if ($finanzas->eliminarTransaccion($id)) {
        mostrarMensaje('success', 'Transacción eliminada correctamente');
    } else {
        mostrarMensaje('danger', 'Error al eliminar transacción');
    }
    header('Location: index.php');
    exit;
}

// Datos financieros
$balance = $finanzas->calcularBalance();
$moneda = $finanzas->obtenerConfiguracion('moneda') ?? '$';
$alerta = $finanzas->verificarAlertaGastos();
$transacciones = $finanzas->obtenerTransacciones();
$mensaje = obtenerMensaje();

// Notificaciones de pagos programados
$notificacionesPagos = [];

foreach ($transacciones as $t) {
    if (!empty($t['fecha_programada']) && $t['fecha_programada'] != '0000-00-00') {
        $fechaPago = new DateTime($t['fecha_programada']);
        $hoy = new DateTime();
        $maniana = (clone $hoy)->modify('+1 day');

        if ($fechaPago->format('Y-m-d') === $hoy->format('Y-m-d')) {
            $notificacionesPagos[] = "Hoy vence el pago de <strong>{$t['categoria']}</strong> por {$moneda}" . number_format($t['monto'], 2);
        }

        if ($fechaPago->format('Y-m-d') === $maniana->format('Y-m-d')) {
            $notificacionesPagos[] = "Mañana vence el pago de <strong>{$t['categoria']}</strong> por {$moneda}" . number_format($t['monto'], 2);
        }

       
    }
}

require_once __DIR__ . '/backend/includes/header.php';
?>

<div class="container mt-4">

    <?php if ($alerta): ?>
    <div class="alert alert-warning alert-dismissible fade show">
        <?= $alerta ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($mensaje): ?>
    <div class="alert alert-<?= $mensaje['tipo'] ?> alert-dismissible fade show">
        <?= $mensaje['texto'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (!empty($notificacionesPagos)): ?>
    <div class="alert alert-info alert-dismissible fade show">
        <ul class="mb-0">
            <?php foreach ($notificacionesPagos as $n): ?>
                <li><?= $n ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

      <!-- Resumen Financiero -->
<div class="row mb-4">
    <!-- Columna de Resumen (Ingresos, Gastos, Balance) -->
    <div class="col-md-5">
        <!-- Ingresos -->
        <div class="card text-white bg-success border-0 shadow-sm mb-3">
            <div class="card-body p-3">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    Ingresos
                    <i class="fas fa-arrow-up fa-sm ms-2 opacity-75"></i>
                </h5>
                <p class="card-text display-6 mb-0 fw-bold"><?= $moneda . number_format($balance['ingresos'], 2) ?></p>
            </div>
        </div>

        <!-- Gastos -->
        <div class="card text-white bg-danger border-0 shadow-sm mb-3">
            <div class="card-body p-3">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    Gastos
                    <i class="fas fa-arrow-down fa-sm ms-2 opacity-75"></i>
                </h5>
                <p class="card-text display-6 mb-0 fw-bold"><?= $moneda . number_format($balance['gastos'], 2) ?></p>
            </div>
        </div>

        <!-- Balance -->
        <div class="card border-0 shadow-sm <?= $balance['balance'] >= 0 ? 'bg-primary text-white' : 'bg-warning text-dark' ?>">
            <div class="card-body p-3">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    Balance
                    <i class="fas fa-balance-scale fa-sm ms-2 opacity-75"></i>
                </h5>
                <p class="card-text display-6 mb-0 fw-bold"><?= $moneda . number_format($balance['balance'], 2) ?></p>
            </div>
        </div>
    </div>

    <!-- Columna para el gráfico -->
    <div class="col-md-7">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header border-0">
                <h5 class="mb-0">Resumen Mensual</h5>
            </div>
            <div class="card-body">
                <canvas id="graficoFinanzas" height="160"></canvas>
            </div>
        </div>
    </div>
</div>
    <!-- Formulario de Transacciones -->
 <div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">AGREGAR TRANSACCIÓN</h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select" required>
                        <option value="ingreso">Ingreso</option>
                        <option value="gasto">Gasto</option>
                        <option value="pago">Programar pagos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Categoría</label>
                    <select name="categoria" class="form-select" required>
                        <option value="sueldo">Sueldo</option>
                        <option value="compras">Compras</option>
                        <option value="transporte">Transporte</option>
                        <option value="estudios">Estudios</option>
                        <option value="entretenimiento">Entretenimiento</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Monto (<?= $moneda ?>)</label>
                    <input type="number" step="0.01" min="0.01" name="monto" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Descripción</label>
                    <input type="text" name="descripcion" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Programada</label>
                    <input type="date" name="fecha_programada" class="form-control">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary w-100">Agregar</button>
                </div>
            </div>
        </form>
    </div>
</div>

    <!-- Historial de Transacciones -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>HISTORIAL DE TRANSACCIONES</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Categoria</th>
                            <th>Descripción</th>
                            <th>Monto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                          <tbody>
                        <?php foreach ($transacciones as $t): ?>
                        <tr class="<?php
                            if ($t['tipo'] === 'ingreso') echo 'table-success';
                            elseif ($t['tipo'] === 'gasto') echo 'table-danger';
                            elseif ($t['tipo'] === 'pago') echo 'table-warning';
                        ?>">
                            <td><?= !empty($t['fecha_programada']) && $t['fecha_programada'] != '0000-00-00' ? $t['fecha_programada'] : $t['fecha'] ?></td>
                            <td><?= $t['tipo'] === 'pago' ? 'Pago Programado' : ucfirst($t['tipo']) ?></td>
                            <td><?= ucfirst($t['categoria']) ?></td>
                            <td><?= htmlspecialchars($t['descripcion']) ?></td>                          
                            <td><?= $moneda . number_format($t['monto'], 2) ?></td>
                            <td>
                                <a href="?eliminar=<?= $t['id'] ?>" class="btn btn-sm btn-danger" 
                                onclick="return confirm('¿Eliminar esta transacción?')">Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>

                </table>
            </div>
        </div>
    </div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="frontend/js/calculadora.js"></script>
<script src="frontend/js/main.js"></script>
<script>
    // Gráfico de finanzas
    const ctx = document.getElementById('graficoFinanzas').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Ingresos', 'Gastos'],
            datasets: [{
                label: 'Resumen Financiero',
                data: [<?= $balance['ingresos'] ?>, <?= $balance['gastos'] ?>],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(220, 53, 69, 0.7)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>



<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet" />

<!-- FullCalendar JS (esto es lo que te falta) -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet" />

<!-- Calendario de Pagos -->
<div class="card mb-4">
    <div class="card-header">
        <h5>Calendario de Pagos Programados</h5>
    </div>
    <div class="card-body">
        <div id="calendarioPagos" style="min-height: 500px;"></div>
    </div>
</div>
<!-- Tu script donde usas FullCalendar -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendarioPagos');
    if (!calendarEl) return;

    var eventos = <?= json_encode(array_map(function($t) use ($moneda) {
        // Determinar la fecha correcta a mostrar
        $fechaMostrar = (!empty($t['fecha_programada']) && $t['fecha_programada'] != '0000-00-00') 
            ? $t['fecha_programada'] 
            : $t['fecha'];
        
        // Determinar el color según el tipo
        $color = (!empty($t['fecha_programada']) && $t['fecha_programada'] != '0000-00-00') 
            ? '#ffc107' // Amarillo para programados
            : ($t['tipo'] === 'gasto' ? '#dc3545' : '#28a745');
        
        return [
            'title' => $t['categoria'] . ': ' . $moneda . number_format($t['monto'], 2),
            'start' => $fechaMostrar,
            'color' => $color,
            'extendedProps' => [
                'descripcion' => $t['descripcion'],
                'tipo' => $t['tipo'],
                'programado' => (!empty($t['fecha_programada']) && $t['fecha_programada'] != '0000-00-00'),
                'fechaProgramada' => $t['fecha_programada']
            ]
        ];
    }, $transacciones), JSON_UNESCAPED_UNICODE) ?>;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        events: eventos,
        eventClick: function(info) {
            var detalles = 'Detalles de la transacción:\n\n' +
                          'Tipo: ' + (info.event.extendedProps.programado ? 'Pago Programado' : info.event.extendedProps.tipo) + '\n' +
                          'Monto: ' + info.event.title.split(':')[1] + '\n' +
                          'Descripción: ' + info.event.extendedProps.descripcion + '\n' +
                          'Fecha: ' + info.event.start.toLocaleDateString();
            
            if (info.event.extendedProps.programado) {
                var hoy = new Date().toISOString().split('T')[0];
                var fechaEvento = info.event.start.toISOString().split('T')[0];
                detalles += '\n\nEstado: ' + (fechaEvento < hoy ? 'Vencido' : 'Pendiente');
            }
            
            alert(detalles);
        }
    });

    calendar.render();
});
</script>




<?php require_once 'backend/includes/footer.php'; ?>
