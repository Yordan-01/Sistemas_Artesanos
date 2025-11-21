CREATE PROCEDURE sp_ObtenerRecomendaciones
AS
BEGIN
    SET NOCOUNT ON;

    WITH VentasPromedio AS (
        SELECT 
            producto,
            AVG(CAST(cantidad as FLOAT)) as promedio_ventas,
            COUNT(*) as total_ventas
        FROM ventas 
        -- Se quitó el filtro de fecha: WHERE fecha >= DATEADD(MONTH, -3, GETDATE())
        GROUP BY producto
    )
    SELECT 
        producto,
        ROUND(promedio_ventas * 1.2, 0) as cantidad_recomendada,
        CASE 
            WHEN promedio_ventas > 50 THEN 'Alta demanda historica'
            WHEN promedio_ventas > 25 THEN 'Demanda estable'
            ELSE 'Baja demanda actual'
        END as motivo
    FROM VentasPromedio
    ORDER BY promedio_ventas DESC;
END
GO