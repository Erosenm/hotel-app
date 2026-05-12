#!/bin/bash

DB="hotel_db"
USER="root"
SOCKET="/opt/lampp/var/mysql/mysql.sock"

# Generar password en bcrypt
ADMIN_PASS=$(php -r "echo password_hash('admin123', PASSWORD_BCRYPT);")

# Ejecutar SQL
mysql -u $USER --socket=$SOCKET < migracion.sql

# Insertar usuario administrador
mysql -u $USER --socket=$SOCKET $DB -e "
INSERT INTO usuarios (codigo, nombre, paterno, materno, ci, email, password)
VALUES ('ADM001', 'Admin', 'Sistema', '', '0000', 'admin@biblioteca.com', '$ADMIN_PASS');
"

mysql -u $USER --socket=$SOCKET $DB -e "
INSERT INTO roles_permiso (usuarios_idusuarios, roles_idroles)
VALUES (1, 1);
"

echo "Migración completa. Usuario administrador creado con contraseña: admin123"
