<?php
date_default_timezone_set("America/Tijuana");
$archivo = "datos.csv";

// Manejar descarga del archivo CSV
if (isset($_GET['descargar']) && file_exists($archivo)) {
    header("Content-Disposition: attachment; filename=datos.csv");
    header("Content-Type: text/csv");
    readfile($archivo);
    exit;
}

// Manejar solicitud de eliminación
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'borrar') {
    $clave_correcta = "clave123"; // 🔐 Cambia esto a tu contraseña
    if ($_POST['clave'] === $clave_correcta) {
        file_put_contents($archivo, "fecha,temp,humedad\n");
        $mensaje = "Historial borrado exitosamente.";
    } else {
        $mensaje = "Contraseña incorrecta.";
    }
}

// Leer datos para gráfica y tabla
$fechas = $temps = $hums = [];
$lineas = file_exists($archivo) ? file($archivo) : [];
$total = count($lineas);
for ($i = max(1, $total - 20); $i < $total; $i++) {
    $cols = str_getcsv($lineas[$i]);
    if (count($cols) == 3) {
        $fechas[] = $cols[0];
        $temps[] = $cols[1];
        $hums[] = $cols[2];
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
    canvas { max-width: 800px; margin: 20px auto; }
  </style>
</head>
<body>
  <h1>Monitor Ambiental</h1>

  <?php if ($mensaje): ?>
    <p><strong><?= $mensaje ?></strong></p>
  <?php endif; ?>

  <div>
    <a href="?descargar=1"><button>📥 Descargar CSV</button></a>
  </div>

  <form method="POST" style="margin: 20px;">
    <input type="hidden" name="accion" value="borrar">
    <label>🔐 Contraseña para borrar historial: </label>
    <input type="password" name="clave" required>
    <button type="submit">🗑️ Borrar historial</button>
  </form>

  <h2>Últimos 20 Registros</h2>
  <canvas id="grafico" width="800" height="400"></canvas>

  <table>
    <tr><th>Fecha</th><th>Temperatura (°C)</th><th>Humedad (%)</th></tr>
    <?php for ($i = 0; $i < count($fechas); $i++): ?>
      <tr>
        <td><?= htmlspecialchars($fechas[$i]) ?></td>
        <td><?= htmlspecialchars($temps[$i]) ?></td>
        <td><?= htmlspecialchars($hums[$i]) ?></td>
      </tr>
    <?php endfor; ?>
  </table>

  <script>
    const labels = <?= json_encode($fechas) ?>;
    const tempData = <?= json_encode($temps) ?>;
    const humData = <?= json_encode($hums) ?>;

    const ctx = document.getElementById('grafico').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Temperatura (°C)',
            data: tempData,
            borderColor: 'red',
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
