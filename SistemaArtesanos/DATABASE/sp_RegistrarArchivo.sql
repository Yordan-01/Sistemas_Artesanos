CREATE PROCEDURE dbo.sp_RegistrarArchivo
    @nombre_archivo NVARCHAR(260),
    @ruta_archivo NVARCHAR(500) = NULL,
    @nuevo_id INT OUTPUT
AS
BEGIN
    SET NOCOUNT ON;

    INSERT INTO dbo.archivos_subidos (nombre_archivo, ruta_archivo)
    VALUES (@nombre_archivo, @ruta_archivo);

    SET @nuevo_id = SCOPE_IDENTITY();
END
GO
