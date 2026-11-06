<?php
declare(strict_types=1);
require_once 'conexion.php';

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pedido_id'])) {
    $pedidoId = (int) $_POST['pedido_id'];

    if ($pedidoId > 0) {
        $stmt = $conn->prepare("UPDATE pedidos SET estado = 'Entregado' WHERE id = ? AND estado = 'Listo'");
        if ($stmt !== false) {
            $stmt->bind_param('i', $pedidoId);
            if ($stmt->execute()) {
                $stmt->close();
                header('Location: mesero.php?entregado=1');
                exit;
            }
            $error = 'No se pudo actualizar el estado del pedido.';
            $stmt->close();
        } else {
            $error = 'Error al preparar la consulta de actualización.';
        }
    }
}

if (isset($_GET['entregado']) && $_GET['entregado'] === '1') {
    $mensaje = 'Pedido marcado como entregado correctamente.';
}

$pedidosListos = [];
$sql = "SELECT p.id, p.mesa_id, p.estado, p.fecha_creacion,
               GROUP_CONCAT(CONCAT(dp.cantidad, 'x ', pr.nombre) SEPARATOR ', ') AS items
        FROM pedidos p
        LEFT JOIN detalle_pedido dp ON p.id = dp.pedido_id
        LEFT JOIN productos pr ON dp.producto_id = pr.id
        WHERE p.estado = 'Listo'
        GROUP BY p.id, p.mesa_id, p.estado, p.fecha_creacion
        ORDER BY p.fecha_creacion ASC";

$resultado = $conn->query($sql);
if ($resultado instanceof mysqli_result) {
    while ($fila = $resultado->fetch_assoc()) {
        $pedidosListos[] = $fila;
    }
    $resultado->free();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="10">
    <title>Panel de Mesero - Pedidos Listos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="top-bar">
        <div class="marca">
            <span class="marca-icono" aria-hidden="true">🛎️</span>
            <h1>Panel de Mesero</h1>
        </div>
        <nav class="nav-links">
            <a href="cocina.php">Cocina</a>
            <a href="mesero.php" class="activo">Mesero</a>
            <a href="caja.php">Caja</a>
        </nav>
    </header>

    <main class="contenedor-panel">
        <div class="barra-estado-panel">
            <span class="indicador-vivo">● Actualización automática cada 10s</span>
            <span class="contador-tickets"><?php echo count($pedidosListos); ?> pedidos listos</span>
        </div>

        <?php if ($mensaje !== ''): ?>
            <div class="alerta alerta-ok"><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="alerta alerta-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (count($pedidosListos) === 0): ?>
            <p class="estado-vacio">No hay pedidos listos en cocina en este momento.</p>
        <?php else: ?>
            <section class="grid-tickets">
                <?php foreach ($pedidosListos as $pedido): ?>
                    <article class="ticket-card ticket-listo">
                        <div class="ticket-header">
                            <h2>Mesa #<?php echo (int) $pedido['mesa_id']; ?></h2>
                            <span class="badge badge-listo">Listo en Cocina</span>
                        </div>
                        <p class="ticket-meta">Pedido #<?php echo (int) $pedido['id']; ?> · <?php echo htmlspecialchars((string) $pedido['fecha_creacion'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="ticket-items">
                            <?php echo htmlspecialchars((string) ($pedido['items'] ?? 'Sin ítems registrados'), ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        <form method="POST" action="mesero.php">
                            <input type="hidden" name="pedido_id" value="<?php echo (int) $pedido['id']; ?>">
                            <button type="submit" class="btn-accion btn-entregar">Marcar como Entregado</button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>