<?php
return [
    ['method' => 'GET',  'path' => '/',                    'target' => 'HomeController@index'],
    ['method' => 'GET',  'path' => '/login',               'target' => 'AuthController@login'],
    ['method' => 'POST', 'path' => '/login',               'target' => 'AuthController@authenticate'],
    ['method' => 'GET',  'path' => '/logout',              'target' => 'AuthController@logout'],

    // Google OAuth
    ['method' => 'GET',  'path' => '/auth/google',          'target' => 'GoogleAuthController@redirect'],
    ['method' => 'GET',  'path' => '/auth/google/callback', 'target' => 'GoogleAuthController@callback'],

    // Recuperación de contraseña
    ['method' => 'GET',  'path' => '/password/forgot',    'target' => 'PasswordController@forgot'],
    ['method' => 'POST', 'path' => '/password/send-link', 'target' => 'PasswordController@sendLink'],
    ['method' => 'GET',  'path' => '/password/reset',     'target' => 'PasswordController@reset'],
    ['method' => 'POST', 'path' => '/password/update',    'target' => 'PasswordController@update'],
    ['method' => 'GET',  'path' => '/register',            'target' => 'RegisterController@register'],
    ['method' => 'POST', 'path' => '/register',            'target' => 'RegisterController@store'],
 
    // ── ADMIN ──────────────────────────────────────────────
    ['method' => 'GET',  'path' => '/adminpanel',          'target' => 'AdminPanelController@index'],
 
    // Usuarios
    ['method' => 'GET',  'path' => '/admin/usuarios',       'target' => 'UsuarioController@index'],
    ['method' => 'GET',  'path' => '/admin/usuarios/crear', 'target' => 'UsuarioController@create'],
    ['method' => 'POST', 'path' => '/admin/usuarios/crear', 'target' => 'UsuarioController@store'],
    ['method' => 'GET',  'path' => '/admin/usuarios/editar','target' => 'UsuarioController@edit'],
    ['method' => 'POST', 'path' => '/admin/usuarios/editar','target' => 'UsuarioController@update'],
    ['method' => 'GET',  'path' => '/admin/usuarios/estado','target' => 'UsuarioController@cambiarEstado'],
 
    // Habitaciones
    ['method' => 'GET',  'path' => '/admin/habitaciones',                 'target' => 'HabitacionController@index'],
    ['method' => 'GET',  'path' => '/admin/habitaciones/crear',           'target' => 'HabitacionController@create'],
    ['method' => 'POST', 'path' => '/admin/habitaciones/crear',           'target' => 'HabitacionController@store'],
    ['method' => 'GET',  'path' => '/admin/habitaciones/editar',          'target' => 'HabitacionController@edit'],
    ['method' => 'POST', 'path' => '/admin/habitaciones/editar',          'target' => 'HabitacionController@update'],
    ['method' => 'GET',  'path' => '/admin/habitaciones/imagen/eliminar', 'target' => 'HabitacionController@eliminarImagen'],
    ['method' => 'GET',  'path' => '/admin/habitaciones/estado',         'target' => 'HabitacionController@cambiarEstado'],
 
    // Reservas
    ['method' => 'GET',  'path' => '/admin/reservas',                  'target' => 'ReservaController@index'],
    ['method' => 'GET',  'path' => '/admin/reservas/crear',            'target' => 'ReservaController@create'],
    ['method' => 'POST', 'path' => '/admin/reservas/crear',            'target' => 'ReservaController@store'],
    ['method' => 'GET',  'path' => '/admin/reservas/buscar-cliente',   'target' => 'ReservaController@buscarCliente'],
    ['method' => 'GET',  'path' => '/admin/reservas/disponibilidad',   'target' => 'ReservaController@verificarDisponibilidad'],
    ['method' => 'GET',  'path' => '/admin/reservas/estado',           'target' => 'ReservaController@cambiarEstado'],
 
    // Productos
    ['method' => 'GET',  'path' => '/admin/productos',              'target' => 'ProductoController@index'],
    ['method' => 'GET',  'path' => '/admin/productos/crear',        'target' => 'ProductoController@create'],
    ['method' => 'POST', 'path' => '/admin/productos/crear',        'target' => 'ProductoController@store'],
    ['method' => 'GET',  'path' => '/admin/productos/editar',       'target' => 'ProductoController@edit'],
    ['method' => 'POST', 'path' => '/admin/productos/actualizar',   'target' => 'ProductoController@update'],
    ['method' => 'POST', 'path' => '/admin/productos/stock',        'target' => 'ProductoController@ajustarStock'],
    ['method' => 'GET',  'path' => '/admin/productos/eliminar',     'target' => 'ProductoController@delete'],

    // Calendario
    ['method' => 'GET',  'path' => '/admin/calendario',            'target' => 'CalendarioController@index'],

    // Limpieza
    ['method' => 'GET',  'path' => '/admin/limpieza',              'target' => 'LimpiezaController@index'],
    ['method' => 'POST', 'path' => '/admin/limpieza/crear',        'target' => 'LimpiezaController@crear'],
    ['method' => 'GET',  'path' => '/admin/limpieza/estado',       'target' => 'LimpiezaController@estado'],

    // Perfil admin
    ['method' => 'GET',  'path' => '/admin/perfil',                'target' => 'PerfilController@index'],
    ['method' => 'POST', 'path' => '/admin/perfil/actualizar',     'target' => 'PerfilController@actualizar'],
    ['method' => 'POST', 'path' => '/admin/perfil/password',       'target' => 'PerfilController@cambiarPassword'],

    // Reportes
    ['method' => 'GET',  'path' => '/admin/reportes',              'target' => 'ReporteController@index'],

    // Check-in / Check-out
    ['method' => 'GET',  'path' => '/admin/reservas/detalle',       'target' => 'ReservaController@detalle'],
    ['method' => 'GET',  'path' => '/admin/reservas/checkin',      'target' => 'ReservaController@checkin'],
    ['method' => 'GET',  'path' => '/admin/reservas/checkout',     'target' => 'ReservaController@checkout'],
    ['method' => 'POST', 'path' => '/admin/reservas/servicio',     'target' => 'ReservaController@agregarServicio'],

    // Categorías de productos
    ['method' => 'GET',  'path' => '/admin/categorias',              'target' => 'CategoriaController@index'],
    ['method' => 'POST', 'path' => '/admin/categorias/crear',        'target' => 'CategoriaController@store'],
    ['method' => 'POST', 'path' => '/admin/categorias/actualizar',   'target' => 'CategoriaController@update'],
    ['method' => 'GET',  'path' => '/admin/categorias/eliminar',     'target' => 'CategoriaController@delete'],

    // Servicios
    ['method' => 'GET',  'path' => '/admin/servicios',              'target' => 'ServicioController@index'],
    ['method' => 'GET',  'path' => '/admin/servicios/crear',        'target' => 'ServicioController@create'],
    ['method' => 'POST', 'path' => '/admin/servicios/crear',        'target' => 'ServicioController@store'],
    ['method' => 'GET',  'path' => '/admin/servicios/editar',       'target' => 'ServicioController@edit'],
    ['method' => 'POST', 'path' => '/admin/servicios/actualizar',   'target' => 'ServicioController@update'],
    ['method' => 'GET',  'path' => '/admin/servicios/eliminar',     'target' => 'ServicioController@delete'],

    // Pagos (admin)
    ['method' => 'GET',  'path' => '/admin/pagos',          'target' => 'PagoController@index'],
    ['method' => 'GET',  'path' => '/admin/pagos/crear',    'target' => 'PagoController@create'],
    ['method' => 'POST', 'path' => '/admin/pagos/crear',    'target' => 'PagoController@store'],
    ['method' => 'GET',  'path' => '/admin/pagos/estado',   'target' => 'PagoController@cambiarEstado'],
 
    // Bitácora
    ['method' => 'GET',  'path' => '/admin/bitacora',       'target' => 'BitacoraController@index'],
 
    // ── CLIENTE ────────────────────────────────────────────
    ['method' => 'GET',  'path' => '/habitaciones',               'target' => 'ClienteController@habitaciones'],
    ['method' => 'GET',  'path' => '/habitaciones/detalle',       'target' => 'ClienteController@detalleHabitacion'],
    ['method' => 'GET',  'path' => '/reservar',                   'target' => 'ClienteController@reservar'],
    ['method' => 'POST', 'path' => '/reservar',                   'target' => 'ClienteController@guardarReserva'],
    ['method' => 'GET',  'path' => '/cliente/dashboard',          'target' => 'ClienteController@dashboard'],
    ['method' => 'GET',  'path' => '/cliente/reservas',           'target' => 'ClienteController@misReservas'],
    ['method' => 'GET',  'path' => '/cliente/reservas/cancelar',  'target' => 'ClienteController@cancelarReserva'],
    ['method' => 'GET',  'path' => '/cliente/perfil',             'target' => 'ClienteController@perfil'],
    ['method' => 'POST', 'path' => '/cliente/perfil',             'target' => 'ClienteController@actualizarPerfil'],
 
    // Pagos (cliente)
    ['method' => 'GET',  'path' => '/cliente/pagar',              'target' => 'ClienteController@pagar'],
    ['method' => 'POST', 'path' => '/cliente/pagar',              'target' => 'ClienteController@procesarPago'],
    ['method' => 'GET',  'path' => '/cliente/pago/confirmacion',  'target' => 'ClienteController@confirmacionPago'],
    ['method' => 'GET',  'path' => '/cliente/reservas/detalle',    'target' => 'ClienteController@detalleReserva'],
    ['method' => 'POST', 'path' => '/cliente/reservas/servicio',   'target' => 'ClienteController@pedirServicio'],
];