<?php
header('Content-Type: application/json');

require_once "conexion.php";

$input = json_decode(file_get_contents("php://input"), true);

$nombre   = $input['usuario'] ?? '';
$password = $input['password'] ?? '';
$rol      = 'Artesano';  

// Validación básica
if (empty($nombre) || empty($password)) {
    echo json_encode([
        "status" => "fail",
        "message" => "Todos los campos son obligatorios."
    ]);
    exit;
}

// ✅ VERIFICAR si el usuario ya existe
$sql_verificar = "{CALL sp_VerificarUsuario(?)}";
$params_verificar = [$nombre];

$stmt_verificar = sqlsrv_query($conn, $sql_verificar, $params_verificar);

if ($stmt_verificar === false) {
    echo json_encode([
        "status" => "error",
        "message" => "Error al validar usuario",
        "details" => sqlsrv_errors()
    ]);
    exit;
}

$row = sqlsrv_fetch_array($stmt_verificar, SQLSRV_FETCH_ASSOC);

// Verificar si el usuario ya existe
if ($row['existe'] > 0) {
    echo json_encode([
        "status" => "fail",
        "message" => "El nombre de usuario ya existe"
    ]);
    exit;
}

// ✅ REGISTRAR usuario con el SP existente
$sql_registro = "{CALL sp_RegistrarUsuario(?, ?, ?)}";
$params_registro = [$nombre, $password, $rol];

$stmt_registro = sqlsrv_query($conn, $sql_registro, $params_registro);

if ($stmt_registro === false) {
    echo json_encode([
        "status" => "error",
        "message" => "Error al registrar el usuario",
        "details" => sqlsrv_errors()
    ]);
    exit;
}

// Respuesta OK
echo json_encode([
    "status" => "success",
    "message" => "Registro exitoso. Ahora puede iniciar sesión."
]);

sqlsrv_close($conn);
?>
