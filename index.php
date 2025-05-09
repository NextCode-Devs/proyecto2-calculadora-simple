<?php
require_once 'includes/funciones.php';
require_once 'includes/header.php';

$finanzas = new Finanzas();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo'])) {
    $tipo = sanitizar($_POST['tipo']);
    $monto = (float) $_POST['monto'];
    $descripcion = sanitizar($_POST['descripcion']);
    $nota = sanitizar($_POST['nota'] ?? '');
    
    if (in_array($tipo, ['ingreso', 'gasto']) && $monto > 0 && !empty($descripcion)) {
        if ($finanzas->agregarTransaccion($tipo, $monto, $descripcion, $nota)) {
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

$balance = $finanzas->calcularBalance();
$moneda = $finanzas->obtenerConfiguracion('moneda') ?? '$';
$alerta = $finanzas->verificarAlertaGastos();
$transacciones = $finanzas->obtenerTransacciones();
$mensaje = obtenerMensaje();
?>

<div class="container mt-4">
    <!-- Alertas -->
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

    <h1 class="text-center mb-4">Finanzas Personales</h1>

    <!-- Resumen Financiero -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Ingresos</h5>
                    <p class="card-text display-6"><?= $moneda . number_format($balance['ingresos'], 2) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Gastos</h5>
                    <p class="card-text display-6"><?= $moneda . number_format($balance['gastos'], 2) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card <?= $balance['balance'] >= 0 ? 'bg-primary' : 'bg-warning' ?>">
                <div class="card-body">
                    <h5 class="card-title">Balance</h5>
                    <p class="card-text display-6"><?= $moneda . number_format($balance['balance'], 2) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Resumen Mensual</h5>
        </div>
        <div class="card-body">
            <canvas id="graficoFinanzas"></canvas>
        </div>
    </div>

    <!-- Formulario de Transacciones -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Agregar Transacción</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select" required>
                            <option value="ingreso">Ingreso</option>
                            <option value="gasto">Gasto</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Monto (<?= $moneda ?>)</label>
                        <input type="number" step="0.01" min="0.01" name="monto" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Descripción</label>
                        <input type="text" name="descripcion" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nota (opcional)</label>
                        <input type="text" name="nota" class="form-control">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Agregar</button>
            </form>
        </div>
    </div>

    <!-- Historial de Transacciones -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Historial de Transacciones</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Monto</th>
                            <th>Nota</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transacciones as $t): ?>
                        <tr class="<?= $t['tipo'] === 'ingreso' ? 'table-success' : 'table-danger' ?>">
                            <td><?= date('d/m/Y H:i', strtotime($t['fecha'])) ?></td>
                            <td><?= ucfirst($t['tipo']) ?></td>
                            <td><?= htmlspecialchars($t['descripcion']) ?></td>
                            <td><?= $moneda . number_format($t['monto'], 2) ?></td>
                            <td><?= htmlspecialchars($t['nota']) ?></td>
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

    <!-- Calculadora -->
    <div class="card">
        <div class="card-header">
            <h5>Calculadora</h5>
        </div>
        <div class="card-body">
            <div class="calculadora">
                <input type="text" id="pantalla" class="form-control mb-2" disabled>
                <div class="botones">
                    <button type="button" class="btn btn-secondary" value="7">7</button>
                    <button type="button" class="btn btn-secondary" value="8">8</button>
                    <button type="button" class="btn btn-secondary" value="9">9</button>
                    <button type="button" class="btn btn-warning operador" value="/">/</button>
                    
                    <button type="button" class="btn btn-secondary" value="4">4</button>
                    <button type="button" class="btn btn-secondary" value="5">5</button>
                    <button type="button" class="btn btn-secondary" value="6">6</button>
                    <button type="button" class="btn btn-warning operador" value="*">*</button>
                    
                    <button type="button" class="btn btn-secondary" value="1">1</button>
                    <button type="button" class="btn btn-secondary" value="2">2</button>
                    <button type="button" class="btn btn-secondary" value="3">3</button>
                    <button type="button" class="btn btn-warning operador" value="-">-</button>
                    
                    <button type="button" class="btn btn-secondary" value="0">0</button>
                    <button type="button" class="btn btn-secondary" value=".">.</button>
                    <button type="button" class="btn btn-success igual" value="=">=</button>
                    <button type="button" class="btn btn-warning operador" value="+">+</button>
                    
                    <button type="button" class="btn btn-danger limpiar" value="C">C</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/calculadora.js"></script>
<script src="js/main.js"></script>
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

<?php require_once 'includes/footer.php'; ?>