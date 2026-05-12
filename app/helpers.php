<?php
// Detectar la base URI automáticamente
if (!function_exists('base_uri')) {
    function base_uri(): string
    {
        $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $base = str_replace('/public', '', $dir);

        if ($base === '/' || $base === '\\') {
            $base = '';
        }

        return rtrim($base, '/');
    }
}

// Helper para recursos (CSS, JS, imágenes)
if (!function_exists('asset')) {
    function asset(string $path): string
    {
        if (php_sapi_name() === 'cli-server') {
            // En servidor embebido ya estamos dentro de /public
            return '/' . ltrim($path, '/');
        }
        // En Apache necesitamos incluir /public
        return base_uri() . '/public/' . ltrim($path, '/');
    }
}

// Helper para URLs de rutas
if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        if (php_sapi_name() === 'cli-server') {
            // En servidor embebido no hay subcarpeta
            return '/' . ltrim($path, '/');
        }
        // En Apache sí puede haber subcarpeta (/biblioteca-app)
        return base_uri() . '/' . ltrim($path, '/');
    }
}