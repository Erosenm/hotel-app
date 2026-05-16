<?php
function require_login()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['usuario'])) {
        header("Location: " . url('login'));
        exit();
    }
}

function require_admin()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['usuario'])) {
        header("Location: " . url('login'));
        exit();
    }
    if ($_SESSION['usuario']['rol'] !== 'Administrador') {
        header("Location: " . url('/'));
        exit();
    }
}

function require_recepcionista()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['usuario'])) {
        header("Location: " . url('login'));
        exit();
    }
    $rol = $_SESSION['usuario']['rol'];
    if (!in_array($rol, ['Administrador', 'Recepcionista', 'Gerente', 'Contador'])) {
        header("Location: " . url('/'));
        exit();
    }
}
function require_gerente()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['usuario'])) {
        header("Location: " . url('login'));
        exit();
    }
    $rol = $_SESSION['usuario']['rol'];
    if (!in_array($rol, ['Administrador', 'Gerente', 'Contador'])) {
        header("Location: " . url('/'));
        exit();
    }
}

function require_cliente()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['usuario'])) {
        header("Location: " . url('login'));
        exit();
    }
}