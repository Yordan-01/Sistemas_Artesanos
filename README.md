SistemaArtesanos
Sistema web para artesanos y administradores que permite predecir demanda, visualizar datos, gestionar usuarios y subir registros de ventas. El proyecto sigue la arquitectura MVC (Modelo–Vista–Controlador) y utiliza PHP, JavaScript, SQL Server y archivos CSV como base de datos de entrada.

#📁 Estructura del Proyecto
#SistemaArtesanos
# ├── DATABASE/                # Scripts SQL y procedimientos almacenados
# ├── LIB/                     # Librerías externas (PHP u otras)
# ├── SRC/                     # Lógica principal (MVC)
# │   ├── CONTROL/             # Controladores en JavaScript
# │   └── MODELO/              # Modelos en PHP (consultas y lógica de negocio)
# ├── VISTA/                   # Interfaces de usuario (HTML/CSS)
# │   ├── ADMINISTRADOR/       # Módulos exclusivos del administrador
# │   ├── ARTESANO/            # Vistas para usuarios artesanos
# │   ├── INICIO SESION/       # Pantalla de login
# │   └── REGISTRARSE/         # Registro de usuarios
# ├── start_server.bat         # Script para iniciar el servidor local
# └── ventas.csv               # Archivo de ejemplo para cargas de ventas

🗄️ DATABASE

Contiene los scripts SQL necesarios para crear tablas y procedimientos almacenados que soportan:
Registro y validación de usuarios
Importación de ventas desde CSV
Predicción estadística o machine learning básico
Obtención de datos para gráficos y recomendaciones
Gestión de archivos subidos

Archivos principales:
Artesania.sql
sp_RegistrarUsuario.sql
sp_ValidarUsuario.sql
sp_ImportarCSVVentas.sql
sp_CalcularPrediccion.sql
sp_ObtenerDatosGrafico.sql
sp_ObtenerRecomendaciones.sql

Otros procedimientos auxiliares.
💻 SRC – Lógica de Negocio
CONTROL (JavaScript)
Controladores que manejan eventos del frontend:
registrarse.js — Manejo del formulario de registro
validarUsuario.js — Validación en login
usuarios.js — Gestión de usuarios (admin)
upload.js — Subida de archivos CSV
predicciones.js — Visualización de predicciones
visualizacion.js — Gráficos y dashboards

MODELO (PHP)
Modelos que interactúan con la base de datos:
conexion.php — Conexión SQL Server
registrarse.php, validarUsuario.php — Autenticación
usuarios.php — CRUD de usuarios
upload.php — Procesamiento de CSV
predicciones.php — Lógica de predicción
visualizacion.php — Datos para gráficos

🖥️ VISTA – Interfaz de Usuario
ADMINISTRADOR
Gestión de usuarios
usuarios.html, usuarios.css
Subir datos (CSV)
subir_datos.html, subir_datos.css
Dashboard administrativo
dashboard_admin.html, dashboard_admin.css

ARTESANO
Predicciones
predicciones.html, predicciones.css
Visualización de datos
visualizacion.html, visualizacion.css
Dashboard
dashboard.html, styles.css

INICIO DE SESIÓN
index.html, styles.css, inicio sesion.jpg

REGISTRARSE
Registrarse.html, styles_registrarse.css, registrarse.jpg

▶️ Ejecución del Sistema
Clonar el repositorio.
Configurar la base de datos usando los scripts dentro de DATABASE/.
Ajustar credenciales en:
SRC/MODELO/conexion.php
Ejecutar el servidor local usando:
start_server.bat

Acceder desde el navegador a:
http://localhost:8080/VISTA/INICIO%20SESION/index.html

📦 Requisitos
SQL Server
Navegador moderno

📊 Funcionalidades principales
Registro e inicio de sesión (roles: administrador y artesano)
Gestión de usuarios
Importación de ventas mediante CSV
Predicción de demanda
Gráficos de tendencias
Recomendaciones automáticas
Dashboards por rol

👥 Autores
- Chavez Yolgo Erick
- Contreras Ricra Jhon
- Paredes Cervantes Yordan
- Quintana Tumilán Renzo
