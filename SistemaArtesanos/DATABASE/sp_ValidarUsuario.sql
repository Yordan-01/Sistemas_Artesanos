USE DBArtesania;
GO

CREATE OR ALTER PROCEDURE sp_ValidarUsuario
    @nombre VARCHAR(50),
    @contraseña VARCHAR(50),
    @rol VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;

    SELECT idUsuario, nombre, rol
    FROM usuario
    WHERE nombre = @nombre 
    AND contraseña = @contraseña 
    AND rol = @rol;
END;    
GO