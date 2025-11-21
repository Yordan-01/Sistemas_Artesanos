--CREACIÓN BASE DE DATOS Y TABLAS
CREATE DATABASE DBArtesania
GO
USE DBArtesania;
GO
DROP TABLE IF EXISTS dbo.metricas_productos;
DROP TABLE IF EXISTS dbo.predicciones;
DROP TABLE IF EXISTS dbo.ventas_staging_simple;
DROP TABLE IF EXISTS dbo.archivos_subidos;
DROP TABLE IF EXISTS dbo.ventas;
DROP TABLE IF EXISTS dbo.usuario;
GO

CREATE TABLE dbo.usuario 
(
	idUsuario INT IDENTITY(1,1) PRIMARY KEY,
	nombre VARCHAR(50) NOT NULL,
	contraseña VARCHAR(50) NOT NULL,
	rol VARCHAR(50) NOT NULL
);
GO

CREATE TABLE dbo.ventas (
    id INT IDENTITY(1,1) PRIMARY KEY,
    archivo_id INT NULL,
    fecha DATE NULL,
    producto NVARCHAR(255) NULL,
    cantidad INT NULL,
    precio DECIMAL(18,2) NULL,
    total DECIMAL(18,2) NULL,
    fecha_creacion DATETIME2 NOT NULL DEFAULT SYSUTCDATETIME()
);
GO

CREATE TABLE dbo.archivos_subidos (
    id INT IDENTITY(1,1) PRIMARY KEY,
    nombre_archivo NVARCHAR(260) NOT NULL,
    ruta_archivo NVARCHAR(500) NULL,
    fecha_subida DATETIME2 NOT NULL DEFAULT SYSUTCDATETIME(),
    estado NVARCHAR(50) NOT NULL DEFAULT 'En revisión',
    mensaje_error NVARCHAR(MAX) NULL
);
GO

CREATE TABLE dbo.ventas_staging_simple (
    col_fecha NVARCHAR(50),
    col_producto NVARCHAR(255),
    col_cantidad NVARCHAR(50),
    col_precio NVARCHAR(50)
);	

CREATE TABLE dbo.predicciones (
    id INT IDENTITY(1,1) PRIMARY KEY,
    producto NVARCHAR(255) NOT NULL,
    periodo NVARCHAR(50) NOT NULL,
    ventas_esperadas INT NOT NULL,
    tendencia NVARCHAR(50) NOT NULL,
    fecha_prediccion DATETIME2 NOT NULL DEFAULT SYSUTCDATETIME(),
    usuario_id INT NULL
);
GO

CREATE TABLE dbo.metricas_productos (
    id INT IDENTITY(1,1) PRIMARY KEY,
    producto NVARCHAR(255) NOT NULL,
    cantidad_recomendada INT NOT NULL,
    motivo NVARCHAR(500) NOT NULL,
    fecha_actualizacion DATETIME2 NOT NULL DEFAULT SYSUTCDATETIME()
);
GO

--CREACIÓN LOGIN Y USER
CREATE LOGIN usuario1
WITH PASSWORD = 'abc123'

DROP USER IF EXISTS artesania
GO
CREATE USER artesania FOR LOGIN usuario1

EXEC sp_addrolemember 'db_owner', 'artesania'

--INSERCIÓN DE DATOS
INSERT INTO usuario (nombre, contraseña, rol) VALUES
('yordan','yordan123','Administrador'),
('renzo','renzo123','Administrador'),
('erick','erick123','Administrador'),
('jhon','jhon123','Administrador')

--PERMISOS REGISTRAR ARCHIVOS VENTAS
-- 1. Dar permiso de BULK ADMIN al usuario
EXEC sp_addsrvrolemember 'usuario1', 'bulkadmin';

-- 2. Dar permisos de lectura al path donde guardas el CSV
EXEC sp_configure 'show advanced options', 1;
RECONFIGURE;

SELECT service_account FROM sys.dm_server_services WHERE servicename LIKE 'SQL Server (%';

EXEC sp_configure 'xp_cmdshell', 1;
RECONFIGURE;

SELECT @@SERVICENAME;

-- PRUEBA DE EJECUCIÓN BULK INSERT EN LA CARPETA C:\uploads
-- Asegúrate de que la carpeta C:\uploads existe y tiene permisos para la cuenta del servicio SQL Server
BULK INSERT dbo.ventas_staging_simple
FROM 'C:\uploads\ventas.csv'
WITH (FIRSTROW = 2, FIELDTERMINATOR = ',', ROWTERMINATOR = '\n', CODEPAGE = '65001');
