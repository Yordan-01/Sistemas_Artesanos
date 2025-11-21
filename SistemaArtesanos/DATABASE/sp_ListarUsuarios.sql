CREATE OR ALTER PROCEDURE sp_ListarUsuarios
AS
BEGIN
    SELECT idUsuario, nombre, contraseña, rol
    FROM usuario;
END;
GO
