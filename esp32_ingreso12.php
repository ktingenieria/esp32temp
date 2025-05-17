<?php
date_default_timezone_set("America/Mexico_City");

// Clave que debe coincidir con la del ESP32
$clave_valida = "segura123";

// Validar clave
if (!isset($_GET['key']) || $_GET['key'] !== $clave_valida) {
    http_response_code(403);
    echo "Acceso denegado.";
    exit;
}

// Obtener datos
$temp = floatval($_GET['temp'] ?? 0);
$hum = floatval($_GET['hum'] ?? 0);
$fecha = date("Y-m-d H:i:s");

// Guardar en CSV
$archivo = "datos.csv";
if (!file_exists($archivo)) {
    file_put_contents($archivo, "fecha,temp,humedad\n", FILE_APPEND);
}
file_put_contents($archivo, "$fecha,$temp,$hum\n", FILE_APPEND);

echo "Datos guardados: $fecha, $temp °C, $hum %";
