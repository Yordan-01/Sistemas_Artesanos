CREATE PROCEDURE dbo.sp_ImportarCSVVentas
    @rutaArchivo NVARCHAR(500), 
    @archivoId INT
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRANSACTION;

        -- 1) Limpiar staging simple
        TRUNCATE TABLE dbo.ventas_staging_simple;

        ---------------------------------------------------------
        -- 2) Cargar CSV en la tabla staging simple
        ---------------------------------------------------------
        DECLARE @sql NVARCHAR(MAX);

        SET @sql = N'
            BULK INSERT dbo.ventas_staging_simple
            FROM ''' + @rutaArchivo + N'''
            WITH (
                FIRSTROW = 2,
                FIELDTERMINATOR = '','',
                ROWTERMINATOR = ''\n'',
                CODEPAGE = ''65001'',
                TABLOCK
            );';

        EXEC sp_executesql @sql;

        ---------------------------------------------------------
        -- 3) Insertar en tabla final ya transformado
        ---------------------------------------------------------
        INSERT INTO dbo.ventas (archivo_id, fecha, producto, cantidad, precio, total)
        SELECT
            @archivoId,
            TRY_CONVERT(date, NULLIF(col_fecha,'')),
            col_producto,
            TRY_CONVERT(int, NULLIF(col_cantidad,'')),
            TRY_CONVERT(decimal(18,2), NULLIF(col_precio,'')),
            CASE 
                WHEN TRY_CONVERT(int, NULLIF(col_cantidad,'')) IS NOT NULL
                AND TRY_CONVERT(decimal(18,2), NULLIF(col_precio,'')) IS NOT NULL
                THEN TRY_CONVERT(int, NULLIF(col_cantidad,'')) * TRY_CONVERT(decimal(18,2), NULLIF(col_precio,''))
                ELSE NULL
            END
        FROM dbo.ventas_staging_simple;

        -- 4) Actualizar estado del archivo
        UPDATE dbo.archivos_subidos
        SET estado = 'Procesado', mensaje_error = NULL
        WHERE id = @archivoId;

        COMMIT TRANSACTION;
    END TRY
    BEGIN CATCH
        ROLLBACK TRANSACTION;

        DECLARE @errMsg NVARCHAR(MAX) = ERROR_MESSAGE();

        UPDATE dbo.archivos_subidos
        SET estado = 'Error', mensaje_error = @errMsg
        WHERE id = @archivoId;

        THROW;
    END CATCH
END
GO