CREATE OR ALTER PROCEDURE sp_EliminarUsuario
    @id INT
AS
BEGIN
    DELETE FROM usuario
    WHERE idUsuario = @id;
END;
GO
