async function cargarDatos() {
  const response = await fetch("datos.csv");
  const texto = await response.text();
  const lineas = texto.trim().split("\n").slice(1); // quitar encabezado

  const fechas = [];
  const temperaturas = [];
  const humedades = [];

  lineas.forEach(l => {
    const [fecha, temp, hum] = l.split(",");
    fechas.push(fecha);
    temperaturas.push(parseFloat(temp));
    humedades.push(parseFloat(hum));
  });

  return { fechas, temperaturas, humedades };
}

async function graficar() {
  const datos = await cargarDatos();
  const ctx = document.getElementById("grafico").getContext("2d");

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: datos.fechas,
      datasets: [
        {
          label: 'Temperatura (°C)',
          data: datos.temperaturas,
          borderColor: 'red',
          fill: false
        },
        {
          label: 'Humedad (%)',
          data: datos.humedades,
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
}

graficar();
