USE DBArtesania;
GO

CREATE OR ALTER PROCEDURE sp_VerificarUsuario
    @nombre VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;
    
    -- Solo verifica si el usuario existe
    SELECT COUNT(*) AS existe
    FROM usuario
    WHERE nombre = @nombre;
END;    
GO