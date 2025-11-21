USE DBArtesania;
GO

CREATE OR ALTER PROCEDURE sp_RegistrarUsuario
    @nombre VARCHAR(50),
    @contraseña VARCHAR(50),
    @rol VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;

    INSERT INTO usuario (nombre, contraseña, rol)
    VALUES (@nombre, @contraseña, @rol);
END;
GO