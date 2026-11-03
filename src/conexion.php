<?php
declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_OFF);

$host = 'localhost';
$usuario = 'root';
$clave = '';
$baseDatos = 'sistema_pedidos';

$conn = new mysqli($host, $usuario, $clave, $baseDatos);

if ($conn->connect_error) {
    http_response_code(500);
    die('Error de conexión con la base de datos del sistema.');
}

$conn->set_charset('utf8mb4');