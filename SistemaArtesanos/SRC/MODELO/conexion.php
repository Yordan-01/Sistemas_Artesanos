<?php
// SRC/MODELO/conexion.php

$serverName = "UHYXE30220\MSSQLSERVER1";
$connectionOptions = [
    "Database" => "DBArtesania",
    "Uid" => "usuario1",
    "PWD" => "abc123",
    "CharacterSet" => "UTF-8"
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die(json_encode([
        "status" => "error",
        "message" => "Error en la conexión",
        "details" => sqlsrv_errors()
    ], JSON_PRETTY_PRINT));
}

return $conn;
