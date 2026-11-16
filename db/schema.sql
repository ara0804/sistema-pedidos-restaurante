CREATE DATABASE IF NOT EXISTS sistema_pedidos
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sistema_pedidos;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS detalle_pedido;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS mesas;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS categorias;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Categorías
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL
);

-- 2. Productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    disponible TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- 3. Mesas
CREATE TABLE mesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL UNIQUE,
    estado ENUM('Disponible', 'Ocupada') NOT NULL DEFAULT 'Disponible'
);

-- 4. Pedidos
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mesa_id INT NOT NULL,
    estado ENUM('Recibido', 'En preparación', 'Listo', 'Entregado', 'Pagado') NOT NULL DEFAULT 'Recibido',
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mesa_id) REFERENCES mesas(id)
);

-- 5. Detalle del pedido
CREATE TABLE detalle_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Datos semilla: 5 categorías colombianas
INSERT INTO categorias (id, nombre) VALUES
(1, 'Perros Calientes'),
(2, 'Hamburguesas'),
(3, 'Salchipapas'),
(4, 'Alitas'),
(5, 'Bebidas');

-- Datos semilla: 17 productos
INSERT INTO productos (id, categoria_id, nombre, descripcion, precio, disponible) VALUES
(1, 1, 'Perro Sencillo', 'Pan artesanal, salchicha americana, ripio de papa, queso rallado y salsas de la casa.', 12000, 1),
(2, 1, 'Perro Especial', 'Salchicha americana, tocineta crocante, doble queso fundido, cebolla caramelizada y salsas.', 16000, 1),
(3, 1, 'Perro Suizo', 'Salchicha suiza a la plancha, queso mozzarella gratinado, maíz tierno y salsa tártara.', 18000, 1),
(4, 2, 'Hamburguesa Clásica', '150 g de carne de res a la parrilla, queso cheddar, lechuga, tomate y pan brioche.', 18000, 1),
(5, 2, 'Hamburguesa Doble Carne', '300 g de carne de res, doble queso cheddar, tocineta ahumada y salsa de la casa.', 25000, 1),
(6, 2, 'Hamburguesa Criolla', 'Carne de res, queso doble crema, huevo frito, tocineta, ripio de papa y salsa BBQ.', 22000, 1),
(7, 2, 'Hamburguesa de Pollo Crispy', 'Pechuga apanada crocante, queso mozzarella, pepinillos y salsa miel mostaza.', 19000, 1),
(8, 3, 'Salchipapa Clásica', 'Papas a la francesa, salchicha tipo ranchera, queso costeño rallado y salsas.', 15000, 1),
(9, 3, 'Salchipapa Especial', 'Papas a la francesa, salchicha, pollo desmechado, tocineta, maíz tierno y queso gratinado.', 24000, 1),
(10, 3, 'Salchipapa Costeña', 'Papas, salchicha, chorizo santarrosano, butifarra, lechuga, suero costeño y queso.', 26000, 1),
(11, 4, 'Alitas BBQ (6 und)', 'Seis alitas bañadas en salsa BBQ artesanal acompañadas de papas a la francesa.', 21000, 1),
(12, 4, 'Alitas Miel Mostaza (6 und)', 'Seis alitas crocantes en salsa miel mostaza con porción de papas.', 21000, 1),
(13, 4, 'Combo Alitas Mixtas (12 und)', 'Doce alitas con dos salsas a elección y papas a la francesa para compartir.', 38000, 1),
(14, 5, 'Gaseosa Personal 400 ml', 'Bebida gaseosa fría sabor a elección.', 4500, 1),
(15, 5, 'Limonada Natural', 'Limonada recién exprimida bien fría en vaso de 16 oz.', 6000, 1),
(16, 5, 'Jugo Natural en Agua', 'Jugo natural de mango, maracuyá o mora en vaso de 16 oz.', 7000, 1),
(17, 5, 'Agua en Botella 600 ml', 'Agua purificada sin gas bien fría.', 3500, 1);

-- Datos semilla: 10 mesas (Mesas #2 y #4 ocupadas para demostración inicial)
INSERT INTO mesas (id, numero, estado) VALUES
(1, 1, 'Disponible'),
(2, 2, 'Ocupada'),
(3, 3, 'Disponible'),
(4, 4, 'Ocupada'),
(5, 5, 'Disponible'),
(6, 6, 'Disponible'),
(7, 7, 'Disponible'),
(8, 8, 'Disponible'),
(9, 9, 'Disponible'),
(10, 10, 'Disponible');

-- Datos semilla: 2 pedidos precargados para demostrar Cocina, Mesero y Caja al instante
INSERT INTO pedidos (id, mesa_id, estado) VALUES
(1, 2, 'En preparación'),
(2, 4, 'Listo');

INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES
(1, 4, 2, 18000),
(1, 15, 2, 6000),
(2, 9, 1, 24000),
(2, 14, 1, 4500);