<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set("America/Tijuana");

// Validar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Acceso solo permitido por POST.";
    exit;
}

// Validar clave
$clave_valida = "segura123";
$clave = $_POST['key'] ?? '';
if ($clave !== $clave_valida) {
    http_response_code(403);
    echo "Clave incorrecta.";
    exit;
}

// Validar datos recibidos
if (!isset($_POST['temp']) || !isset($_POST['hum'])) {
    echo "Faltan datos.";
    exit;
}

$temp = floatval($_POST['temp']);
$hum = floatval($_POST['hum']);
$fecha = date("Y-m-d H:i:s");

// Ruta del archivo
$archivo = __DIR__ . "/datos.csv";

// Crear si no existe
if (!file_exists($archivo)) {
    $encabezado = "fecha,temp,humedad\n";
    if (file_put_contents($archivo, $encabezado, FILE_APPEND) === false) {
        echo "Error al crear archivo.";
        exit;
    }
}

// Escribir datos
$linea = "$fecha,$temp,$hum\n";
if (file_put_contents($archivo, $linea, FILE_APPEND) === false) {
    echo "Error al escribir datos.";
    exit;
}

echo "OK: $fecha";
?>
