# Sistema Web de Autogestión de Pedidos

Aplicación web local para la gestión de pedidos de un restaurante de comidas rápidas, conectando en un solo flujo al cliente, la cocina, el mesero y la caja presencial.

## Stack Tecnológico

- **Frontend:** HTML5 y CSS3 responsivo (validado en 360×640 px)
- **Backend:** PHP (MySQLi con consultas preparadas y transacciones)
- **Base de datos:** MySQL / MariaDB (`sistema_pedidos`)
- **Entorno local:** XAMPP (Apache + MySQL)
- **Calidad y estándares:** PHP Intelephense, Prettier, Trunk-Based Development y Conventional Commits (`ESTANDARES.md`)

## Instalación y Ejecución en XAMPP

1. Clonar el repositorio dentro de `C:\xampp\htdocs\sistema-pedidos-restaurante`.
2. Iniciar **Apache** y **MySQL** en el panel de control de XAMPP.
3. Abrir `http://localhost/phpmyadmin` e importar `db/schema.sql` (crea 5 categorías, 17 productos, 10 mesas y 2 pedidos de demostración).

## Rutas de Acceso (Separación Cliente vs. Personal)

- **Clientes (menú digital sin enlaces administrativos):** `http://localhost/sistema-pedidos-restaurante/src/index.php`
- **Panel de Cocina:** `http://localhost/sistema-pedidos-restaurante/src/cocina.php`
- **Panel de Mesero:** `http://localhost/sistema-pedidos-restaurante/src/mesero.php`
- **Panel de Caja:** `http://localhost/sistema-pedidos-restaurante/src/caja.php`

## Equipo de Desarrollo

- **Aracely Hinojosa:** Líder Técnica / Backend (Sprints 1–3) · QA (Sprint 4)
- **Andrezza Batista:** Frontend / UI (Sprints 1–3) · Scrum Master (Sprint 4)
- **Mariana López:** Scrum Master / QA (Sprints 1–3) · Documentación y Cierre (Sprint 4)