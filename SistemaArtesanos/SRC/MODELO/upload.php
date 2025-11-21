<?php
header("Content-Type: application/json");

// Manejar errores de PHP
ini_set('display_errors', 0);
error_reporting(0);

try {
    // Conexión SQL Server
    $conn = require __DIR__ . "/conexion.php";
    
    if (!$conn) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // 1) Listar historial
    if (isset($_GET["listar"])) {
        $sql = "SELECT id, nombre_archivo, fecha_subida, estado 
                FROM archivos_subidos 
                ORDER BY id DESC";

        $stmt = sqlsrv_query($conn, $sql);
        
        if ($stmt === false) {
            throw new Exception("Error en consulta: " . print_r(sqlsrv_errors(), true));
        }

        $rows = [];
        while ($r = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            if ($r["fecha_subida"] instanceof DateTime) {
                $r["fecha_subida"] = $r["fecha_subida"]->format("Y-m-d H:i");
            }
            $rows[] = $r;
        }

        echo json_encode($rows);
        exit;
    }

    // 2) Subida real del archivo
    if (!isset($_FILES["archivo"])) {
        echo json_encode(["ok" => false, "mensaje" => "Archivo no recibido"]);
        exit;
    }

    $archivo = $_FILES["archivo"];
    $nombreOriginal = $archivo["name"];

    // Validar tipo de archivo
    if (pathinfo($nombreOriginal, PATHINFO_EXTENSION) !== 'csv') {
        echo json_encode(["ok" => false, "mensaje" => "Solo se permiten archivos CSV"]);
        exit;
    }

    // RUTA FIJA QUE SQL SERVER ACEPTA
    $carpeta = "C:/uploads/";

    if (!is_dir($carpeta)) {
        if (!mkdir($carpeta, 0777, true)) {
            throw new Exception("No se pudo crear el directorio de uploads");
        }
    }

    // ✅ GUARDAR CON NOMBRE FIJO "ventas.csv" para que BULK INSERT lo encuentre
    $rutaFinal = $carpeta . "ventas.csv";

    // Si ya existe, borrarlo primero
    if (file_exists($rutaFinal)) {
        unlink($rutaFinal);
    }

    // Mover archivo subido
    if (!move_uploaded_file($archivo["tmp_name"], $rutaFinal)) {
        echo json_encode(["ok" => false, "mensaje" => "No se pudo guardar el archivo en el servidor"]);
        exit;
    }

    // Registrar archivo en BD
    $sql = "{CALL sp_RegistrarArchivo(?, ?, ?)}";
    $archivoId = 0;

    $params = [
        [$nombreOriginal, SQLSRV_PARAM_IN],
        [$rutaFinal, SQLSRV_PARAM_IN],
        [&$archivoId, SQLSRV_PARAM_OUT]
    ];

    $result = sqlsrv_query($conn, $sql, $params);

    if (!$result) {
        $errors = sqlsrv_errors();
        throw new Exception("Error al registrar archivo: " . print_r($errors, true));
    }

    // Importar CSV con BULK INSERT dentro del SP
    $sql = "{CALL sp_ImportarCSVVentas(?, ?)}";
    $params = [
        [$rutaFinal, SQLSRV_PARAM_IN],
        [$archivoId, SQLSRV_PARAM_IN]
    ];

    $import = sqlsrv_query($conn, $sql, $params);

    if ($import) {
        echo json_encode(["ok" => true, "mensaje" => "Archivo importado correctamente"]);
    } else {
        $errors = sqlsrv_errors();
        echo json_encode(["ok" => false, "mensaje" => "Error al procesar CSV: " . print_r($errors, true)]);
    }

} catch (Exception $e) {
    // Log del error (opcional)
    error_log("Error en upload.php: " . $e->getMessage());
    
    // Respuesta de error limpia
    echo json_encode([
        "ok" => false, 
        "mensaje" => "Error interno del servidor"
    ]);
}

// Cerrar conexión si existe
if (isset($conn)) {
    sqlsrv_close($conn);
}
?>