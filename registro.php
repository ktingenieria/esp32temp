<?php
date_default_timezone_set("America/Tijuana"); // ajusta a tu zona horaria

// Verificar si se recibieron los parámetros
if (isset($_GET['temp']) && isset($_GET['hum'])) {
    $temp = floatval($_GET['temp']);
    $hum = floatval($_GET['hum']);
    $fecha = date("Y-m-d H:i:s");

    // Archivo donde se guardarán los datos
    $archivo = "datos.csv";

    // Si no existe, escribir cabecera
    if (!file_exists($archivo)) {
        file_put_contents($archivo, "fecha,temp,humedad\n", FILE_APPEND);
    }

    // Guardar datos
    $linea = "$fecha,$temp,$hum\n";
    file_put_contents($archivo, $linea, FILE_APPEND);

    echo "Datos recibidos: $temp °C, $hum %";
} else {
    echo "Faltan datos.";
}
?>
