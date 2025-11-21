// script_inicio_sesion

async function redirigir() {
    const usuario = document.getElementById("usuario").value.trim();
    const password = document.getElementById("password").value.trim();
    const rol = document.getElementById("rol").value;   

    const datos = { usuario, password, rol };

    try {
        const response = await fetch("../../SRC/MODELO/validarUsuario.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(datos)
        });

        const result = await response.json();

        if (result.status === "success") {
            alert(result.message);

            // Redirigir según el rol
            if (result.usuario.rol.toLowerCase() === "administrador") {
                window.location.href = "../../VISTA/ADMINISTRADOR/dashboard_admin.html";
            } else if (result.usuario.rol.toLowerCase() === "artesano") {
                window.location.href = "../../VISTA/ARTESANO/dashboard.html";
            }
        } else {
            alert("❌ " + result.message);
        }
    } catch (error) {
        console.error("Error en la conexión:", error);
        alert("Ocurrió un error al intentar iniciar sesión.");
    }
}