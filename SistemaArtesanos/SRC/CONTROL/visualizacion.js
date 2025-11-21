// Cargar datos al iniciar la página
document.addEventListener('DOMContentLoaded', function() {
    cargarGraficoVentas();
    cargarRecomendaciones();
});

function cargarGraficoVentas() {
    fetch('../../../SRC/MODELO/visualizacion.php?action=grafico')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }
            crearGraficoVentas(data);
        })
        .catch(error => console.error('Error:', error));
}

function cargarRecomendaciones() {
    fetch('../../../SRC/MODELO/visualizacion.php?action=recomendaciones')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }
            actualizarTablaRecomendaciones(data);
        })
        .catch(error => console.error('Error:', error));
}

function crearGraficoVentas(datos) {
    const ctx = document.getElementById('graficoVentas').getContext('2d');
    new Chart(ctx, {
        type: 'bar', // Cambié a gráfico de barras para mejor visualización
        data: {
            labels: datos.labels,
            datasets: [{
                label: 'Ventas por producto',
                data: datos.ventas,
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'
                ],
                borderColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: 'Distribución de Ventas'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Unidades Vendidas'
                    }
                }
            }
        }
    });
}

function actualizarTablaRecomendaciones(recomendaciones) {
    const tbody = document.querySelector('#tablaRecomendaciones tbody');
    tbody.innerHTML = recomendaciones.map(item => `
        <tr>
            <td>${item.producto}</td>
            <td>${item.cantidad_recomendada} unidades</td>
            <td>${item.motivo}</td>
        </tr>
    `).join('');
}