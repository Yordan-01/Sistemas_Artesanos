<?php
header('Content-Type: application/json');

// Importamos la conexión
require_once "conexion.php";   // <= IMPORTANTE

// Recibir datos desde JavaScript (POST)
$input = json_decode(file_get_contents("php://input"), true);

$nombre    = $input['usuario'];
$password  = $input['password'];
$rol       = $input['rol'];

// Ejecutar procedimiento almacenado
$sql = "{CALL sp_ValidarUsuario(?, ?, ?)}";
$params = [$nombre, $password, $rol];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo json_encode([
        "status" => "error",
        "message" => "Error en la consulta",
        "details" => sqlsrv_errors()
    ]);
    exit;
}

if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo json_encode([
        "status"  => "success",
        "message" => "Inicio de sesión exitoso",
        "usuario" => $row
    ]);
} else {
    echo json_encode([
        "status" => "fail",
        "message" => "Usuario o contraseña incorrectos"
    ]);
}

// Cerrar conexión
sqlsrv_close($conn);
?>
