# Estándares de Trabajo del Equipo - Sistema Web de Autogestión de Pedidos

## 1. Guía de Estilo y Nombres
*   **Guías Oficiales Adoptadas:** PSR-12 para backend (PHP) y W3C Standards para maquetación (HTML).
*   **Idioma:** Código, variables y base de datos en español. Mensajes de commit con prefijos de convención en inglés y descripciones en español.
*   **Formateadores:** "Prettier" para HTML y "PHP Intelephense" para PHP configurados en Visual Studio Code.
*   **Reglas Propias de Nombres (3):**
    1. Las tablas en MySQL siempre se nombran en minúsculas y plural (`mesas`, `pedidos`).
    2. Las variables de PHP y columnas de BD usan estricto `snake_case` (`numero_mesa`, `categoria_id`).
    3. Los atributos `id` y `class` en HTML usan estricto `kebab-case` (`ticket-pedido`, `resumen-cobro`).

## 2. Convención de Commits y Ramas
*   **Formato del Mensaje:** `tipo(ámbito): descripción en minúsculas [closes #ID]`
*   **Tipos Permitidos:** `feat` (nueva característica), `fix` (solución de error), `docs` (documentación), `chore` (mantenimiento/configuración de XAMPP/BD), `refactor` (mejora de código sin alterar funcionalidad).
*   **Esquema de Ramas:** Trunk-based development simplificado. Existirá una rama `main` de producción. Para desarrollos aislados se usarán ramas `feat/numero-issue` que luego se fusionan a `main`.

## 3. Definition of Ready (DoR)
*Un Issue está listo para empezar a programarse SÓLO SI cumple las siguientes condiciones:*
1. Está vinculado al Project "Tablero Scrum - Restaurante" en GitHub.
2. Tiene asignada al menos una etiqueta (Label) de área (`frontend`, `backend`, `base-de-datos`) y de prioridad.
3. Contiene en su descripción los Criterios de Aceptación con formato de checkbox de Markdown (`- [ ]`).
4. Tiene a una de las tres integrantes (Aracely, Mariana o Andrezza) asignada explícitamente (Assignee).

## 4. Definition of Done (DoD)
*Un Issue se considera terminado SÓLO SI un tercero ajeno al equipo puede abrir el repositorio y comprobar empíricamente lo siguiente:*
1. El código nuevo está fusionado en la rama `main` del repositorio de GitHub.
2. El mensaje del Commit en el historial incluye la palabra clave `closes #ID`, enlazando la tarea técnica.
3. La tarjeta del Issue se encuentra ubicada físicamente en la columna "Completado" del Tablero de GitHub Projects.
4. Las tablas creadas coinciden exactamente con los scripts `.sql` guardados en la carpeta `db/`.
5. Los archivos `.html` abren en el navegador sin arrojar errores en la consola de herramientas de desarrollador (F12).
6. Todas las funciones de PHP ejecutan sin mostrar advertencias (Warnings) ni errores fatales (Fatal Errors) en la vista del navegador al usar XAMPP.

## 5. Política de Revisión (Code Review)
*   **Responsabilidades:** Aracely revisa el código Frontend (HTML) de Andrezza; Andrezza revisa el código Backend (PHP/MySQL) de Aracely; Mariana verifica que ambas cumplan con los criterios del DoD en el tablero.
*   **Plazo Máximo:** 24 horas hábiles tras solicitar la revisión en el repositorio.
*   **Causales Explícitas de Bloqueo (Rechazo del código):**
    *   Errores de sintaxis que impidan la ejecución (ej. PHP Fatal Errors o base de datos no conectada).
    *   Vistas HTML que no carguen correctamente en el navegador o formularios que no envíen datos.
    *   Incumplimiento comprobable de la regla de variables en `snake_case`.
*   **Causales de No Bloqueo (Aprobación con observaciones):**
    *   Sugerencias de optimización de rendimiento (ej. mejorar una consulta SQL que ya funciona).
    *   Detalles de diseño en HTML/CSS que difieren ligeramente del mockup original pero no rompen la estructura.
*   **Cómo se comenta:** Directamente en la línea de código afectada dentro de GitHub usando "Request changes" para bloquear, y "Comment" para observaciones que no bloquean.

## 6. Aceptación
*   ARACELY MELANY HINOJOSA TORREJON: conozco y acepto estos estándares.
*   LOPEZ MARIANA: conozco y acepto estos estándares.
*   BATISTA ANDREZZA: conozco y acepto estos estándares.