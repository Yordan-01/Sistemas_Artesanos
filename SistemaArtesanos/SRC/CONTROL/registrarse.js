// SRC/CONTROL/registrarse.js

async function registrarUsuario() {
    const usuario = document.getElementById("usuario").value.trim();
    const password = document.getElementById("password").value.trim();

    if (!usuario || !password) {
        alert("Por favor, complete todos los campos.");
        return;
    }

    const datos = { usuario, password };

    try {
        const response = await fetch("../../SRC/MODELO/registrarse.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(datos)
        });

        const result = await response.json();

        if (result.status === "success") {
            alert(result.message);
            window.location.href = "../../VISTA/INICIO%20SESION/index.html"; // Regresar al incio de sesión
        } else {
            alert("❌ " + result.message);
        }

    } catch (error) {
        console.error("Error al registrar:", error);
        alert("Ocurrió un error en el registro.");
    }
}
