<?php
include 'conexion.php';
// Código PHP da Aracely mantido intacto para não quebrar o banco
$consultaSql = "SELECT id, nombre, descripcion, precio FROM productos WHERE estaDisponible = TRUE";
$listaProductos = $conexionBd->query($consultaSql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Digital</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>🍔 Nuestro Menú</h1>
        <p>Haz tu pedido rápido y fácil</p>
    </header>

    <div class="container">
        <div class="grid">
            <?php
            if ($listaProductos && $listaProductos->num_rows > 0) {
                while($producto = $listaProductos->fetch_assoc()) {
                    echo "<div class='card'>";
                    echo "<h3>" . htmlspecialchars($producto["nombre"]) . "</h3>";
                    echo "<p>" . htmlspecialchars($producto["descripcion"]) . "</p>";
                    echo "<p class='precio'>$" . number_format($producto["precio"], 2) . "</p>";
                    
                    echo "<div class='controles'>";
                    echo "<button class='btn-cant' onclick='cambiarCantidad(" . $producto["id"] . ", -1)'>-</button>";
                    echo "<span id='cantidad-" . $producto["id"] . "'>0</span>";
                    echo "<button class='btn-cant' onclick='cambiarCantidad(" . $producto["id"] . ", 1)'>+</button>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>Cargando menú...</p>";
            }
            ?>
        </div>

        <div class="card" style="margin-top: 30px;">
            <h3>Confirma tu Pedido</h3>
            <label for="numeroMesa">Número de Mesa:</label><br>
            <input type="number" id="numeroMesa" class="input-mesa" min="1" placeholder="Ej: 5"><br>
            <button class="btn" onclick="confirmarPedido()">Enviar a Cocina</button>
        </div>
    </div>

    <script>
        const pedido = {};
        function cambiarCantidad(id, cambio) {
            if (!pedido[id]) pedido[id] = 0;
            pedido[id] += cambio;
            if (pedido[id] < 0) pedido[id] = 0;
            document.getElementById('cantidad-' + id).innerText = pedido[id];
        }
        function confirmarPedido() {
            const mesa = document.getElementById('numeroMesa').value;
            if (!mesa) return alert("Por favor, ingresa el número de tu mesa.");
            alert("¡Pedido enviado! (Listo para que Aracely conecte al Backend)");
        }
    </script>
</body>
</html>