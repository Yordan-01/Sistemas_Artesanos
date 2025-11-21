<?php
header('Content-Type: application/json');

include_once 'conexion.php';

$action = $_GET['action'] ?? '';

try {
    if ($action === 'grafico') {
        // Llamar al procedimiento almacenado para el gráfico
        $query = "{CALL sp_ObtenerDatosGrafico}";
        $stmt = sqlsrv_query($conn, $query);

        if ($stmt) {
            $labels = [];
            $ventas = [];
            
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $labels[] = $row['producto'];
                $ventas[] = $row['total_ventas'];
            }

            echo json_encode([
                'labels' => $labels,
                'ventas' => $ventas
            ]);
        } else {
            $errors = sqlsrv_errors();
            echo json_encode(["error" => "Error en la consulta: " . $errors[0]['message']]);
        }

    } elseif ($action === 'recomendaciones') {
        // Llamar al procedimiento almacenado para recomendaciones
        $query = "{CALL sp_ObtenerRecomendaciones}";
        $stmt = sqlsrv_query($conn, $query);

        if ($stmt) {
            $recomendaciones = [];
            
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $recomendaciones[] = [
                    'producto' => $row['producto'],
                    'cantidad_recomendada' => $row['cantidad_recomendada'],
                    'motivo' => $row['motivo']
                ];
            }

            echo json_encode($recomendaciones);
        } else {
            $errors = sqlsrv_errors();
            echo json_encode(["error" => "Error en la consulta: " . $errors[0]['message']]);
        }

    } else {
        echo json_encode(["error" => "Acción no válida"]);
    }

} catch (Exception $e) {
    echo json_encode(["error" => "Error del sistema: " . $e->getMessage()]);
}

sqlsrv_close($conn);
?>