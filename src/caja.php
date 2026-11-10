<?php
declare(strict_types=1);
require_once 'conexion.php';

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mesa_id'])) {
    $mesaId = (int) $_POST['mesa_id'];

    if ($mesaId > 0) {
        $conn->begin_transaction();
        try {
            $stmtPedidos = $conn->prepare(
                "UPDATE pedidos SET estado = 'Pagado' WHERE mesa_id = ? AND estado <> 'Pagado'"
            );
            if ($stmtPedidos === false) {
                throw new RuntimeException('Error preparando cierre de pedidos.');
            }
            $stmtPedidos->bind_param('i', $mesaId);
            $stmtPedidos->execute();
            $stmtPedidos->close();

            $stmtMesa = $conn->prepare("UPDATE mesas SET estado = 'Disponible' WHERE id = ?");
            if ($stmtMesa === false) {
                throw new RuntimeException('Error preparando liberación de mesa.');
            }
            $stmtMesa->bind_param('i', $mesaId);
            $stmtMesa->execute();
            $stmtMesa->close();

            $conn->commit();
            header('Location: caja.php?cobrado=' . $mesaId);
            exit;
        } catch (Throwable $e) {
            $conn->rollback();
            $error = 'No se pudo completar el cobro de la mesa. Intente nuevamente.';
        }
    }
}

if (isset($_GET['cobrado'])) {
    $mesaCobrada = (int) $_GET['cobrado'];
    if ($mesaCobrada > 0) {
        $mensaje = "Cobro registrado. La Mesa #{$mesaCobrada} quedó disponible.";
    }
}

$cuentasAbiertas = [];
$sql = "SELECT m.id AS mesa_id,
               m.estado AS estado_mesa,
               COUNT(DISTINCT p.id) AS total_pedidos,
               COALESCE(SUM(dp.cantidad * dp.precio_unitario), 0) AS total_consumo
        FROM mesas m
        INNER JOIN pedidos p ON m.id = p.mesa_id AND p.estado <> 'Pagado'
        LEFT JOIN detalle_pedido dp ON p.id = dp.pedido_id
        GROUP BY m.id, m.estado
        ORDER BY m.id ASC";

$resultado = $conn->query($sql);
if ($resultado instanceof mysqli_result) {
    while ($fila = $resultado->fetch_assoc()) {
        $cuentasAbiertas[] = $fila;
    }
    $resultado->free();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Caja - Facturación y Mesas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="top-bar">
        <div class="marca">
            <span class="marca-icono" aria-hidden="true">💵</span>
            <h1>Panel de Caja</h1>
        </div>
        <nav class="nav-links">
            <a href="cocina.php">Cocina</a>
            <a href="mesero.php">Mesero</a>
            <a href="caja.php" class="activo">Caja</a>
        </nav>
    </header>

    <main class="contenedor-panel">
        <div class="barra-estado-panel">
            <span>Facturación presencial por mesa</span>
            <span class="contador-tickets"><?php echo count($cuentasAbiertas); ?> mesas por cobrar</span>
        </div>

        <?php if ($mensaje !== ''): ?>
            <div class="alerta alerta-ok"><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="alerta alerta-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (count($cuentasAbiertas) === 0): ?>
            <p class="estado-vacio">No hay mesas con cuentas pendientes por cobrar.</p>
        <?php else: ?>
            <section class="grid-tickets">
                <?php foreach ($cuentasAbiertas as $cuenta): ?>
                    <article class="ticket-card ticket-caja">
                        <div class="ticket-header">
                            <h2>Mesa #<?php echo (int) $cuenta['mesa_id']; ?></h2>
                            <span class="badge badge-ocupada">Por Cobrar</span>
                        </div>
                        <p class="ticket-meta">Pedidos acumulados: <?php echo (int) $cuenta['total_pedidos']; ?></p>
                        <p class="ticket-total">
                            Total: $<?php echo number_format((float) $cuenta['total_consumo'], 0, ',', '.'); ?>
                        </p>
                        <form method="POST" action="caja.php" onsubmit="return confirm('¿Confirmar cobro y liberar la Mesa #<?php echo (int) $cuenta['mesa_id']; ?>?');">
                            <input type="hidden" name="mesa_id" value="<?php echo (int) $cuenta['mesa_id']; ?>">
                            <button type="submit" class="btn-accion btn-cobrar">Cobrar y Liberar Mesa</button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>