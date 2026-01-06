<?php
/*
 * procesar_solicitud.php
 *
 * Este script NO es una página web que se ve.
 * Su trabajo es:
 * 1. Recoger los datos enviados por el formulario de 'solicitudes.php'.
 * 2. Validar los datos de texto.
 * 3. Procesar y guardar el archivo adjunto (si existe) de forma segura.
 * 4. Guardar la solicitud en un archivo de texto/HTML.
 * 5. Mostrar un mensaje de éxito y redirigir al usuario.
 */

// --- 1. RECOGIDA DE DATOS DEL FORMULARIO ---

// '$_POST' es un array especial de PHP que contiene todos los datos
// enviados por un formulario con 'method="post"'.
// Usamos el "operador de fusión de null" (?? '') como atajo.
// Significa: "coge el valor de $_POST['nombre'], pero si no existe, usa '' (vacío)".
// Esto evita errores si alguien accede al script directamente.
$nombre = $_POST['nombre'] ?? '';
$email = $_POST['email'] ?? '';
$tipo = $_POST['tipo'] ?? '';
$mensaje = $_POST['mensaje'] ?? '';

// --- 2. VALIDACIÓN BÁSICA DE TEXTO ---

// Si falta algún campo obligatorio, muestra un error y detiene el script.
if (!$nombre || !$email || !$tipo || !$mensaje) {
    echo "Faltan datos en el formulario. <a href='solicitudes.php'>Volver</a>";
    exit; // 'exit' detiene la ejecución del script aquí.
}

// --- 3. PROCESAMIENTO DEL ARCHIVO ADJUNTO ---

// Esta variable guardará el enlace <a> si la subida es exitosa.
// Si no se sube archivo, se quedará vacía.
$enlace_archivo = '';

// '__DIR__' es una constante de PHP que significa "el directorio de este script"
$ruta_subidas = __DIR__ . '/solicitudes/'; // La carpeta de destino

// '$_FILES' es otro array especial de PHP para los archivos subidos.
// 'isset($_FILES['adjunto'])' -> Comprueba si se envió un archivo con name="adjunto".
// '$_FILES['adjunto']['error'] == 0' -> Comprueba que la subida fue exitosa (código 0).
if (isset($_FILES['adjunto']) && $_FILES['adjunto']['error'] == 0) {
    
    // --- 3A. MEDIDA DE SEGURIDAD 1: VALIDAR TIPO DE ARCHIVO ---
    // NO se confía en el tipo de archivo que dice el navegador, se confía en la extensión.
    
    $nombre_original = $_FILES['adjunto']['name']; // ej. "mi_factura.pdf"
    
    // 'pathinfo' saca la extensión. 'strtolower' la pasa a minúsculas (PDF -> pdf).
    $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
    
    // Lista blanca de extensiones que SÍ permitimos.
    // Esto es crucial para evitar que alguien suba un virus (ej. .exe) o
    // un script malicioso (ej. .php).
    $extensiones_permitidas = ['pdf', 'docx', 'jpg', 'jpeg', 'png', 'txt'];

    // 'in_array()' comprueba si la $extension del archivo está en nuestra $extensiones_permitidas.
    if (in_array($extension, $extensiones_permitidas)) {
        
        // --- 3B. MEDIDA DE SEGURIDAD 2: CREAR NOMBRE DE ARCHIVO ÚNICO ---
        // Si dos usuarios suben "factura.pdf", el segundo borraría al primero.
        // 'uniqid()' genera un ID único basado en la hora actual (ej. "6908e812c62f2").
        // Esto asegura que no haya colisiones.
        $nombre_seguro = uniqid() . '.' . $extension; // ej. "6908e812c62f2.pdf"
        $ruta_destino = $ruta_subidas . $nombre_seguro; // Ruta final donde se guardará

        // --- 3C. MOVER EL ARCHIVO ---
        // 'move_uploaded_file()' es la función segura de PHP para mover
        // el archivo desde la carpeta temporal de PHP (tmp_name) a nuestra carpeta de destino.
        if (move_uploaded_file($_FILES['adjunto']['tmp_name'], $ruta_destino)) {
            
            // El archivo está en /solicitudes/. Ahora creamos el HTML.
            // 'htmlspecialchars()' es OTRA MEDIDA DE SEGURIDAD (XSS)
            // para los nombres de archivo.
            
            $enlace_archivo = "<br><b>Archivo adjunto:</b> <a href='../solicitudes/" . htmlspecialchars($nombre_seguro) . "' target='_blank'>" . htmlspecialchars($nombre_original) . "</a>";
        
        } else {
            // Error si 'move_uploaded_file' falla.
            echo "Error: No se pudo mover el archivo. <a href='solicitudes.php'>Volver</a>";
            exit;
        }
    } else {
        // Error si la extensión (ej. ".exe") no estaba en nuestra lista blanca.
        echo "Error: Tipo de archivo no permitido (solo PDF, DOCX, JPG, PNG, TXT). <a href='solicitudes.php'>Volver</a>";
        exit;
    }
}

// --- 4. FORMATEAR LA SOLICITUD COMO HTML ---

// 'htmlspecialchars()' es la MEDIDA DE SEGURIDAD MÁS IMPORTANTE aquí.
// Evita que un usuario escriba código HTML o JavaScript malicioso (Ataque XSS)
$entrada = "<p><strong>" . htmlspecialchars($nombre) . "</strong> (" . htmlspecialchars($email) . ") [" . htmlspecialchars($tipo) . "]:<br>" .
           // 'nl2br()' convierte los saltos de línea (Enter) en etiquetas <br>
           nl2br(htmlspecialchars($mensaje)) .
           // Añade el enlace <a> si la subida fue exitosa, o '' (vacío) si no.
           $enlace_archivo .
           "</p>\n"; // '\n' es un salto de línea (para ordenar el archivo guardado)

// --- 5. GUARDAR LA SOLICITUD EN EL ARCHIVO ---

// Ruta al archivo donde guardamos todo (nuestra "base de datos" plana)
$archivo = __DIR__ . '/admin/solicitudes_guardadas.html';

// 'file_get_contents()' lee el contenido antiguo del archivo
// El '?' es un if corto:
// "Si el archivo existe, léelo; si no, usa '' (vacío)"
$contenido_actual = file_exists($archivo) ? file_get_contents($archivo) : '';

// 'file_put_contents()' escribe en el archivo.
// Escribimos la $entrada nueva primero, y luego el $contenido_actual.
// Esto hace que las solicitudes más nuevas aparezcan arriba.
file_put_contents($archivo, $entrada . $contenido_actual);

// --- 6. MOSTRAR MENSAJE DE ÉXITO Y REDIRIGIR ---

echo "<h2>Solicitud recibida correctamente</h2>";
echo "<p>Gracias, <strong>" . htmlspecialchars($nombre) . "</strong>. Su solicitud ha sido enviada.</p>";
echo "<p>Redirigiendo al panel administrativo...</p>";

// 'header("refresh:3...")' es una orden de PHP al navegador.
// Le dice: "Espera 3 segundos, y luego redirige a esta URL".
header("refresh:3;url=admin/dashboard.php");
exit; // Termina el script
?>
