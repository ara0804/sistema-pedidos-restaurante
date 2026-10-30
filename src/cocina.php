<?php
declare(strict_types=1);
require_once 'conexion.php';

$comandas = [];
$sql = "SELECT p.id, p.mesa_id, p.estado, p.fecha_creacion,
               GROUP_CONCAT(CONCAT(dp.cantidad, 'x ', pr.nombre) SEPARATOR ', ') AS items
        FROM pedidos p
        LEFT JOIN detalle_pedido dp ON p.id = dp.pedido_id
        LEFT JOIN productos pr ON dp.producto_id = pr.id
        WHERE p.estado IN ('Recibido', 'En preparación')
        GROUP BY p.id, p.mesa_id, p.estado, p.fecha_creacion
        ORDER BY p.fecha_creacion ASC";

$resultado = $conn->query($sql);
if ($resultado instanceof mysqli_result) {
    while ($fila = $resultado->fetch_assoc()) {
        $comandas[] = $fila;
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
    <title>Panel de Cocina - Comandas en Tiempo Real</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="top-bar">
        <div class="marca">
            <span class="marca-icono" aria-hidden="true">🔥</span>
            <h1>Panel de Cocina</h1>
        </div>
        <nav class="nav-links">
            <a href="cocina.php" class="activo">Cocina</a>
            <a href="mesero.php">Mesero</a>
            <a href="caja.php">Caja</a>
        </nav>
    </header>

    <main class="contenedor-panel">
        <div class="barra-estado-panel">
            <span class="indicador-vivo">● Actualización automática cada 10s</span>
            <span class="contador-tickets"><?php echo count($comandas); ?> comandas activas</span>
        </div>

        <?php if (count($comandas) === 0): ?>
            <p class="estado-vacio">No hay comandas pendientes en cocina.</p>
        <?php else: ?>
            <section class="grid-tickets">
                <?php foreach ($comandas as $comanda): ?>
                    <?php
                    $esRecibido = ($comanda['estado'] === 'Recibido');
                    $claseCard = $esRecibido ? 'ticket-recibido' : 'ticket-preparacion';
                    $claseBadge = $esRecibido ? 'badge-recibido' : 'badge-preparacion';
                    $siguienteEstado = $esRecibido ? 'En preparación' : 'Listo';
                    $textoBoton = $esRecibido ? 'Empezar a Preparar' : 'Marcar como Listo';
                    $claseBoton = $esRecibido ? 'btn-preparar' : 'btn-marcar-listo';
                    ?>
                    <article class="ticket-card <?php echo $claseCard; ?>">
                        <div class="ticket-header">
                            <h2>Mesa #<?php echo (int) $comanda['mesa_id']; ?></h2>
                            <span class="badge <?php echo $claseBadge; ?>">
                                <?php echo htmlspecialchars((string) $comanda['estado'], ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </div>
                        <p class="ticket-meta">
                            Pedido #<?php echo (int) $comanda['id']; ?> · <?php echo htmlspecialchars((string) $comanda['fecha_creacion'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        <p class="ticket-items">
                            <?php echo htmlspecialchars((string) ($comanda['items'] ?? 'Sin ítems'), ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        <form method="POST" action="actualizar_estado.php">
                            <input type="hidden" name="pedido_id" value="<?php echo (int) $comanda['id']; ?>">
                            <input type="hidden" name="nuevo_estado" value="<?php echo $siguienteEstado; ?>">
                            <button type="submit" class="btn-accion <?php echo $claseBoton; ?>">
                                <?php echo $textoBoton; ?>
                            </button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>