<?php
header('Content-Type: application/json');

// Importar la conexión
require_once "conexion.php";  // <=== AQUÍ USAMOS EL PHP DE CONEXIÓN

// Recibir datos JSON
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

// Verificar si el usuario ya existe
$sql_check = "SELECT COUNT(*) AS total FROM usuario WHERE nombre = ?";
$params_check = [$nombre];
$stmt_check = sqlsrv_query($conn, $sql_check, $params_check);

if ($stmt_check === false) {
    echo json_encode([
        "status" => "error",
        "message" => "Error al validar usuario",
        "details" => sqlsrv_errors()
    ]);
    exit;
}

$row = sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC);

if ($row['total'] > 0) {
    echo json_encode([
        "status" => "fail",
        "message" => "El nombre de usuario ya existe"
    ]);
    exit;
}

// Registrar usuario con el SP
$sql = "{CALL sp_RegistrarUsuario(?, ?, ?)}";
$params = [$nombre, $password, $rol];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
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

// Cerrar conexión
sqlsrv_close($conn);
?>
