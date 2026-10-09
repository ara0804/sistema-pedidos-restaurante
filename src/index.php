<?php
require_once 'conexion.php';

$sql_productos = "SELECT p.nombre, p.precio, c.nombre as categoria FROM productos p JOIN categorias c ON p.categoria_id = c.id WHERE p.disponible = 1";
$resultado = $conn->query($sql_productos);

$sql_mesas = "SELECT id, numero_mesa FROM mesas WHERE estado = 'Disponible'";
$mesas_disp = $conn->query($sql_mesas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Digital - Cliente</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <header>
        <h1>Menú Digital</h1>
        <form action="procesar_pedido.php" method="POST">
            <label for="mesa_id">Seleccione su Mesa:</label>
            <select name="mesa_id" id="mesa_id" required>
                <option value="">-- Elija una mesa --</option>
                <?php
                if ($mesas_disp && $mesas_disp->num_rows > 0) {
                    while($mesa = $mesas_disp->fetch_assoc()) {
                        echo "<option value='" . $mesa['id'] . "'>Mesa " . $mesa['numero_mesa'] . "</option>";
                    }
                }
                ?>
            </select>
    </header>
    <main>
        <section id="catalogo">
            <?php
            if ($resultado && $resultado->num_rows > 0) {
                while($fila = $resultado->fetch_assoc()) {
                    echo "<div class='producto'>"; 
                    echo "<h3>" . $fila["nombre"] . " <small>(" . $fila["categoria"] . ")</small></h3>";
                    echo "<p>Precio: Bs. " . number_format($fila["precio"], 2) . "</p>";
                    echo "<button type='button'>Añadir al carrito</button>";
                    echo "</div>";
                }
            } else {
                echo "<p style='width: 100%; text-align: center;'>El menú está vacío. Inyecte datos de prueba en phpMyAdmin.</p>";
            }
            ?>
        </section>
        <aside id="carrito">
            <h2>Tu Pedido</h2>
            <p>Total a pagar: Bs. 0.00</p>
            <button type="submit">Confirmar y Enviar a Cocina</button>
        </form>
        </aside>
    </main>
</body>
</html>