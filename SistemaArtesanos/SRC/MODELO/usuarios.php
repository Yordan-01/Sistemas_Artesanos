<?php
header("Content-Type: application/json");
require_once "conexion.php";

$input = json_decode(file_get_contents("php://input"), true);
$action = $input["action"] ?? "";

switch ($action) {

    case "registrar":
        registrarUsuario($input);
        break;

    case "listar":
        listarUsuarios();
        break;

    case "eliminar":
        eliminarUsuario($input);
        break;

    default:
        echo json_encode([
            "status" => "error",
            "message" => "Acción no válida"
        ]);
}

function registrarUsuario($input) {
    global $conn;

    $nombre = $input["nombre"] ?? "";
    $contraseña = $input["contraseña"] ?? "";
    $rol = $input["rol"] ?? "";

    if (!$nombre || !$contraseña) {
        echo json_encode(["status" => "fail", "message" => "Faltan datos"]);
        return;
    }

    // 🔍 VALIDACIÓN: verificar si el usuario ya existe
    $sqlCheck = "SELECT COUNT(*) AS total FROM usuario WHERE nombre = ?";
    $paramsCheck = [$nombre];
    $stmtCheck = sqlsrv_query($conn, $sqlCheck, $paramsCheck);

    if ($stmtCheck === false) {
        echo json_encode([
            "status" => "error",
            "message" => "Error verificando duplicados",
            "details" => sqlsrv_errors()
        ]);
        return;
    }

    $row = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);

    if ($row["total"] > 0) {
        echo json_encode([
            "status" => "fail",
            "message" => "El usuario ya está registrado, elija otro nombre"
        ]);
        return;
    }

    // ✅ REGISTRO: si no está duplicado, llamar a SP
    $sql = "{CALL sp_RegistrarUsuario(?, ?, ?)}";
    $params = [$nombre, $contraseña, $rol];

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "Error al registrar", "details" => sqlsrv_errors()]);
        return;
    }

    echo json_encode(["status" => "success", "message" => "Usuario registrado correctamente"]);
}

function listarUsuarios() {
    global $conn;

    $sql = "{CALL sp_ListarUsuarios()}";
    $stmt = sqlsrv_query($conn, $sql);

    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "Error al obtener datos"]);
        return;
    }

    $usuarios = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $usuarios[] = $row;
    }

    echo json_encode(["status" => "success", "data" => $usuarios]);
}

function eliminarUsuario($input) {
    global $conn;

    $id = $input["id"] ?? 0;

    if ($id == 0) {
        echo json_encode(["status" => "fail", "message" => "ID inválido"]);
        return;
    }

    $sql = "{CALL sp_EliminarUsuario(?)}";
    $params = [$id];

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "Error al eliminar"]);
        return;
    }

    echo json_encode(["status" => "success", "message" => "Usuario eliminado"]);
}

sqlsrv_close($conn);
?>
