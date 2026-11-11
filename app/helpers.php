<?php
// Funciones auxiliares globales del sistema Vantia

function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

function vista($archivo, $datos = [])
{
    extract($datos);
    require __DIR__ . '/../views/' . $archivo . '.php';
}

function calcularEdad($fechaNacimiento)
{
    if (empty($fechaNacimiento)) {
        return 0;
    }
    $nacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime();
    return $hoy->diff($nacimiento)->y;
}

function esMenorDeEdad($fechaNacimiento)
{
    return calcularEdad($fechaNacimiento) < 18;
}

function etiquetaTipo($tipo)
{
    $tipos = [
        'medio_tiempo' => 'Medio tiempo',
        'practicas' => 'Prácticas',
        'fines_semana' => 'Fines de semana',
        'remoto' => 'Remoto',
    ];
    return $tipos[$tipo] ?? ucfirst(str_replace('_', ' ', (string) $tipo));
}

function etiquetaEstado($estado)
{
    $estados = [
        'pendiente' => 'Pendiente',
        'revision' => 'En revisión',
        'entrevista' => 'Entrevista',
        'contratado' => 'Contratado',
        'rechazado' => 'Rechazado',
    ];
    return $estados[$estado] ?? ucfirst((string) $estado);
}

// Sube un archivo validado (CV, permiso de tutor o documento legal de empresa)
function subirArchivo($campoArchivo, $carpetaDestino, $prefijo = 'doc')
{
    if (!isset($_FILES[$campoArchivo]) || $_FILES[$campoArchivo]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $permitidos = ['pdf', 'jpg', 'jpeg', 'png'];
    $extension = strtolower(pathinfo($_FILES[$campoArchivo]['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $permitidos, true)) {
        return false;
    }

    // Tamaño máximo 5MB
    if ($_FILES[$campoArchivo]['size'] > 5 * 1024 * 1024) {
        return false;
    }

    $nombreArchivo = $prefijo . '_' . uniqid('', true) . '.' . $extension;
    $directorioDestino = dirname(__DIR__) . '/uploads/' . trim($carpetaDestino, '/\\') . '/';

    if (!is_dir($directorioDestino)) {
        mkdir($directorioDestino, 0755, true);
    }

    $rutaCompleta = $directorioDestino . $nombreArchivo;

    if (move_uploaded_file($_FILES[$campoArchivo]['tmp_name'], $rutaCompleta)) {
        return trim($carpetaDestino, '/\\') . '/' . $nombreArchivo;
    }

    return false;
}
