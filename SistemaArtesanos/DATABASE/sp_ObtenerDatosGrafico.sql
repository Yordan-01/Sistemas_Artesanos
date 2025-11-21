CREATE PROCEDURE sp_ObtenerDatosGrafico
AS
BEGIN
    SET NOCOUNT ON;

    -- Datos para el gráfico de ventas por producto (usar TODOS los datos disponibles)
    SELECT 
        producto,
        SUM(cantidad) as total_ventas
    FROM ventas 
    -- Se quitó el filtro de fecha: WHERE fecha >= DATEADD(MONTH, -6, GETDATE())
    GROUP BY producto
    ORDER BY total_ventas DESC;
END
GO