<?php
/*
 * logout.php (Script de Cierre de Sesión)
 *
 * Este script NO es una página web que se ve. No tiene HTML.
 * Su único trabajo es:
 * 1. Iniciar la sesión actual del usuario.
 * 2. Borrar todos los datos de esa sesión.
 * 3. Destruir la sesión.
 * 4. Redirigir al usuario de vuelta a la página de inicio.
 */

// --- 1. INICIAR LA SESIÓN ---
// Es OBLIGATORIO llamar a session_start() primero.
session_start();

// --- 2. VACIAR LAS VARIABLES DE SESIÓN ---
// 'session_unset()' borra inmediatamente todas las variables
// guardadas dentro de la sesión.
session_unset();

// --- 3. DESTRUIR LA SESIÓN ---
// 'session_destroy()' elimina la sesión del servidor.
// Esto invalida el ID de sesión (la cookie PHPSESSID)
// que el navegador del usuario tenía.
// Este es el paso final y más importante del logout.
session_destroy();

// --- 4. REDIRIGIR AL USUARIO ---
// 'header('Location: ...')' envía una orden al navegador
// para que cargue la página 'index.php'.
// Como la sesión ya está destruida, cuando 'index.php' cargue,
// su 'if (isset($_SESSION['usuario_logueado']))' dará "falso"
// y mostrará correctamente el botón de "Iniciar Sesión".
header('Location: index.php');

// 'exit' es una buena práctica de seguridad.
// Detiene la ejecución del script inmediatamente,
// asegurando que no se ejecute ningún otro código PHP
// (si lo hubiera) después de la redirección.
exit;
?>
