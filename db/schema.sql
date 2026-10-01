-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS sistema_pedidos;
USE sistema_pedidos;

-- Tabla de Categorías (Bebidas, Platos Fuertes, etc.)
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

-- Tabla de Productos (Menú Digital - RF1 y RF2)
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    disponible BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- Tabla de Mesas (Registro exigido - RF3)
CREATE TABLE mesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_mesa INT NOT NULL UNIQUE,
    estado ENUM('Disponible', 'Ocupada') DEFAULT 'Disponible'
);

-- Tabla de Pedidos (Cabecera para Cocina, Mesero y Caja - RF4, RF5, RF6, RF7)
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mesa_id INT NOT NULL,
    estado ENUM('Recibido', 'En preparación', 'Listo', 'Entregado', 'Pagado') DEFAULT 'Recibido',
    total DECIMAL(10, 2) DEFAULT 0.00,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mesa_id) REFERENCES mesas(id)
);

-- Tabla de Detalle de Pedidos (Cantidades seleccionadas - RF2)
CREATE TABLE detalle_pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);