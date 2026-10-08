<?php
// conexion.php
$nombreServidor = "localhost";
$nombreUsuario = "root";
$contrasena = "";
$nombreBaseDatos = "restaurante_bd";

// Crear la conexión
$conexionBd = new mysqli($nombreServidor, $nombreUsuario, $contrasena, $nombreBaseDatos);

// Verificar la conexión
if ($conexionBd->connect_error) {
    die("Error de conexión: " . $conexionBd->connect_error);
}
