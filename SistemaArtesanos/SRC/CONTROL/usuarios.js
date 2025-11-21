document.addEventListener("DOMContentLoaded", () => {
    cargarUsuarios();

    const form = document.getElementById("formUsuario");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const nombre = document.getElementById("nombre").value;
        const contraseña = document.getElementById("password").value;
        const rol = document.getElementById("rol").value;

        const response = await fetch("../../../SRC/MODELO/usuarios.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: "registrar",
                nombre,
                contraseña,
                rol
            })
        });

        const result = await response.json();
        alert(result.message);

        if (result.status === "success") {
            form.reset();
            cargarUsuarios();
        }
    });
});

async function cargarUsuarios() {
    const tabla = document.getElementById("tablaUsuarios");
    tabla.innerHTML = "<tr><td colspan='5'>Cargando...</td></tr>";

    const response = await fetch("../../../SRC/MODELO/usuarios.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "listar" })
    });

    const result = await response.json();

    if (result.status === "success") {
        let html = "";
        result.data.forEach(u => {
            html += `
                <tr>
                    <td>${u.idUsuario}</td>
                    <td>${u.nombre}</td>
                    <td>${u.rol}</td>
                    <td>
                        <button class="delete-btn" onclick="eliminarUsuario(${u.idUsuario})">🗑️</button>
                    </td>
                </tr>
            `;
        });
        tabla.innerHTML = html;
    } else {
        tabla.innerHTML = "<tr><td colspan='5'>Error al cargar</td></tr>";
    }
}

async function eliminarUsuario(id) {
    if (!confirm("¿Eliminar este usuario?")) return;

    const response = await fetch("../../../SRC/MODELO/usuarios.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "eliminar", id })
    });

    const result = await response.json();
    alert(result.message);

    if (result.status === "success") {
        cargarUsuarios();
    }
}