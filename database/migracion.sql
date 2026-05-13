CREATE DATABASE IF NOT EXISTS hotel_db;
USE hotel_db;

-- =========================
-- USUARIOS
-- =========================

CREATE TABLE usuario (
    idUsuario INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    ci VARCHAR(15) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    paterno VARCHAR(50) NOT NULL,
    materno VARCHAR(50),
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    google_id VARCHAR(100) UNIQUE DEFAULT NULL,
    estado ENUM('Activo','Inactivo','Suspendido') DEFAULT 'Activo',
    fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE empleado (
    idEmpleado INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    cargo VARCHAR(50),
    fechaContratacion DATE,
    salario DECIMAL(10,2),
    idUsuario_FK INT UNIQUE,
    FOREIGN KEY (idUsuario_FK) REFERENCES usuario(idUsuario)
);

-- =========================
-- ROLES Y PERMISOS
-- =========================

CREATE TABLE rol (
    idRol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) UNIQUE
);

CREATE TABLE usuario_rol (
    idUsuario INT,
    idRol INT,
    PRIMARY KEY (idUsuario, idRol),
    FOREIGN KEY (idUsuario) REFERENCES usuario(idUsuario),
    FOREIGN KEY (idRol) REFERENCES rol(idRol)
);

-- =========================
-- HABITACIONES
-- =========================

CREATE TABLE tipo_habitacion (
    idTipoHabitacion INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    descripcion TEXT,
    precioBase DECIMAL(10,2)
);

CREATE TABLE estado_habitacion (
    idEstado INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50)
);

CREATE TABLE habitacion (
    idHabitacion INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(36) UNIQUE DEFAULT (UUID()),
    numero VARCHAR(10) UNIQUE,
    piso INT,
    idTipoHabitacion_FK INT,
    idEstadoHabitacion_FK INT,
    FOREIGN KEY (idTipoHabitacion_FK) REFERENCES tipo_habitacion(idTipoHabitacion),
    FOREIGN KEY (idEstadoHabitacion_FK) REFERENCES estado_habitacion(idEstado)
);

CREATE TABLE habitacion_imagen (
    idImagen INT AUTO_INCREMENT PRIMARY KEY,
    rutaImagen VARCHAR(255),
    idHabitacion_FK INT,
    FOREIGN KEY (idHabitacion_FK) REFERENCES habitacion(idHabitacion)
);

-- =========================
-- RESERVAS
-- =========================

CREATE TABLE estado_reserva (
    idEstado INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50)
);

CREATE TABLE reserva (
    idReserva INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(36) UNIQUE DEFAULT (UUID()),
    fechaInicio DATE,
    fechaFin DATE,
    cantidadPersonas INT,
    precioTotal DECIMAL(10,2),
    idEstadoReserva_FK INT,
    idUsuario_FK INT,
    idHabitacion_FK INT,
    idEmpleado_FK INT,
    FOREIGN KEY (idEstadoReserva_FK) REFERENCES estado_reserva(idEstado),
    FOREIGN KEY (idUsuario_FK) REFERENCES usuario(idUsuario),
    FOREIGN KEY (idHabitacion_FK) REFERENCES habitacion(idHabitacion),
    FOREIGN KEY (idEmpleado_FK) REFERENCES empleado(idEmpleado)
);

-- =========================
-- SERVICIOS
-- =========================

CREATE TABLE servicio (
    idServicio INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(36) UNIQUE DEFAULT (UUID()),
    nombre VARCHAR(100),
    precio DECIMAL(10,2)
);

CREATE TABLE reserva_servicio (
    idReserva INT,
    idServicio INT,
    cantidad INT DEFAULT 1,
    precioUnitario DECIMAL(10,2),
    PRIMARY KEY (idReserva, idServicio),
    FOREIGN KEY (idReserva) REFERENCES reserva(idReserva),
    FOREIGN KEY (idServicio) REFERENCES servicio(idServicio)
);

-- =========================
-- PRODUCTOS
-- =========================

CREATE TABLE categoria_producto (
    idCategoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT
);

CREATE TABLE producto (
    idProducto INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(36) UNIQUE DEFAULT (UUID()),
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    stockMinimo INT DEFAULT 5,
    unidad VARCHAR(20) DEFAULT 'unidad',
    estado ENUM('Activo','Inactivo') DEFAULT 'Activo',
    idCategoria_FK INT,
    fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idCategoria_FK) REFERENCES categoria_producto(idCategoria)
);

CREATE TABLE reserva_producto (
    idReservaProducto INT AUTO_INCREMENT PRIMARY KEY,
    idReserva INT NOT NULL,
    idProducto INT NOT NULL,
    cantidad INT DEFAULT 1,
    precioUnitario DECIMAL(10,2) NOT NULL,
    fechaConsumo DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idReserva) REFERENCES reserva(idReserva),
    FOREIGN KEY (idProducto) REFERENCES producto(idProducto)
);

-- =========================
-- PAGOS
-- =========================

CREATE TABLE metodo_pago (
    idMetodoPago INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50)
);

CREATE TABLE estado_pago (
    idEstado INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50)
);

CREATE TABLE pago (
    idPago INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(36) UNIQUE DEFAULT (UUID()),
    monto DECIMAL(10,2),
    fechaPago DATETIME DEFAULT CURRENT_TIMESTAMP,
    idEstadoPago_FK INT,
    idReserva_FK INT,
    idMetodoPago_FK INT,
    idEmpleado_FK INT,
    FOREIGN KEY (idEstadoPago_FK) REFERENCES estado_pago(idEstado),
    FOREIGN KEY (idReserva_FK) REFERENCES reserva(idReserva),
    FOREIGN KEY (idMetodoPago_FK) REFERENCES metodo_pago(idMetodoPago),
    FOREIGN KEY (idEmpleado_FK) REFERENCES empleado(idEmpleado)
);

CREATE TABLE recibo (
    idRecibo INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(36) UNIQUE DEFAULT (UUID()),
    numero VARCHAR(20) UNIQUE,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2),
    idPago_FK INT,
    FOREIGN KEY (idPago_FK) REFERENCES pago(idPago)
);

-- =========================
-- BITÁCORA
-- =========================

CREATE TABLE bitacora (
    idBitacora INT AUTO_INCREMENT PRIMARY KEY,
    accion TEXT,
    fechaHora DATETIME DEFAULT CURRENT_TIMESTAMP,
    idUsuario_FK INT,
    FOREIGN KEY (idUsuario_FK) REFERENCES usuario(idUsuario)
);

-- =========================
-- RECUPERACIÓN DE CONTRASEÑA
-- =========================

CREATE TABLE password_reset (
    idReset INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expira DATETIME NOT NULL,
    usado TINYINT(1) DEFAULT 0,
    fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_email (email)
);

-- =========================
-- IA
-- =========================

CREATE TABLE ia_log (
    idLog INT AUTO_INCREMENT PRIMARY KEY,
    idUsuario_FK INT,
    idReserva_FK INT,
    accion VARCHAR(50),
    descripcion TEXT,
    datosEntrada JSON,
    datosSalida JSON,
    estado VARCHAR(20),
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUsuario_FK) REFERENCES usuario(idUsuario),
    FOREIGN KEY (idReserva_FK) REFERENCES reserva(idReserva)
);

CREATE TABLE ia_reserva (
    idIaReserva INT AUTO_INCREMENT PRIMARY KEY,
    idReserva_FK INT,
    origen VARCHAR(20),
    confianza DECIMAL(5,2),
    FOREIGN KEY (idReserva_FK) REFERENCES reserva(idReserva)
);

CREATE TABLE ia_mensaje (
    idMensaje INT AUTO_INCREMENT PRIMARY KEY,
    idUsuario_FK INT,
    mensajeUsuario TEXT,
    respuestaIA TEXT,
    contexto JSON,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUsuario_FK) REFERENCES usuario(idUsuario)
);

-- =========================
-- INSERTS
-- =========================

INSERT IGNORE INTO rol (nombre) VALUES
('Administrador'),
('Recepcionista'),
('Cliente');

INSERT IGNORE INTO estado_pago (nombre) VALUES
('Pendiente'),
('Pagado'),
('Cancelado'),
('Reembolsado'),
('Parcial');

INSERT IGNORE INTO estado_habitacion (nombre) VALUES
('Disponible'),
('Ocupada'),
('Reservada'),
('Mantenimiento'),
('Limpieza');

INSERT IGNORE INTO estado_reserva (nombre) VALUES
('Pendiente'),
('Confirmada'),
('Cancelada'),
('Completada'),
('No show');

INSERT IGNORE INTO tipo_habitacion (nombre, descripcion, precioBase) VALUES
('Simple',      'Habitación individual con cama simple, baño privado y TV.',        150.00),
('Doble',       'Habitación con cama doble o dos camas, baño privado y TV.',        250.00),
('Suite',       'Suite de lujo con sala de estar, jacuzzi y vista panorámica.',     500.00),
('Triple',      'Habitación amplia con tres camas, ideal para familias.',           350.00),
('Matrimonial', 'Habitación romántica con cama king size y decoración especial.',   300.00);

INSERT IGNORE INTO categoria_producto (nombre, descripcion) VALUES
('Bebidas',         'Agua, jugos, refrescos y bebidas alcohólicas'),
('Alimentacion',    'Snacks, desayunos y comidas al cuarto'),
('Spa & Relax',     'Masajes, tratamientos y servicios de bienestar'),
('Lavanderia',      'Lavado, planchado y limpieza de ropa'),
('Transporte',      'Traslados, taxi y alquiler de vehículos'),
('Entretenimiento', 'Películas, tours y actividades recreativas');

INSERT IGNORE INTO producto (nombre, descripcion, precio, stock, stockMinimo, unidad, idCategoria_FK) VALUES
-- Bebidas
('Agua mineral 500ml',    'Agua mineral natural',                   8.00, 100, 20, 'botella', 1),
('Jugo natural',          'Jugo de fruta fresca del día',          15.00,  50, 10, 'vaso',    1),
('Refresco lata',         'Coca-Cola, Sprite o Fanta',             12.00,  80, 15, 'lata',    1),
('Cerveza nacional',      'Cerveza fría 350ml',                    20.00,  60, 10, 'botella', 1),
('Vino tinto copa',       'Copa de vino tinto de la casa',         35.00,  30,  5, 'copa',    1),
-- Alimentación
('Desayuno continental',  'Café, jugo, tostadas y fruta',          45.00,   0,  0, 'porción', 2),
('Sandwich club',         'Sandwich de pollo con papas fritas',    55.00,   0,  0, 'porción', 2),
('Tabla de quesos',       'Selección de quesos y embutidos',       70.00,   0,  0, 'porción', 2),
('Snack mix',             'Mix de maní, galletas y frutos secos',  25.00,  40, 10, 'bolsa',   2),
-- Spa & Relax
('Masaje relajante 60min','Masaje corporal completo',             200.00,   0,  0, 'sesión',  3),
('Masaje de pies 30min',  'Reflexología y masaje podal',           80.00,   0,  0, 'sesión',  3),
('Aromaterapia',          'Sesión de aromaterapia 45 min',        150.00,   0,  0, 'sesión',  3),
-- Lavandería
('Lavado ropa casual',    'Lavado y secado por prenda',            15.00,   0,  0, 'prenda',  4),
('Planchado',             'Planchado por prenda',                  10.00,   0,  0, 'prenda',  4),
('Lavado traje/vestido',  'Lavado en seco traje formal',           50.00,   0,  0, 'prenda',  4),
-- Transporte
('Traslado aeropuerto',   'Traslado ida o vuelta al aeropuerto',  120.00,   0,  0, 'viaje',   5);

-- =========================
-- INDICES
-- =========================

-- =========================
-- PARTICIONES
-- =========================

-- =========================
-- FUNCIONES
-- =========================

-- =========================
-- PROCEDIMIENTOS
-- =========================

-- =========================
-- TRIGGERS
-- =========================