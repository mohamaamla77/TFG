<?php
/*
 * login.php (Página de Inicio de Sesión)
 *
 * Este archivo era 'login.html', pero se ha renombrado a '.php'.
 *
 * ¿POR QUÉ?
 * Para poder incluir el mismo menú dinámico que tiene 'index.php'.
 * Así, la cabecera es consistente. Si un usuario ya logueado
 * llega aquí, verá el botón de "Cerrar Sesión".
 */

// session_start() es necesario para poder leer la variable $_SESSION
// y saber si el usuario ha iniciado sesión o no.
session_start(); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" /> 
    <title>TEcnycom - Acceso de Empleados</title>
    
    <!-- Enlace a la hoja de estilos CSS -->
    <link rel="stylesheet" href="css/estilo.css" />
    <!-- Enlace al icono (favicon) de la pestaña -->
    <link rel="icon" href="logo.png" type="image/png">
</head>
<body>
    <!-- INICIO DE LA CABECERA Y MENÚ -->
    <header>
        <h1>Acceso de Empleados</h1>
        
        <!-- MENÚ DINÁMICO -->
        <nav>
            <a href="index.php">Inicio</a> |
            <a href="empleados.php">Directorio de Empleados</a> |
            <a href="solicitudes.php">Solicitudes</a> |
            
            <?php
            // Comprueba si la variable de sesión 'usuario_logueado' existe
            if (isset($_SESSION['usuario_logueado'])) {
                
                // Si SÍ existe (usuario logueado):
                echo '<a href="admin/dashboard.php">Área Administrativa</a> | ';
                echo '<a href="logout.php" style="color: #FFC107;"><b>Cerrar Sesión (' . htmlspecialchars($_SESSION['usuario_logueado']) . ')</b></a>';

            } else {
                
                // Si NO existe (usuario no logueado):
                echo '<a href="login.php"><b>Iniciar Sesión</b></a>';
            }
            ?>
        </nav>
    </header>
    <!-- FIN DE LA CABECERA Y MENÚ -->

    
    <!-- INICIO CONTENIDO: FORMULARIO DE LOGIN -->
    <section>
        <h2>Iniciar Sesión</h2>
        
        <!-- 
          Este formulario envía los datos a 'validar_login.php' .
        -->
        <form 
            action="validar_login.php" 
            method="post">
            <!--
              'action' -> "validar_login.php"
                Define el script PHP que recibirá y comprobará el usuario/clave.
                
              'method="post"'
                Envía los datos de forma "oculta". Es obligatorio
                para enviar contraseñas de forma segura (no en la URL).
            -->

            <!-- Campo 1: Usuario (texto) -->
            <label for="usuario">Usuario:</label><br>
            <!-- 
              'name="usuario"' -> El nombre de la variable que recibirá PHP (ej. $_POST['usuario']).
            -->
            <input type="text" id="usuario" name="usuario" required><br><br>

            <!-- Campo 2: Contraseña (oculto) -->
            <label for="clave">Contraseña:</label><br>
            <!-- 
              'type="password"' -> Oculta el texto (muestra ••••)
              'name="clave"' -> La variable que recibirá PHP (ej. $_POST['clave']).
            -->
            <input type="password" id="clave" name="clave" required><br><br>

            <!-- Botón de envío -->
            <input type="submit" value="Entrar">
        </form>
    </section>
    <!-- FIN CONTENIDO: FORMULARIO DE LOGIN -->

    
    <!-- INICIO PIE DE PÁGINA -->
    <footer>
        <p>© 2025 TEcnycom - Todos los derechos reservados.</p>
    </footer>
    <!-- FIN PIE DE PÁGINA -->
    
</body>
</html>
