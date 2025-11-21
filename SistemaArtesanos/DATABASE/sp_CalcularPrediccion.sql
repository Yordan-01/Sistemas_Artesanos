CREATE PROCEDURE sp_CalcularPrediccion
    @producto NVARCHAR(255),
    @periodo NVARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @promedio_ventas FLOAT;
    DECLARE @total_registros INT;
    DECLARE @multiplicador INT;
    DECLARE @ventas_esperadas INT;
    DECLARE @tendencia NVARCHAR(50);

    -- Determinar multiplicador según periodo
    SET @multiplicador = CASE 
        WHEN @periodo = '3m' THEN 3
        WHEN @periodo = '6m' THEN 6
        ELSE 1
    END;

    -- Calcular promedio de ÚLTIMOS 12 MESES (balance entre datos recientes y suficientes)
    SELECT 
        @promedio_ventas = AVG(CAST(cantidad as FLOAT)),
        @total_registros = COUNT(*)
    FROM ventas 
    WHERE producto = @producto
    AND fecha >= DATEADD(MONTH, -12, GETDATE()); -- Últimos 12 meses

    -- Si no hay datos recientes, usar todos los datos disponibles
    IF @total_registros = 0
    BEGIN
        SELECT 
            @promedio_ventas = AVG(CAST(cantidad as FLOAT)),
            @total_registros = COUNT(*)
        FROM ventas 
        WHERE producto = @producto;
    END

    IF @total_registros > 0
    BEGIN
        SET @ventas_esperadas = ROUND(@promedio_ventas * @multiplicador, 0);
        
        -- Determinar tendencia
        SET @tendencia = CASE 
            WHEN @ventas_esperadas > 50 THEN 'Alta'
            WHEN @ventas_esperadas > 25 THEN 'Estable'
            ELSE 'Baja'
        END;

        SELECT 
            @producto as producto,
            @ventas_esperadas as ventas_esperadas,
            @tendencia as tendencia;
    END
    ELSE
    BEGIN
        SELECT 
            NULL as producto,
            NULL as ventas_esperadas,
            NULL as tendencia;
    END
END
GO