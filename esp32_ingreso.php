<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set("America/Tijuana");

// 🔐 Validación
$clave_valida = "segura123";

// Procesar datos POST del ESP32
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clave = $_POST['key'] ?? '';
    $tempC = floatval($_POST['temp'] ?? 0);
    $hum = floatval($_POST['hum'] ?? 0);
    $tempF = $tempC * 9 / 5 + 32;

    if ($clave !== $clave_valida) {
        http_response_code(403);
        echo "Clave incorrecta.";
        exit;
    }

    $fecha = date("Y-m-d H:i:s");
    $archivo = __DIR__ . "/datos.csv";

    if (!file_exists($archivo)) {
        file_put_contents($archivo, "fecha,tempC,tempF,humedad\n");
    }

    $linea = "$fecha,$tempC,$tempF,$hum\n";
    file_put_contents($archivo, $linea, FILE_APPEND);
    echo "OK: $fecha";
    exit;
}

// Mostrar últimos 10 registros
$lineas = file_exists("datos.csv") ? file("datos.csv") : [];
$total = count($lineas);

echo "<h2>Últimos 10 registros</h2>";
echo "<table border='1' cellpadding='5'><tr><th>Fecha</th><th>Temp (°C)</th><th>Temp (°F)</th><th>Humedad (%)</th></tr>";

for ($i = max(1, $total - 10); $i < $total; $i++) {
    $datos = str_getcsv($lineas[$i]);
    if (count($datos) == 4) {
        echo "<tr><td>{$datos[0]}</td><td>{$datos[1]}</td><td>{$datos[2]}</td><td>{$datos[3]}</td></tr>";
    }
}
echo "</table>";

echo "<p><a href='datos.csv' download>📥 Descargar CSV</a></p>";
?>
