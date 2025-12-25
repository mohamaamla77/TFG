<?php
/*
 * empleados.php (Página de Directorio)
 *
 * Este archivo era 'empleados.html', pero se ha renombrado a '.php'.
 *
 * ¿POR QUÉ?
 * Para poder incluir el mismo menú dinámico que tiene 'index.php'.
 * Así, si el usuario ha iniciado sesión, verá aquí también
 * el botón de "Cerrar Sesión" y el enlace a "Área Administrativa".
 */

// session_start() es necesario para poder leer la variable $_SESSION
// y saber si el usuario ha iniciado sesión o no.
session_start(); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>TEcnycom - Directorio de Empleados</title>
    
    <!-- Enlace a la hoja de estilos CSS (define colores, fuentes, etc.) -->
    <link rel="stylesheet" href="css/estilo.css" />
    <!-- Enlace al icono (favicon) de la pestaña -->
    <link rel="icon" href="logo.png" type="image/png">
</head>
<body>
    <!-- INICIO DE LA CABECERA Y MENÚ -->
    <header>
        <h1>Directorio de Empleados TEcnycom</h1>
        
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
                // 'htmlspecialchars' es por SEGURIDAD, para no "romper" la web
                // si un nombre de usuario tiene caracteres extraños.
                echo '<a href="logout.php" style="color: #FFC107;"><b>Cerrar Sesión (' . htmlspecialchars($_SESSION['usuario_logueado']) . ')</b></a>';

            } else {
                
                // Si NO existe (usuario no logueado):
                echo '<a href="login.php"><b>Iniciar Sesión</b></a>';
            }
            ?>
        </nav>
    </header>
    <!-- FIN DE LA CABECERA Y MENÚ -->

    
    <!-- INICIO CONTENIDO: TABLA DE EMPLEADOS -->
    <!-- Esta es una tabla HTML estática (escrita a mano) -->
    <section>  
    <table border="1" cellpadding="10">
        <!-- <thead> define la fila de encabezado de la tabla -->
        <thead>
            <!-- <tr> es una fila (Table Row) -->
            <tr>
                <!-- <th> es una celda de encabezado (Table Header) -->
                <th>Nombre</th>
                <th>Cargo</th>
                <th>Email</th>
                <th>Teléfono</th>
            </tr>
        </thead>
        <!-- <tbody> define el cuerpo principal de la tabla -->
        <tbody>
            <!-- Fila 1 de datos -->
            <tr>
                <!-- <td> es una celda de datos (Table Data) -->
                <td>María López</td>
                <td>Gerente de Proyecto</td>
                <td>mlopez@tecnycom.com</td>
                <td>+34 600 123 456</td>
            </tr>
            <!-- Fila 2 de datos -->
            <tr>
                <td>Javier Pérez</td>
                <td>Administrador de Sistemas</td>
                <td>jperez@tecnycom.com</td>
                <td>+34 600 654 321</td>
            </tr>
            <!-- Fila 3 de datos -->
            <tr>
                <td>Laura García</td>
                <td>Soporte Técnico</td>
                <td>lgarcia@tecnycom.com</td>
                <td>+34 600 789 012</td>
            </tr>
        </tbody>
    </table>
    </section>
    <!-- FIN CONTENIDO: TABLA DE EMPLEADOS -->

    
    <!-- INICIO PIE DE PÁGINA -->
    <footer>
        <p>© 2025 TEcnycom - Todos los derechos reservados.</p>
    </footer>
    <!-- FIN PIE DE PÁGINA -->
    
</body>
</html>
