@echo off
setlocal enabledelayedexpansion
set DB=hotel_db
set USER=root
set MYSQL="J:\XAMPP\mysql\bin\mysql.exe"

echo =====================================
echo INICIANDO MIGRACION HOTEL
echo =====================================

REM Verificar conexión
%MYSQL% -h 127.0.0.1 -u %USER% -e "SELECT 1;" >nul 2>&1
IF ERRORLEVEL 1 (
    echo ERROR: MySQL no esta corriendo o no es accesible
    pause
    exit /b
)

REM Eliminar y crear BD
%MYSQL% -h 127.0.0.1 -u %USER% -e "DROP DATABASE IF EXISTS %DB%;"
%MYSQL% -h 127.0.0.1 -u %USER% -e "CREATE DATABASE %DB%;"

REM Ejecutar migracion
%MYSQL% -h 127.0.0.1 -u %USER% %DB% < migracion.sql

IF ERRORLEVEL 1 (
    echo ERROR en migracion.sql
    pause
    exit /b
)

echo Estructura creada correctamente

REM Insertar metodos de pago
%MYSQL% -h 127.0.0.1 -u %USER% %DB% -e "INSERT IGNORE INTO metodo_pago (nombre) VALUES ('Efectivo'),('Tarjeta'),('QR');"

echo Datos base insertados

REM =============================================
REM GENERAR PASSWORDS PARA LOS 3 USUARIOS
REM =============================================
php -r "file_put_contents('tmp_admin.txt',  password_hash('admin123',        PASSWORD_BCRYPT));"
php -r "file_put_contents('tmp_recep.txt',  password_hash('recepcion123',    PASSWORD_BCRYPT));"
php -r "file_put_contents('tmp_client.txt', password_hash('cliente123',      PASSWORD_BCRYPT));"

set /p ADMIN_PASS=<tmp_admin.txt
set /p RECEP_PASS=<tmp_recep.txt
set /p CLIENT_PASS=<tmp_client.txt

del tmp_admin.txt
del tmp_recep.txt
del tmp_client.txt

echo Passwords generados

REM =============================================
REM USUARIO ADMINISTRADOR
REM =============================================
%MYSQL% -h 127.0.0.1 -u %USER% %DB% -e "INSERT INTO usuario (codigo, nombre, paterno, materno, ci, telefono, email, password, estado) VALUES (UUID(),'Admin','Sistema','','00000000','65181436','admin@realplazahotel.com','!ADMIN_PASS!','Activo');"
IF ERRORLEVEL 1 ( echo ERROR al insertar admin & pause & exit /b )

%MYSQL% -h 127.0.0.1 -u %USER% %DB% -e "INSERT INTO usuario_rol (idUsuario, idRol) SELECT u.idUsuario, r.idRol FROM usuario u, rol r WHERE u.email='admin@realplazahotel.com' AND r.nombre='Administrador' LIMIT 1;"
IF ERRORLEVEL 1 ( echo ERROR al asignar rol admin & pause & exit /b )

REM Crear registro empleado para admin
%MYSQL% -h 127.0.0.1 -u %USER% %DB% -e "INSERT INTO empleado (codigo, cargo, fechaContratacion, salario, idUsuario_FK) SELECT UUID(), 'Administrador', CURDATE(), 5000.00, idUsuario FROM usuario WHERE email='admin@realplazahotel.com' LIMIT 1;"

echo Admin creado correctamente

REM =============================================
REM USUARIO RECEPCIONISTA
REM =============================================
%MYSQL% -h 127.0.0.1 -u %USER% %DB% -e "INSERT INTO usuario (codigo, nombre, paterno, materno, ci, telefono, email, password, estado) VALUES (UUID(),'Recepcion','Sistema','','12345678','71234567','recepcion@realplazahotel.com','!RECEP_PASS!','Activo');"
IF ERRORLEVEL 1 ( echo ERROR al insertar recepcionista & pause & exit /b )

%MYSQL% -h 127.0.0.1 -u %USER% %DB% -e "INSERT INTO usuario_rol (idUsuario, idRol) SELECT u.idUsuario, r.idRol FROM usuario u, rol r WHERE u.email='recepcion@realplazahotel.com' AND r.nombre='Recepcionista' LIMIT 1;"
IF ERRORLEVEL 1 ( echo ERROR al asignar rol recepcionista & pause & exit /b )

REM Crear registro empleado para recepcionista
%MYSQL% -h 127.0.0.1 -u %USER% %DB% -e "INSERT INTO empleado (codigo, cargo, fechaContratacion, salario, idUsuario_FK) SELECT UUID(), 'Recepcionista', CURDATE(), 3500.00, idUsuario FROM usuario WHERE email='recepcion@realplazahotel.com' LIMIT 1;"

echo Recepcionista creado correctamente

REM =============================================
REM USUARIO CLIENTE
REM =============================================
%MYSQL% -h 127.0.0.1 -u %USER% %DB% -e "INSERT INTO usuario (codigo, nombre, paterno, materno, ci, telefono, email, password, estado) VALUES (UUID(),'Cliente','Sistema','','87654321','79876543','cliente@realplazahotel.com','!CLIENT_PASS!','Activo');"
IF ERRORLEVEL 1 ( echo ERROR al insertar cliente & pause & exit /b )

%MYSQL% -h 127.0.0.1 -u %USER% %DB% -e "INSERT INTO usuario_rol (idUsuario, idRol) SELECT u.idUsuario, r.idRol FROM usuario u, rol r WHERE u.email='cliente@realplazahotel.com' AND r.nombre='Cliente' LIMIT 1;"
IF ERRORLEVEL 1 ( echo ERROR al asignar rol cliente & pause & exit /b )

echo Cliente creado correctamente

echo =====================================
echo MIGRACION COMPLETADA
echo =====================================
echo.
echo ADMINISTRADOR
echo   Email:    admin@realplazahotel.com
echo   Password: admin123
echo.
echo RECEPCIONISTA
echo   Email:    recepcion@realplazahotel.com
echo   Password: recepcion123
echo.
echo CLIENTE
echo   Email:    cliente@realplazahotel.com
echo   Password: cliente123
echo =====================================

pause