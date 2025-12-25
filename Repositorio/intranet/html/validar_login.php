<?php
/*
 * validar_login.php (Script de Lógica de Autenticación)
 *
 * Este script NO es una página web que se ve. Es un "backend".
 * Su único trabajo es:
 * 1. Iniciar el motor de sesiones de PHP.
 * 2. Simular una base de datos con usuarios y contraseñas válidos.
 * 3. Recoger los datos (usuario/clave) que el usuario escribió en 'login.php'.
 * 4. Comprobar si el usuario y la clave son correctos.
 * 5. Si SÍ son correctos:
 * a. "Recordar" al usuario guardando sus datos en la $_SESSION.
 * b. Redirigir al usuario al 'dashboard.php'.
 * 6. Si NO son correctos:
 * a. Redirigir al usuario de vuelta a 'login.php'.
 */

// --- 1. INICIAR EL SISTEMA DE SESIONES ---
// Es OBLIGATORIO llamar a session_start() al principio
// de cualquier script que vaya a *escribir* o *leer*
// la variable $_SESSION.
session_start();

// --- 2. SIMULACIÓN DE BASE DE DATOS (Usuarios Válidos) ---
// En un proyecto real, esto sería una consulta a una base de datos MySQL/PostgreSQL.
$usuarios_validos = [
    // 'nombre_de_usuario' => 'contraseña'
    'admin' => 'admin',  // Este usuario tiene rol de Administrador
    'emp1' => 'emp1',    // Este usuario tiene rol de Empleado
    'emp2' => 'emp2',    // Este usuario tiene rol de Empleado
    'emp3' => 'emp3'     // Este usuario tiene rol de Empleado
];

// --- 3. RECOGIDA DE DATOS DEL FORMULARIO ---
// $_POST es un array especial de PHP que contiene los datos
// enviados por un formulario con 'method="post"'.
// '$_POST['usuario']' -> coge el valor del campo con name="usuario".
// '$_POST['clave']' -> coge el valor del campo con name="clave".
//
// Usamos el "operador de fusión de null" (?? '') como atajo.
// Significa: "coge el valor de $_POST['usuario'], pero si no existe, usa '' (vacío)".
// Esto es una medida simple para evitar errores si alguien accede a este script directamente.
$usuario_form = $_POST['usuario'] ?? '';
$clave_form = $_POST['clave'] ?? '';

// --- 4. VALIDACIÓN DE CREDENCIALES ---
// Esta es la comprobación de seguridad principal.
//
// 'isset($usuarios_validos[$usuario_form])'
//   Comprueba si el usuario que escribió el usuario (ej. 'admin')
//   existe como "clave" en nuestro array de $usuarios_validos.
//   (Evita errores si el usuario no existe)
//
// '&&' significa "Y ADEMÁS"
//
// '$usuarios_validos[$usuario_form] == $clave_form'
//   Comprueba si la contraseña de ese usuario en nuestro array
//   (ej. $usuarios_validos['admin'] que es 'admin')
//   es EXACTAMENTE igual a la contraseña que escribió el usuario.
//
if (isset($usuarios_validos[$usuario_form]) && $usuarios_validos[$usuario_form] == $clave_form) {
    
    // --- 5. ¡ÉXITO! Usuario y contraseña correctos ---
    
    // "Recordamos" al usuario para las demás páginas.
    // Creamos una variable de sesión llamada 'usuario_logueado'
    // y le asignamos el nombre del usuario.
    // Esta variable $_SESSION estará disponible en TODAS las páginas
    // (siempre que hagamos session_start() al principio).
    $_SESSION['usuario_logueado'] = $usuario_form;
    
    // Asignamos un "rol" (permiso) a este usuario en la sesión.
    // Usamos un 'if' corto (ternario):
    // (Condición) ? (Si es verdad) : (Si es falso)
    // "Si el usuario es 'admin', dale el rol 'admin', si no, dale 'empleado'"
    $_SESSION['rol'] = ($usuario_form == 'admin') ? 'admin' : 'empleado';
    
    // Redirigimos al usuario al panel de administración
    // 'header('Location: ...')' envía una orden al navegador.
    header('Location: admin/dashboard.php');
    
    // 'exit' detiene la ejecución del script. Es una buena práctica
    // de seguridad para asegurar que la redirección ocurra inmediatamente.
    exit;

} else {
    // --- 6. ¡FALLO! Usuario o contraseña incorrectos ---
    
    // El usuario no existía o la contraseña era incorrecta.
    // Lo redirigimos de vuelta a la página de login para que lo intente de nuevo.
    header('Location: login.php'); // (Tu archivo se llama 'login.html', pero lo renombramos a 'login.php')
    exit;
}
