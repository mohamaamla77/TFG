<?php
/*
 * solicitudes.php (Página del Formulario de Solicitudes)
 */

// session_start() es necesario para poder leer la variable $_SESSION
// y saber si el usuario ha iniciado sesión o no.
session_start(); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>TEcnycom - Formulario de Solicitudes</title>
    
    <!-- Enlace a la hoja de estilos CSS (define colores, fuentes, etc.) -->
    <link rel="stylesheet" href="css/estilo.css" />
    <!-- Enlace al icono (favicon) de la pestaña -->
    <link rel="icon" href="logo.png" type="image/png">
</head>
<body>
    <!-- INICIO DE LA CABECERA Y MENÚ -->
    <header>
        <h1>Formulario de Solicitudes</h1>
        
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
                // 'htmlspecialchars' es por SEGURIDAD
                echo '<a href="logout.php" style="color: #FFC107;"><b>Cerrar Sesión (' . htmlspecialchars($_SESSION['usuario_logueado']) . ')</b></a>';

            } else {
                
                // Si NO existe (usuario no logueado):
                echo '<a href="login.php"><b>Iniciar Sesión</b></a>';
            }
            ?>
        </nav>
    </header>
    <!-- FIN DE LA CABECERA Y MENÚ -->

    
    <!-- INICIO CONTENIDO: FORMULARIO DE SOLICITUD -->
    <!-- 
      Este es el formulario que envía los datos a 'procesar_solicitud.php' .
      Es una de las partes clave de la intranet.
    -->
    <section>
    <form 
        action="procesar_solicitud.php" 
        method="post" 
        enctype="multipart/form-data">
        <!--
          'action' -> "procesar_solicitud.php"
            Define qué archivo PHP recibirá y procesará los datos del formulario.
            
          'method="post"'
            Envía los datos de forma "oculta". Es más seguro que "get",
            que los pondría en la URL.
            
          'enctype="multipart/form-data"'
            ¡MUY IMPORTANTE! Esto es obligatorio si el formulario
            va a enviar archivos (como nuestro campo 'adjunto').
        -->

        <!-- Campo 1: Nombre (texto) -->
        <!-- 'label for="nombre"' -> Conecta la etiqueta al 'input' con id="nombre" -->
        <label for="nombre">Nombre completo:</label><br />
        <!-- 
          'type="text"' -> Un campo de texto normal.
          'id="nombre"' -> Identificador único (para el 'label').
          'name="nombre"' -> El "nombre de la variable" que recibirá PHP (ej. $_POST['nombre']).
          'required' -> HTML5 obliga al usuario a rellenar este campo.
        -->
        <input type="text" id="nombre" name="nombre" required /><br /><br />

        <!-- Campo 2: Email (texto con validación) -->
        <label for="email">Correo electrónico:</label><br />
        <!-- 'type="email"' -> HTML5 comprueba que el texto parece un email. -->
        <input type="email" id="email" name="email" required /><br /><br />

        <!-- Campo 3: Tipo (menú desplegable) -->
        <label for="tipo">Tipo de solicitud:</label><br />
        <select id="tipo" name="tipo" required>
            <option value="">-- Seleccione --</option> <!-- Opción por defecto -->
            <option value="permiso">Permiso</option>
            <option value="incidencia">Incidencia técnica</option>
            <option value="otro">Otro</option>
        </select><br /><br />

        <!-- Campo 4: Mensaje (área de texto) -->
        <label for="mensaje">Descripción:</label><br />
        <textarea id="mensaje" name="mensaje" rows="5" required></textarea><br><br />

        <!-- Campo 5: Archivo Adjunto (subida de fichero) -->
        <label for="adjunto">Adjuntar archivo (opcional):</label><br>
        <!-- 'type="file"' -> El navegador mostrará un botón de "Examinar..." -->
        <input type="file" id="adjunto" name="adjunto"><br><br>

        <!-- Botón de envío -->
        <input type="submit" value="Enviar solicitud" />
    </form>
    </section>
    <!-- FIN CONTENIDO: FORMULARIO DE SOLICITUD -->

    
    <!-- INICIO PIE DE PÁGINA -->
    <footer>
        <p>© 2025 TEcnycom - Todos los derechos reservados.</p>
    </footer>
    <!-- FIN PIE DE PÁGINA -->
    
</body>
</html>
