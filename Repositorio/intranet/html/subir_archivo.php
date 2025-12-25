<?php
/*
 * subir_archivo.php
 *
 * Este script NO es una página web que se ve. Es un "backend".
 * Su trabajo es:
 * 1. Recoger el archivo enviado desde el formulario de "Gestión de Documentos" (index.php).
 * 2. Validar que el tipo de archivo sea seguro (¡MUY IMPORTANTE!).
 * 3. Comprobar si ya existe un archivo con ese nombre.
 * 4. Guardar el archivo en la carpeta /archivos/.
 * 5. Mostrar un mensaje de éxito/error y redirigir al inicio.
 */

// --- 1. DEFINIR LA CARPETA DE DESTINO ---
$ruta_subidas = __DIR__ . '/archivos/';

// Variable para el mensaje que mostraremos al final
$mensaje_usuario = '';

// --- 2. COMPROBAR LA SUBIDA INICIAL ---
// Comprueba si se envió un archivo (name="archivo") Y
// si el código de error es 0 (UPLOAD_ERR_OK), que significa "subido con éxito".
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    
    // --- 3. MEDIDA DE SEGURIDAD 1: VALIDAR EXTENSIÓN (Whitelist) ---
    // (Este paso faltaba en tu código original y es crítico)
    
    // 'basename' es una función de seguridad que elimina
    // caracteres peligrosos (como '../') del nombre del archivo.
    $nombre_original = basename($_FILES['archivo']['name']);
    
    // Sacamos la extensión (pdf, jpg, etc.)
    $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
    
    // Lista "blanca" de extensiones seguras que SÍ permitimos.
    // NUNCA permitas .php, .html, .js, .exe, .bat
    $extensiones_permitidas = ['pdf', 'docx', 'doc', 'xlsx', 'xls', 'jpg', 'jpeg', 'png', 'txt', 'zip', 'rar'];

    // Comprueba si la extensión del archivo está en nuestra lista permitida
    if (in_array($extension, $extensiones_permitidas)) {
        
        // --- 4. MEDIDA DE SEGURIDAD 2: EVITAR SOBRESCRIBIR ARCHIVOS ---
        // (Esto es una mejora. Tu código original borraba archivos si se llamaban igual)
        
        $nombre_base = pathinfo($nombre_original, PATHINFO_FILENAME); // "documento"
        $nombre_final = $nombre_original; // "documento.pdf"
        $ruta_destino = $ruta_subidas . $nombre_final;
        $contador = 1;

        // Bucle "while": Mientras el archivo ya exista...
        while (file_exists($ruta_destino)) {
            // ...cambia el nombre y vuelve a comprobar.
            // ej. "documento (1).pdf", "documento (2).pdf", etc.
            $nombre_final = $nombre_base . " ($contador)." . $extension;
            $ruta_destino = $ruta_subidas . $nombre_final;
            $contador++;
        }

        // --- 5. MOVER EL ARCHIVO A SU DESTINO FINAL ---
        // 'move_uploaded_file' es la función segura para mover el archivo
        // desde la carpeta temporal de PHP a nuestra carpeta de destino.
        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_destino)) {
            // ¡Éxito!
            $mensaje_usuario = "¡Éxito! Archivo subido correctamente: " . htmlspecialchars($nombre_final);
        } else {
            // Error de permisos en el servidor
            $mensaje_usuario = "Error: No se pudo mover el archivo. (Comprueba los permisos de la carpeta /archivos/)";
        }
        
    } else {
        // Error si la extensión (ej. .php) no estaba en nuestra lista.
        $mensaje_usuario = "Error: Tipo de archivo no permitido. Solo se aceptan: " . implode(', ', $extensiones_permitidas);
    }
    
} else {
    // Error si PHP falló al subir el archivo
    // (Causa común: el archivo es demasiado grande para la configuración 'upload_max_filesize' de php.ini)
    $mensaje_usuario = "Error en la subida. (Archivo no seleccionado o demasiado grande)";
}

// --- 6. MOSTRAR MENSAJE Y REDIRIGIR ---
// Mostramos el mensaje de éxito o error que hemos preparado
echo "<h2>" . $mensaje_usuario . "</h2>";
echo "<p>Redirigiendo a la página principal en 3 segundos...</p>";

// Redirigimos de vuelta al 'index.php'
header("refresh:3;url=index.php");
exit;
?>
