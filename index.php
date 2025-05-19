<?php
date_default_timezone_set("America/Tijuana");
$archivo = "datos.csv";

// Descargar CSV
if (isset($_GET['descargar']) && file_exists($archivo)) {
    header("Content-Disposition: attachment; filename=datos.csv");
    header("Content-Type: text/csv");
    readfile($archivo);
    exit;
}

// Borrar historial
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'borrar') {
    if ($_POST['clave'] === 'clave123') {
        file_put_contents($archivo, "fecha,tempC,tempF,humedad\n");
        $mensaje = "Historial borrado exitosamente.";
    } else {
        $mensaje = "Contraseña incorrecta.";
    }
}

// Leer datos
$fechas = $tempsC = $tempsF = $hums = [];
$lineas = file_exists($archivo) ? file($archivo) : [];
$total = count($lineas);
for ($i = max(1, $total - 20); $i < $total; $i++) {
    $cols = str_getcsv($lineas[$i]);
    if (count($cols) === 4) {
        $fechas[] = $cols[0];
        $tempsC[] = $cols[1];
        $tempsF[] = $cols[2];
        $hums[] = $cols[3];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Monitor de Temperatura</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: sans-serif; text-align: center; padding: 20px; }
    table { margin: auto; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 8px; }
    canvas { max-width: 900px; margin: 20px auto; }
  </style>
</head>
<body>
  <h1>Monitor Ambiental</h1>

  <?php if ($mensaje): ?>
    <p><strong><?= htmlspecialchars($mensaje) ?></strong></p>
  <?php endif; ?>

  <div>
    <a href="?descargar=1"><button>📥 Descargar CSV</button></a>
  </div>

  <form method="POST" style="margin: 20px;">
    <input type="hidden" name="accion" value="borrar">
    <label>Contraseña para borrar: </label>
    <input type="password" name="clave" required>
    <button type="submit">🗑️ Borrar historial</button>
  </form>

  <h2>Últimos 20 Registros</h2>
  <canvas id="grafico" width="900" height="400"></canvas>

  <table>
    <tr><th>Fecha</th><th>Temp (°C)</th><th>Temp (°F)</th><th>Humedad (%)</th></tr>
    <?php for ($i = 0; $i < count($fechas); $i++): ?>
      <tr>
        <td><?= htmlspecialchars($fechas[$i]) ?></td>
        <td><?= htmlspecialchars($tempsC[$i]) ?></td>
        <td><?= htmlspecialchars($tempsF[$i]) ?></td>
        <td><?= htmlspecialchars($hums[$i]) ?></td>
      </tr>
    <?php endfor; ?>
  </table>

  <script>
    const labels = <?= json_encode($fechas) ?>;
    const tempCData = <?= json_encode($tempsC) ?>;
    const tempFData = <?= json_encode($tempsF) ?>;
    const humData = <?= json_encode($hums) ?>;

    const ctx = document.getElementById('grafico').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Temp (°C)',
            data: tempCData,
            borderColor: 'red',
            fill: false
          },
          {
            label: 'Temp (°F)',
            data: tempFData,
            borderColor: 'orange',
            fill: false
          },
          {
            label: 'Humedad (%)',
            data: humData,
            borderColor: 'blue',
            fill: false
          }
        ]
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  </script>
</body>
</html>
