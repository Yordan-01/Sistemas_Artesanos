### Prerrequisitos
- **Windows 10/11**
- **SQL Server 2019+** (Express o Developer)
- **ODBC Driver 17 for SQL Server**
- **Navegador moderno** (Chrome, Firefox, Edge)

### 1. Iniciar el Servidor Web
# Iniciar start_server.bat
# Pegar en el buscador http://localhost:8080/VISTA/INICIO%20SESION/index.html

-- 1. Crear base de datos
CREATE DATABASE DBArtesania;

-- 2. Ejecutar scripts en orden desde carpeta DATABASE/
-- Artesania.sql (tablas base)
-- sp_RegistrarUsuario.sql
-- sp_ValidarUsuario.sql
-- sp_RegistrarArchivo.sql
-- sp_ImportarCSVVentas.sql
-- sp_CalcularPrediccion.sql
-- sp_ObtenerDatosGrafico.sql
-- sp_ObtenerRecomendaciones.sql

-- 3. Crear usuarios de prueba
EXEC sp_RegistrarUsuario 'admin', 'admin123', 'Administrador';
EXEC sp_RegistrarUsuario 'artesano', 'artesano123', 'Artesano';

# Cambiar la instancia por la de SQL SERVER, $serverName = "NOMBRE_SERVIDOR\\INSTANCIA";
