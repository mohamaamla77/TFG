<?php
/*
 * index.php (Página Principal de la Intranet)
 *
 * Este archivo es la página de bienvenida.
 * Es un archivo .php porque necesita hacer tres cosas "dinámicas" (que cambian):
 * 1. Comprobar si el usuario ha iniciado sesión (para cambiar el menú).
 * 2. Cargar las noticias dinámicamente desde un archivo.
 * 3. Listar los archivos de la carpeta "Gestión de Documentos".
 */

// session_start() DEBE ser la primera línea de PHP.
// Inicia la sesión o reanuda una existente.
// Esto nos permite usar la variable $_SESSION, que es como una memoria
// que recuerda quién es el usuario entre las distintas páginas.
session_start(); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>TEcnycom - Intranet Corporativa</title>
    
    <!-- Enlace a la hoja de estilos CSS que define el diseño y los colores -->
    <link rel="stylesheet" href="css/estilo.css" />
    
    <!-- Enlace al icono (favicon) que aparece en la pestaña del navegador -->
    <link rel="icon" href="logo.png" type="image/png">
</head>
<body>
    <!-- INICIO DE LA CABECERA Y MENÚ -->
    <header>
        <h1>Bienvenido a la Intranet de TEcnycom</h1>
        
        <!-- Menú de Navegación Principal -->
        <nav>
            <a href="index.php">Inicio</a> |
            <a href="empleados.php">Directorio de Empleados</a> |
            <a href="solicitudes.php">Solicitudes</a> |
            
            <?php
            /*
             * LÓGICA DEL MENÚ DINÁMICO
             *
             * 'isset(...)' comprueba si la variable $_SESSION['usuario_logueado'] "existe".
             * Esta variable solo la creamos en 'validar_login.php' si el login es correcto.
             */
            if (isset($_SESSION['usuario_logueado'])) {
                
                // Si SÍ existe (usuario ha iniciado sesión):
                // 1. Muestra el enlace al panel de administración.
                // 2. Muestra el enlace para "Cerrar Sesión" con el nombre del usuario.
                
                echo '<a href="admin/dashboard.php">Área Administrativa</a> | ';
                
                // 'htmlspecialchars()' es una FUNCIÓN DE SEGURIDAD MUY IMPORTANTE.
                // Evita que un nombre de usuario malicioso (ej. con <script>)
                // "rompa" la página o robe datos (Ataque XSS).
                echo '<a href="logout.php" style="color: #FFC107;"><b>Cerrar Sesión (' . htmlspecialchars($_SESSION['usuario_logueado']) . ')</b></a>';

            } else {
                
                // Si NO existe (nadie ha iniciado sesión):
                // Muestra el enlace para "Iniciar Sesión".
                echo '<a href="login.php"><b>Iniciar Sesión</b></a>';
            }
            ?>
        </nav>
    </header>
    <!-- FIN DE LA CABECERA Y MENÚ -->

    
    <!-- INICIO SECCIÓN "QUIÉNES SOMOS" (CONTENIDO ACTUALIZADO) -->
    <section>
        <h2>Quiénes somos</h2>
        <p>TEcnycom es una empresa dedicada a proveer soluciones tecnológicas integrales, enfocada en la innovación y la calidad de servicio.</p>
        <p>Nuestra misión es ser el socio tecnológico de confianza para las PYMES, ayudándolas a navegar la transformación digital.</p>
        <p>Este portal de intranet es una herramienta clave para nuestros empleados, permitiendo un acceso centralizado a noticias, gestión de documentos y solicitudes internas, todo protegido por nuestra infraestructura de VPN y firewall.</p>
    </section>
    <!-- FIN SECCIÓN "QUIÉNES SOMOS" -->

    
    <!-- INICIO SECCIÓN "ÚLTIMAS NOTICIAS" (DINÁMICO) -->
    <section>
        <h2>Últimas Noticias</h2>
        <ul>
            <?php
                /*
                 * LÓGICA DE NOTICIAS DINÁMICAS
                 * Este bloque de PHP lee un archivo HTML ('noticias_guardadas.html')
                 * que el administrador actualiza desde el 'dashboard.php'.
                 */
                
                // '__DIR__' es una constante de PHP que significa "este mismo directorio".
                // Es la forma más segura de construir una ruta de archivo.
                $archivo_noticias = __DIR__ . '/admin/noticias_guardadas.html';

                // 'file_exists()' comprueba si el archivo exist
                // 'filesize() > 0' comprueba que no esté vacío
                if (file_exists($archivo_noticias) && filesize($archivo_noticias) > 0) {
                    
                    // Si existe y tiene contenido, lo "imprime" (muestra)
                    // tal cual dentro del <ul>.
                    echo file_get_contents($archivo_noticias);
                    
                } else {
                    // Si no existe o está vacío, muestra un mensaje por defecto
                    echo "<li>No hay noticias por el momento.</li>";
                }
            ?>
        </ul>
    </section>
    <!-- FIN SECCIÓN "ÚLTIMAS NOTICIAS" -->

    
    <!-- INICIO SECCIÓN "GESTIÓN DE DOCUMENTOS" (DINÁMICO) -->
    <section>
        <h2>Gestión de Documentos</h2>
        
        <!-- 
          Formulario para subir archivos.
          - 'action="subir_archivo.php"' -> Script PHP que procesará la subida.
          - 'method="post"' -> Método de envío (oculto).
          - 'enctype="multipart/form-data"' -> ESENCIAL para poder subir archivos.
        -->
        <form action="subir_archivo.php" method="post" enctype="multipart/form-data">
            <label for="archivo">Selecciona un archivo para subir:</label><br>
            <input type="file" name="archivo" id="archivo" required><br><br>
            <input type="submit" value="Subir Archivo">
        </form>

        <h3>Archivos disponibles para descargar:</h3>
        <ul>
            <?php
            /*
             * LÓGICA DE LISTAR ARCHIVOS
             * Este bloque PHP escanea la carpeta /archivos/ y
             * crea un enlace de descarga <a> por cada archivo que encuentra.
             */
            
            $dir = __DIR__ . '/archivos/'; // Ruta física de la carpeta
            $webPath = 'archivos/'; // Ruta web para el enlace <a>

            if (is_dir($dir)) { // Comprueba si la carpeta '/archivos/' existe
                
                // 'scandir($dir)' lista TODOS los archivos (incl. '..' y '.')
                // 'array_diff' filtra los que no queremos mostrar.
                $archivos_a_ignorar = ['.', '..'];
                $files = array_diff(scandir($dir), $archivos_a_ignorar);
                
                // Comprueba si la carpeta está vacía (después de filtrar)
                if (empty($files)) {
                     echo "<li>No hay archivos disponibles.</li>";
                } else {
                    // 'foreach' crea un bucle, repitiendo el 'echo'
                    // por cada archivo encontrado en la variable $files.
                    foreach ($files as $file) {
                        // 'htmlspecialchars' (SEGURIDAD) evita XSS si un archivo
                        // tiene un nombre malicioso (ej. "><script>...).
                        // 'download' es un atributo HTML5 que fuerza la descarga.
                        echo "<li><a href=\"" . $webPath . htmlspecialchars($file) . "\" download>" . htmlspecialchars($file) . "</a></li>";
                    }
                }
            } else {
                // Mensaje de error si la carpeta /archivos/ no existe
                echo "<li>Error: El directorio de archivos no se ha encontrado.</li>";
            }
            ?>
        </ul>
    </section>
    <!-- FIN SECCIÓN "GESTIÓN DE DOCUMENTOS" -->

    
    <!-- INICIO PIE DE PÁGINA -->
    <footer>
        <p>© 2025 TEcnycom - Todos los derechos reservados.</p>
    </footer>
    <!-- FIN PIE DE PÁGINA -->
    
</body>
</html>
