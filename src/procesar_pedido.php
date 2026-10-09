<?php
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['mesa_id']) && !empty($_POST['mesa_id'])) {
        $mesa_id = intval($_POST['mesa_id']);
        
        $sql_pedido = "INSERT INTO pedidos (mesa_id, estado, total) VALUES ($mesa_id, 'Recibido', 0.00)";
        
        if ($conn->query($sql_pedido) === TRUE) {
            $conn->query("UPDATE mesas SET estado = 'Ocupada' WHERE id = $mesa_id");
            echo "<script>alert('¡Pedido enviado a cocina exitosamente!'); window.location.href='index.php';</script>";
            exit();
        } else {
            echo "Error fatal al registrar en BD: " . $conn->error;
        }
    } else {
        echo "<script>alert('Debe seleccionar una mesa válida.'); window.location.href='index.php';</script>";
    }
}
?>