document.addEventListener("DOMContentLoaded", () => {
    cargarHistorial();
});

document.getElementById("formSubida").addEventListener("submit", function (e) {
    e.preventDefault();

    const archivo = document.getElementById("archivoCSV").files[0];
    const mensaje = document.getElementById("mensaje");
    
    if (!archivo) {
        mensaje.textContent = "Por favor selecciona un archivo";
        mensaje.style.color = "red";
        return;
    }

    const formData = new FormData();
    formData.append("archivo", archivo);

    // Mostrar mensaje de carga
    mensaje.textContent = "Subiendo archivo...";
    mensaje.style.color = "blue";

    fetch("/SRC/MODELO/upload.php", {
        method: "POST",
        body: formData
    })
    .then(response => {
        // Primero verificar si la respuesta es JSON válido
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('El servidor no retornó JSON válido');
        }
        return response.json();
    })
    .then(data => {
        console.log("Respuesta del servidor:", data);
        
        mensaje.textContent = data.mensaje;
        mensaje.style.color = data.ok ? "green" : "red";

        if (data.ok) {
            // Limpiar el formulario
            document.getElementById("formSubida").reset();
            // Recargar el historial
            cargarHistorial();
        }
    })
    .catch(error => {
        console.error("Error:", error);
        mensaje.textContent = "Error al comunicarse con el servidor";
        mensaje.style.color = "red";
    });
});

function cargarHistorial() {
    fetch("/SRC/MODELO/upload.php?listar=1")
        .then(response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('El servidor no retornó JSON válido');
            }
            return response.json();
        })
        .then(data => {
            const tabla = document.getElementById("tablaArchivos");
            tabla.innerHTML = "";

            data.forEach(row => {
                tabla.innerHTML += `
                    <tr>
                        <td>${row.id}</td>
                        <td>${row.nombre_archivo}</td>
                        <td>${row.fecha_subida}</td>
                        <td>${row.estado}</td>
                    </tr>`;
            });
        })
        .catch(error => {
            console.error("Error cargando historial:", error);
            const tabla = document.getElementById("tablaArchivos");
            tabla.innerHTML = `
                <tr>
                    <td colspan="4" style="text-align: center; color: red;">
                        Error al cargar el historial
                    </td>
                </tr>`;
        });
}