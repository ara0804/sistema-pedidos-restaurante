<?php
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['pedido_id']) && isset($_POST['nuevo_estado'])) {
        $pedido_id = intval($_POST['pedido_id']);
        $nuevo_estado = $_POST['nuevo_estado'];
        
        // Evitamos inyección SQL
        $nuevo_estado = $conn->real_escape_string($nuevo_estado);
        
        $sql_actualizar = "UPDATE pedidos SET estado = '$nuevo_estado' WHERE id = $pedido_id";
        
        if ($conn->query($sql_actualizar) === TRUE) {
            // Volver automáticamente a la pantalla de cocina
            header("Location: cocina.php");
            exit();
        } else {
            echo "Error al actualizar el estado: " . $conn->error;
        }
    }
} else {
    header("Location: cocina.php");
    exit();
}
?>