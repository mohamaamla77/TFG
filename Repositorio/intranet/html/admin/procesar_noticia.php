<?php
/*
 * admin/procesar_noticia.php (Script de Lógica de Noticias)
 *
 * Este script NO es una página web que se ve. Es un "backend".
 * Su único trabajo es:
 * 1. Recoger el texto de la noticia enviado desde 'dashboard.php'.
 * 2. Validar que el texto no esté vacío.
 * 3. Formatear la noticia como un <li> de HTML (añadiendo la fecha).
 * 4. Guardar ese HTML en el archivo 'noticias_guardadas.html'.
 * 5. Redirigir al administrador de vuelta al 'dashboard.php'.
 */


// --- 1. RECOGER EL DATO DEL FORMULARIO ---
// Coge el valor del campo <textarea> que tenía 'name="noticia"'
// El '?? ""' es para evitar errores si el campo llega vacío.
$noticia_texto = $_POST['noticia'] ?? '';

// --- 2. VALIDAR QUE NO ESTÉ VACÍO ---
// 'empty()' comprueba si la variable está vacía (0, "", null).
if (empty($noticia_texto)) {
    // Si está vacía, muestra un error y detiene el script.
    echo "Error: El contenido de la noticia no puede estar vacío. <a href='dashboard.php'>Volver</a>";
    exit;
}

// --- 3. CREAR EL FORMATO HTML PARA LA NOTICIA ---
// 'date('d/m/Y')' obtiene la fecha actual (ej. "12/11/2025")
$fecha = date('d/m/Y'); 

// MEDIDA DE SEGURIDAD (XSS):
// 'htmlspecialchars()' es crucial. Convierte caracteres especiales
// (como '<' y '>') en texto normal. Evita que un admin
// (o un atacante) pueda insertar código <script> malicioso.
//
// 'nl2br()' es para formato. Convierte los "Enter" (saltos de línea)
// en etiquetas HTML <br> para que el texto se vea bien.
$noticia_html = "<li><strong>" . $fecha . "</strong> - " . 
                nl2br(htmlspecialchars($noticia_texto)) . "</li>\n";


// --- 4. GUARDAR LA NOTICIA (LA MÁS NUEVA ARRIBA) ---

// '__DIR__' es la carpeta actual (es decir, /admin/)
$archivo = __DIR__ . '/noticias_guardadas.html';

// Lee el contenido antiguo del archivo (si existe)
$contenido_actual = file_exists($archivo) ? file_get_contents($archivo) : '';

// Escribe en el archivo.
// Pone la $noticia_html (la nueva) PRIMERO,
// y luego añade el $contenido_actual (el antiguo).
// Esto hace que las noticias más nuevas siempre aparezcan arriba.
file_put_contents($archivo, $noticia_html . $contenido_actual);

// --- 5. REDIRIGIR DE VUELTA AL DASHBOARD ---
// 'header('Location: ...')' envía una orden al navegador
// para que cargue la página 'dashboard.php' inmediatamente.
header("Location: dashboard.php");
exit; // Detiene el script
?>