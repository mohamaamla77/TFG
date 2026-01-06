<?php
/*
 * admin/dashboard.php (Panel de Administración)
 *
 * Esta es la página principal y privada del administrador.
 * Es el archivo más complejo porque combina 4 tareas:
 * 1. Seguridad (Comprobar quién eres).
 * 2. Monitorización (Ver el estado del servidor).
 * 3. Lectura de datos (Ver solicitudes).
 * 4. Escritura de datos (Publicar noticias).
 */

// --- 1. INICIAR LA SESIÓN ---
session_start();

// --- 2. MEDIDA DE SEGURIDAD 1: ¿ESTÁ LOGUEADO? ---
// 'isset' comprueba si la variable $_SESSION['usuario_logueado'] existe.
if (!isset($_SESSION['usuario_logueado'])) {
    
    // Si no ha iniciado sesión, lo "expulsamos" a la página de login.
    // 'header('Location: ...')' redirige al navegador.
    // Usamos '../login.php' porque tenemos que "subir un nivel"
    // desde la carpeta /admin/ para encontrar 'login.php' en la raíz.
    header('Location: ../login.php');
    
    // 'exit' detiene la ejecución del script. Es crucial después de una redirección.
    exit;
}

// --- 3. MEDIDA DE SEGURIDAD 2: ¿TIENE PERMISOS DE ADMIN? ---
// Comprobamos la variable 'rol' que creamos en 'validar_login.php'.
// Si el rol NO ES (!=) 'admin'...
if ($_SESSION['rol'] != 'admin') {
    
    // El usuario está logueado, pero es un "empleado" normal.
    // Le mostramos un mensaje de error y detenemos el script.
    echo "<h1>Acceso Denegado</h1><p>No tienes permisos de administrador.</p>";
    echo "<a href='../index.php'>Volver al inicio</a>";
    exit;
}

// --- SI EL SCRIPT LLEGA HASTA AQUÍ, SIGNIFICA QUE EL USUARIO ES EL ADMIN ---
// --- 4. LÓGICA DE MONITORIZACIÓN DEL SERVIDOR ---

/*
 * 'shell_exec' es una función de PHP que ejecuta un comando
 * directamente en la terminal del servidor Linux.
 *
 * ¿Por qué 'sudo'?
 * Porque el usuario de Apache ('www-data') no tiene permisos
 * para ver el estado de los servicios.
 *
 * ¿Cómo funciona?
 * 1. En el servidor Linux, se ha ejecutado 'sudo visudo'.
 * 2. Al final de ese archivo, se ha añadido esta línea:
 * www-data ALL=(ALL) NOPASSWD: /usr/bin/systemctl is-active apache2, /usr/bin/systemctl is-active wg-quick@wg0
 * 3. Esto le da permiso a Apache para ejecutar *solo* esos dos
 * comandos de forma segura y sin pedir contraseña.
 */

// 4A. Comprobar el estado de Apache
$apache_raw = shell_exec('sudo systemctl is-active apache2');
// 'trim()' limpia la respuesta (quita saltos de línea).
// Usamos un 'if' corto (ternario):
// (condición) ? (si es verdad) : (si es falso)
$estado_apache = (trim($apache_raw) == 'active') ? '🟢 Activo' : '🔴 Inactivo/Caído';

// 4B. Comprobar el estado de WireGuard
$vpn_raw = shell_exec('sudo systemctl is-active wg-quick@wg0');
$estado_vpn = (trim($vpn_raw) == 'active') ? '🟢 En ejecución' : '🔴 Detenido';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>TEcnycom - Panel Administrativo</title>
    
    <!--
      RUTAS RELATIVAS (../)
      Como este archivo está en la carpeta /admin/,
      necesitamos "subir un nivel" (con '../') para
      encontrar las carpetas /css/ y el 'logo.png' que están en la raíz.
    -->
    <link rel="stylesheet" href="../css/estilo.css" />
    <link rel="icon" href="../logo.png" type="image/png">
</head>
<body>
    <header>
        <h1>Panel Administrativo</h1>
        <nav>
            <!-- Los enlaces también suben un nivel (../) -->
            <a href="../index.php">Inicio</a> |
            <!--
            <a href="../empleados.php">Directorio de Empleados</a> |
            <a href="../solicitudes.php">Solicitudes</a>
            <!--
              Como este dashboard SÓLO lo ve un admin logueado,
              no necesitamos el 'if (isset...)' aquí.
              Podemos poner el enlace de "Cerrar Sesión" directamente.
            -->
            <a href="../logout.php" style="color: #FFC107;"><b>Cerrar Sesión (<?php echo htmlspecialchars($_SESSION['usuario_logueado']); ?>)</b></a>
        </nav>
    </header>

    <!-- Sección de Estado (muestra las variables PHP de arriba) -->
    <section>
        <h2>Estado del sistema</h2>
        <ul>
            <li>Servidor Web (Apache): <strong><?php echo $estado_apache; ?></strong></li>
            <li>Servidor VPN (WireGuard): <strong><?php echo $estado_vpn; ?></strong></li>
            <li>Base de datos: <strong>Desconectada</strong> (Simulado)</li>
        </ul>
    </section>

    <!-- Sección de Solicitudes (Lectura de archivo) -->
    <section>
        <h2>Últimas solicitudes</h2>
  	<?php
        // '__DIR__' es la carpeta actual (/admin/)
      	$archivo = __DIR__ . '/solicitudes_guardadas.html';
        
        // Comprueba si el archivo existe Y no está vacío
        if (file_exists($archivo) && filesize($archivo) > 0) {
            // "Imprime" todo el contenido del archivo aquí
            echo file_get_contents($archivo);
        } else {
            echo "<p>No hay solicitudes nuevas.</p>";
        }
	?>
    </section>

    <!-- Sección de Noticias (Formulario de envío) -->
    <section>
        <h2>Publicar Nueva Noticia</h2>
        
        <!--
          Este formulario envía los datos a 'procesar_noticia.php',
          que está en esta misma carpeta /admin/.
        -->
        <form action="procesar_noticia.php" method="post">
            <label for="noticia">Contenido de la noticia:</label><br>
            <textarea id="noticia" name="noticia" rows="5" style="width: 90%;" required></textarea><br><br>
            <input type="submit" value="Publicar Noticia">
        </form>
    </section>

    <footer>
        <p>© 2025 TEcnycom - Todos los derechos reservados.</p>
    </footer>
</body>
</html>
