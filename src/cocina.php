<?php
require_once 'conexion.php';

// Consulta para traer los pedidos que no están 'Listos' ni 'Entregados'
$sql_pedidos = "SELECT p.id, m.numero_mesa, p.estado, p.fecha_creacion, p.total 
                FROM pedidos p 
                JOIN mesas m ON p.mesa_id = m.id 
                WHERE p.estado IN ('Recibido', 'En preparación') 
                ORDER BY p.fecha_creacion ASC";

$resultado_pedidos = $conn->query($sql_pedidos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Cocina - Pedidos Activos</title>
    <link rel="stylesheet" href="style.css">
    <!-- Un pequeño script para que la página se recargue sola cada 10 segundos y vea pedidos nuevos -->
    <meta http-equiv="refresh" content="10"> 
</head>
<body>
    <header style="background-color: #e74c3c;">
        <h1>🔥 Panel de Cocina 🔥</h1>
        <p>Los pedidos se actualizan automáticamente</p>
    </header>
    
    <main>
        <section id="catalogo" style="flex: 1 1 100%; justify-content: flex-start;">
            <?php
            if ($resultado_pedidos && $resultado_pedidos->num_rows > 0) {
                while($pedido = $resultado_pedidos->fetch_assoc()) {
                    // Cambiamos el color de la tarjeta según el estado
                    $borde_color = ($pedido['estado'] == 'Recibido') ? '#e74c3c' : '#f39c12';
                    
                    echo "<div class='producto' style='border-top: 5px solid $borde_color;'>";
                    echo "<h2>Mesa " . $pedido["numero_mesa"] . "</h2>";
                    echo "<h3>Pedido #" . $pedido["id"] . "</h3>";
                    echo "<p><strong>Estado:</strong> " . $pedido["estado"] . "</p>";
                    echo "<p><strong>Hora:</strong> " . date("H:i", strtotime($pedido["fecha_creacion"])) . "</p>";
                    
                    // Formulario para cambiar el estado del pedido
                    echo "<form action='actualizar_estado.php' method='POST'>";
                    echo "<input type='hidden' name='pedido_id' value='" . $pedido["id"] . "'>";
                    
                    if($pedido['estado'] == 'Recibido') {
                        echo "<input type='hidden' name='nuevo_estado' value='En preparación'>";
                        echo "<button type='submit' style='background-color: #f39c12; width: 100%;'>Empezar a Preparar</button>";
                    } else if ($pedido['estado'] == 'En preparación') {
                        echo "<input type='hidden' name='nuevo_estado' value='Listo'>";
                        echo "<button type='submit' style='background-color: #2ecc71; width: 100%;'>¡Plato Listo!</button>";
                    }
                    
                    echo "</form>";
                    echo "</div>";
                }
            } else {
                echo "<div style='width: 100%; text-align: center; padding: 50px;'>";
                echo "<h2>No hay pedidos pendientes. ¡Buen trabajo!</h2>";
                echo "</div>";
            }
            ?>
        </section>
    </main>
</body>
</html>