document.getElementById("formPrediccion").addEventListener("submit", function(e) {
    e.preventDefault();

    const producto = document.getElementById("producto").value;
    const periodo = document.getElementById("periodo").value;

    if (!producto || !periodo) return alert("Por favor seleccione todos los campos.");

    // Llamar al backend PHP
    fetch('../../../SRC/MODELO/predicciones.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `producto=${producto}&periodo=${periodo}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            return;
        }

        const tbody = document.getElementById("tablaPredicciones");
        tbody.innerHTML = `
            <tr>
                <td>${data.producto}</td>
                <td>${data.periodo_texto}</td>
                <td>${data.ventas_esperadas} unidades</td>
                <td>${data.tendencia}</td>
            </tr>
        `;
        document.getElementById("resultadoPrediccion").style.display = "block";
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al generar la predicción');
    });
});