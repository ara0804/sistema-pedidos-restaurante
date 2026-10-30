<?php
declare(strict_types=1);
require_once 'conexion.php';

$mesas = [];
$resMesas = $conn->query("SELECT id, numero, estado FROM mesas ORDER BY numero ASC");
if ($resMesas instanceof mysqli_result) {
    while ($fila = $resMesas->fetch_assoc()) {
        $mesas[] = $fila;
    }
    $resMesas->free();
}

$catalogo = [];
$sqlProd = "SELECT p.id, p.nombre, p.descripcion, p.precio, c.nombre AS categoria
            FROM productos p
            INNER JOIN categorias c ON p.categoria_id = c.id
            WHERE p.disponible = 1
            ORDER BY c.id ASC, p.nombre ASC";
$resProd = $conn->query($sqlProd);
if ($resProd instanceof mysqli_result) {
    while ($fila = $resProd->fetch_assoc()) {
        $cat = (string) $fila['categoria'];
        $catalogo[$cat][] = $fila;
    }
    $resProd->free();
}

$alertaTipo = '';
$alertaTexto = '';
if (isset($_GET['ok']) && $_GET['ok'] === '1') {
    $pedidoNum = isset($_GET['pedido']) ? (int) $_GET['pedido'] : 0;
    $mesaNum = isset($_GET['mesa']) ? (int) $_GET['mesa'] : 0;
    $alertaTipo = 'alerta-ok';
    $alertaTexto = "Pedido #{$pedidoNum} enviado a cocina correctamente para la Mesa #{$mesaNum}.";
} elseif (isset($_GET['error'])) {
    $alertaTipo = 'alerta-error';
    if ($_GET['error'] === 'mesa') {
        $alertaTexto = 'Debes seleccionar una mesa válida antes de enviar el pedido.';
    } elseif ($_GET['error'] === 'vacio') {
        $alertaTexto = 'Agrega al menos un producto al carrito para confirmar el pedido.';
    } else {
        $alertaTexto = 'Ocurrió un inconveniente al registrar tu pedido. Intenta nuevamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Digital - Autogestión de Pedidos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="con-barra-carrito">
    <header class="top-bar">
        <div class="marca">
            <h1>Date un Gustito · Menú Digital</h1>
        </div>
    </header>

    <main class="contenedor-panel">
        <?php if ($alertaTexto !== ''): ?>
            <div class="alerta <?php echo $alertaTipo; ?>">
                <?php echo htmlspecialchars($alertaTexto, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="procesar_pedido.php" id="form-pedido">
            <section class="selector-mesa-card">
                <div>
                    <label for="mesa_id" class="selector-label">1. Selecciona tu mesa</label>
                    <p class="selector-ayuda">El pedido llegará directo a cocina con el número de tu mesa.</p>
                </div>
                <select name="mesa_id" id="mesa_id" class="select-mesa" required>
                    <option value="">-- Selecciona una mesa --</option>
                    <?php foreach ($mesas as $mesa): ?>
                        <option value="<?php echo (int) $mesa['id']; ?>">
                            Mesa #<?php echo (int) $mesa['numero']; ?> (<?php echo htmlspecialchars((string) $mesa['estado'], ENT_QUOTES, 'UTF-8'); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </section>

            <?php if (count($catalogo) === 0): ?>
                <p class="estado-vacio">El menú está vacío.</p>
            <?php else: ?>
                <?php foreach ($catalogo as $nombreCategoria => $productos): ?>
                    <section class="seccion-categoria">
                        <h2 class="titulo-categoria"><?php echo htmlspecialchars($nombreCategoria, ENT_QUOTES, 'UTF-8'); ?></h2>
                        <div class="grid-productos">
                            <?php foreach ($productos as $prod): ?>
                                <?php $prodId = (int) $prod['id']; ?>
                                <article class="producto">
                                    <div class="producto-info">
                                        <h3><?php echo htmlspecialchars((string) $prod['nombre'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                        <p class="producto-desc"><?php echo htmlspecialchars((string) $prod['descripcion'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                    <div class="producto-pie">
                                        <span class="producto-precio">$<?php echo number_format((float) $prod['precio'], 0, ',', '.'); ?></span>
                                        <div class="control-cantidad">
                                            <button type="button" class="btn-qty" data-accion="restar" data-target="qty-<?php echo $prodId; ?>" aria-label="Disminuir">−</button>
                                            <input
                                                type="number"
                                                id="qty-<?php echo $prodId; ?>"
                                                name="cantidades[<?php echo $prodId; ?>]"
                                                value="0"
                                                min="0"
                                                max="20"
                                                class="input-qty"
                                                data-precio="<?php echo (float) $prod['precio']; ?>"
                                                readonly
                                            >
                                            <button type="button" class="btn-qty" data-accion="sumar" data-target="qty-<?php echo $prodId; ?>" aria-label="Aumentar">+</button>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="barra-carrito">
                <div class="resumen-carrito">
                    <span id="total-items">0 productos</span>
                    <strong id="total-precio">$0</strong>
                </div>
                <button type="submit" class="btn-confirmar">Pedir</button>
            </div>
        </form>
    </main>

    <script>
        (function () {
            const inputs = document.querySelectorAll('.input-qty');
            const totalItemsEl = document.getElementById('total-items');
            const totalPrecioEl = document.getElementById('total-precio');

            function actualizarResumen() {
                let items = 0;
                let total = 0;
                inputs.forEach(function (input) {
                    const cant = parseInt(input.value, 10) || 0;
                    const precio = parseFloat(input.dataset.precio) || 0;
                    items += cant;
                    total += cant * precio;
                });
                totalItemsEl.textContent = items === 1 ? '1 producto' : items + ' productos';
                totalPrecioEl.textContent = '$' + total.toLocaleString('es-CO');
            }

            document.querySelectorAll('.btn-qty').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const input = document.getElementById(btn.dataset.target);
                    if (!input) return;
                    let valor = parseInt(input.value, 10) || 0;
                    if (btn.dataset.accion === 'sumar' && valor < 20) valor++;
                    if (btn.dataset.accion === 'restar' && valor > 0) valor--;
                    input.value = String(valor);
                    actualizarResumen();
                });
            });
        })();
    </script>
</body>
</html>