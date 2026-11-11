<?php
// Configuración de conexión para XAMPP y base de datos unificada Vantia
$host = 'localhost';
$baseDatos = 'vantia_empleabilidad_v2';
$usuario = 'root';
$contrasena = '';

try {
    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $conexion = new PDO("mysql:host=$host;dbname=$baseDatos;charset=utf8mb4", $usuario, $contrasena, $opciones);
} catch (PDOException $error) {
    error_log('Error de conexión Vantia: ' . $error->getMessage());
    http_response_code(500);
    exit('No se pudo conectar con MySQL. Verifica que MySQL esté activo en XAMPP y que la base de datos ' . $baseDatos . ' exista.');
}
