<?php
declare(strict_types=1);
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$mesaId = isset($_POST['mesa_id']) ? intval($_POST['mesa_id']) : 0;
if ($mesaId <= 0) {
    header('Location: index.php?error=mesa');
    exit;
}

$cantidadesRaw = $_POST['cantidades'] ?? [];
$itemsValidos = [];
if (is_array($cantidadesRaw)) {
    foreach ($cantidadesRaw as $prodId => $cant) {
        $id = intval($prodId);
        $qty = intval($cant);
        if ($id > 0 && $qty > 0) {
            $itemsValidos[$id] = min($qty, 20);
        }
    }
}

if (count($itemsValidos) === 0) {
    header('Location: index.php?error=vacio');
    exit;
}

$conn->begin_transaction();
try {
    // Verificar existencia y disponibilidad real de la mesa
    $stmtCheckMesa = $conn->prepare("SELECT numero, estado FROM mesas WHERE id = ? FOR UPDATE");
    if ($stmtCheckMesa === false) {
        throw new RuntimeException('Error verificando mesa.');
    }
    $stmtCheckMesa->bind_param('i', $mesaId);
    $stmtCheckMesa->execute();
    $resMesa = $stmtCheckMesa->get_result();
    $mesaRow = $resMesa ? $resMesa->fetch_assoc() : null;
    $stmtCheckMesa->close();

    if (!$mesaRow) {
        $conn->rollback();
        header('Location: index.php?error=mesa');
        exit;
    }

    if ((string) $mesaRow['estado'] !== 'Disponible') {
        $conn->rollback();
        header('Location: index.php?error=ocupada');
        exit;
    }

    // Crear cabecera del pedido
    $stmtPedido = $conn->prepare("INSERT INTO pedidos (mesa_id, estado) VALUES (?, 'Recibido')");
    if ($stmtPedido === false) {
        throw new RuntimeException('Error creando pedido.');
    }
    $stmtPedido->bind_param('i', $mesaId);
    $stmtPedido->execute();
    $pedidoId = (int) $conn->insert_id;
    $stmtPedido->close();

    // Insertar detalle consultando el precio oficial en BD
    $stmtPrecio = $conn->prepare("SELECT precio FROM productos WHERE id = ? AND disponible = 1");
    $stmtDetalle = $conn->prepare(
        "INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)"
    );
    if ($stmtPrecio === false || $stmtDetalle === false) {
        throw new RuntimeException('Error preparando inserción de detalle.');
    }

    $insertados = 0;
    foreach ($itemsValidos as $prodId => $qty) {
        $stmtPrecio->bind_param('i', $prodId);
        $stmtPrecio->execute();
        $resP = $stmtPrecio->get_result();
        $prodRow = $resP ? $resP->fetch_assoc() : null;

        if ($prodRow) {
            $precioUnitario = (float) $prodRow['precio'];
            $stmtDetalle->bind_param('iiid', $pedidoId, $prodId, $qty, $precioUnitario);
            $stmtDetalle->execute();
            $insertados++;
        }
    }
    $stmtPrecio->close();
    $stmtDetalle->close();

    if ($insertados === 0) {
        $conn->rollback();
        header('Location: index.php?error=vacio');
        exit;
    }

    // Actualizar estado de la mesa a Ocupada
    $stmtMesa = $conn->prepare("UPDATE mesas SET estado = 'Ocupada' WHERE id = ?");
    if ($stmtMesa === false) {
        throw new RuntimeException('Error actualizando estado de mesa.');
    }
    $stmtMesa->bind_param('i', $mesaId);
    $stmtMesa->execute();
    $stmtMesa->close();

    $conn->commit();
    $numeroMesa = (int) $mesaRow['numero'];
    header("Location: index.php?ok=1&pedido={$pedidoId}&mesa={$numeroMesa}");
    exit;
} catch (Throwable $e) {
    $conn->rollback();
    header('Location: index.php?error=sistema');
    exit;
}