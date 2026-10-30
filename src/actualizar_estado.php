<?php
declare(strict_types=1);
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pedido_id'], $_POST['nuevo_estado'])) {
    $pedidoId = intval($_POST['pedido_id']);
    $nuevoEstado = (string) $_POST['nuevo_estado'];
    $permitidos = ['En preparación', 'Listo'];

    if ($pedidoId > 0 && in_array($nuevoEstado, $permitidos, true)) {
        $stmt = $conn->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
        if ($stmt !== false) {
            $stmt->bind_param('si', $nuevoEstado, $pedidoId);
            $stmt->execute();
            $stmt->close();
        }
    }
}

header('Location: cocina.php');
exit;