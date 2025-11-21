<?php
header('Content-Type: application/json');

include_once 'conexion.php';

$producto = $_POST['producto'] ?? '';
$periodo = $_POST['periodo'] ?? '';

if (empty($producto) || empty($periodo)) {
    echo json_encode(["error" => "Datos incompletos"]);
    exit;
}

// Mapear productos a nombres reales en la BD
$productosMap = [
    'ceramica' => 'Ceramica decorativa',
    'textil' => 'Textil artesanal', 
    'joyeria' => 'Joyeria andina',
    'madera' => 'Tallado en madera'
];

$productoReal = $productosMap[$producto] ?? $producto;

// Mapear periodos a texto
$periodosMap = [
    '1m' => 'Próximo mes',
    '3m' => 'Próximos 3 meses',
    '6m' => 'Próximos 6 meses'
];

$periodoTexto = $periodosMap[$periodo] ?? $periodo;

try {
    // Llamar al procedimiento almacenado
    $query = "{CALL sp_CalcularPrediccion(?, ?)}";
    $params = array(
        array($productoReal, SQLSRV_PARAM_IN),
        array($periodo, SQLSRV_PARAM_IN)
    );

    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        
        if ($row && $row['producto'] !== null) {
            echo json_encode([
                'producto' => $row['producto'],
                'periodo_texto' => $periodoTexto,
                'ventas_esperadas' => $row['ventas_esperadas'],
                'tendencia' => $row['tendencia']
            ]);
        } else {
            echo json_encode(["error" => "No hay datos históricos para este producto"]);
        }
    } else {
        $errors = sqlsrv_errors();
        echo json_encode(["error" => "Error en la consulta: " . $errors[0]['message']]);
    }

} catch (Exception $e) {
    echo json_encode(["error" => "Error del sistema: " . $e->getMessage()]);
}

sqlsrv_close($conn);
?>