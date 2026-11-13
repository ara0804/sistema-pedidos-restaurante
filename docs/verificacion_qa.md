# Reporte de Verificación de Calidad (Sprint 4)

- **Fecha de ejecución:** 13 de noviembre de 2026
- **Responsable QA (rotación Sprint 4):** Aracely Hinojosa
- **Entorno validado:** XAMPP local (Apache + MariaDB/MySQL, base de datos `sistema_pedidos`)
- **Herramientas estáticas:** PHP Intelephense y Prettier en Visual Studio Code

## 1. Validación de Umbrales del Plan de Calidad

| Atributo | Umbral definido | Estado Corte 2 | Resultado Sprint 4 | Estado |
| :--- | :--- | :--- | :--- | :--- |
| Estabilidad funcional | 0 Fatal Errors, 0 Warnings PHP, 0 errores consola F12 | No medido | 0 errores PHP / 0 consola F12 (ventana incógnito) | Cumple |
| Rendimiento | Inserción en `procesar_pedido.php` y retorno a `index.php` < 1,5 s (Network) | No medido | 134 ms (`procesar_pedido.php`) + 10 ms (`index.php`) = 144 ms total | Cumple |
| Usabilidad móvil | 0 scroll horizontal en viewport 360×640 px | No medido | 0 desbordamiento horizontal | Cumple |
| Análisis estático | 0 errores de sintaxis en PHP Intelephense | No medido | 0 errores / 0 advertencias | Cumple |

## 2. Defectos Cerrados en Pruebas de Integración

- **DEF-01 (Usabilidad móvil):** Desbordamiento horizontal en pantallas menores a 360 px. Cerrado en Sprint 3.
- **DEF-02 (Integridad funcional):** El sistema permitía seleccionar mesas ocupadas y mantenía visible la alerta tras recargar. Cerrado en Sprint 4 deshabilitando mesas ocupadas en frontend (`disabled`), validando con `SELECT ... FOR UPDATE` en `procesar_pedido.php` y limpiando la URL con `history.replaceState`.

## 3. Flujo End-to-End Verificado

1. **Cliente (`src/index.php`):** 5 categorías, 10 mesas con bloqueo de ocupadas y envío transaccional validado (Pedido #8, Mesa #1).
2. **Cocina (`src/cocina.php`):** Auto-refresco cada 10 s y transición `Recibido` → `En preparación` → `Listo`.
3. **Mesero (`src/mesero.php`):** Listado exclusivo de pedidos en estado `Listo` y paso a `Entregado`.
4. **Caja (`src/caja.php`):** Cálculo total por mesa, cierre a `Pagado` y liberación transaccional a `Disponible`.