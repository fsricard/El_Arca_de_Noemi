<?php
// Función para restringir contenido solo para el rol "admin"
function tienePermiso(): bool
{
    $rolesPermitidos = ['admin'];

    return in_array($_SESSION['rol'], $rolesPermitidos, true);
}

// Función para detectar dispositos móviles
function esSoloMovil()
{
    $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');

    // Detectores de móvil
    $moviles = [
        'iphone',
        'ipod',
        'android',
        'blackberry',
        'windows phone',
        'opera mini',
        'mobile',
        'webos'
    ];

    // Si es tablet, no es móvil
    if (esSoloTablet()) {
        return false;
    }

    foreach ($moviles as $m) {
        if (strpos($ua, $m) !== false) {
            return true;
        }
    }

    return false;
}

// Función para detectar tablets
function esSoloTablet()
{
    $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');

    // Detectores de tablet
    $tablets = [
        'ipad',
        'tablet',
        'kindle',
        'silk'
    ];

    foreach ($tablets as $t) {
        if (strpos($ua, $t) !== false) {
            return true;
        }
    }

    // Caso especial: Android tablet (Android sin "mobile")
    if (strpos($ua, 'android') !== false && strpos($ua, 'mobile') === false) {
        return true;
    }

    return false;
}

// Función combinada para detectar dispositivos móviles y tablets
function esMovilOtablet()
{
    return esSoloMovil() || esSoloTablet();
}

// Función para limitar el número de palabras en un texto a 20 palabras
function limitar_palabras($texto, $max_palabras = 20)
{
    // Eliminar etiquetas HTML para evitar cortes feos
    $texto_limpio = trim(strip_tags($texto));

    // Convertir múltiples espacios en uno solo
    $texto_limpio = preg_replace('/\s+/', ' ', $texto_limpio);

    $palabras = explode(' ', $texto_limpio);

    if (count($palabras) <= $max_palabras) {
        return $texto_limpio;
    }

    $corte = array_slice($palabras, 0, $max_palabras);
    return implode(' ', $corte) . '...';
}

// Función para limpiar las carpetas en "uploads"
function limpiarNombreCarpeta($cadena)
{
    $cadena = strtolower($cadena);
    $cadena = preg_replace('/[^a-z0-9_\-]/', '_', $cadena);
    return $cadena;
}

// Función para imprimir los textos dinámicos en el header del BackEnd
function tituloPagina($pagina)
{
    // Array asociativo de títulos
    $titulos = [
        'logs'                                      => 'Registro de logs',
        'contacto'                                  => 'Mensajes de contacto',
        'usuarios'                                  => 'Gestión de Usuarios',
        'dashboard'                                 => 'Panel de Control',
        'noemi_dice'                                => 'Frases de Noemí',
        'asi_es_noemi'                              => 'Así es Noemí',
        'crear_usuario'                             => 'Crear usuario',
        'contacto_intro'                            => 'Invocación al contacto',
        'noemi_bichillos'                           => 'Gestión de los bichillos de Noemí',
        'tablas_de_datos'                           => 'Tablas de la base de datos',
        'contacto_editar'                           => 'Vista de mensaje',
        'crear_recaudacion'                         => 'Crear campaña CrowdFunding',
        'noemi_dice_listado'                        => 'Listado de las frases de Noemí',
        'adopciones_listado'                        => 'Listado de adopciones',
        'editar_recaudacion'                        => 'Editar recaudación',
        'tablas_de_datos_ver'                       => 'Contenido de la tabla de la base datos',
        'adopciones_adoptante'                      => 'Incluir un nuevo adoptante',
        'adopciones_adoptante'                      => 'Incluir un nuevo adoptante',
        'listado_recaudaciones'                     => 'Listado de recaudaciones',
        'apadrina_editar_animal'                    => 'Editar animal para apadrinar',
        'apadrinamiento_incluir'                    => 'Gestión de apadrinamientos',
        'politica_de_privacidad'                    => 'Política de privacidad',
        'apadrina_editar_padrino'                   => 'Edición de un padrino',
        'adopciones_incluir_raza'                   => 'Incluir nuevo animal',
        'noemi_bichillos_listado'                   => 'Listado de los bichillos de Noemí',
        'sistema_adopciones_crear'                  => 'Crear nueva adopción',
        'apadrina_editar_relacion'                  => 'Edita la última relación',
        'apadrina_listado_padrinos'                 => 'Listado de padrinos',
        'apadrina_listado_animales'                 => 'Listado de animales',
        'adopciones_incluir_animal'                 => 'Gestión de Adopciones',
        'sistema_adopciones_iniciar'                => 'Iniciar nueva adopción',
        'opiniones_de_usuario_editar'               => 'Opinión de usuario',   
        'apadrina_ver_apadrinamientos'              => 'Listado individual',
        'opiniones_de_usuario_listado'              => 'Opiniones de usuarios',
        'incluir_plataforma_crowdfunding'           => 'Gestión plataformas CrowdFunding',
        'sistema_adopciones_por_adoptante'          => 'Adopciones por adoptante',
        'sistema_adopciones_editar_animales'        => 'Editar animal en adopción',
        'sistema_adopciones_editar_adoptante'       => 'Edición de adoptantes',
        'sistema_adopciones_editar_formulario'      => 'Edición de un adoptante',
        'sistema_adopciones_listado_adoptantes'     => 'Listado de adoptantes',
        'sistema_adopciones_formulario_frontend'    => 'Listado formulario FrontEnd'
    ];

    // Si existe en el array, devolvemos el título; si no, uno genérico
    return $titulos[$pagina] ?? 'Administración';
}

// Función para imprimir textos personalizados en "header.php" del FrontEnd
function mostrarTextoPersonalizado()
{
    // Recupera la ruta desde la variable global
    $pagina = $GLOBALS['pagina_actual'] ?? '';

    // Recupera el nombre del animal si existe
    $nombreAnimal = $GLOBALS['nombre_animal'] ?? '';

    // Define los textos personalizados
    $textos = [
        ''                          => 'El Arca de Noemi',
        '404'                       => '!!Vaya por Dios¡¡, que situación más vergonzosa',
        'inicio'                    => 'El Arca de Noemí',
        'contacto'                  => 'Contacta con Noemí',
        'asi-es-noemi'              => 'Esta es Noemí, descubre su historia.',
        'ficha-adopcion'            => 'Ficha individual para adoptar a ',
        'listado-adopciones'        => 'Listado de todos los animales listos para adoptar en el santuario de "El Arca de Noemí"',
        'listado-crowdfunding'      => 'Listado de todas las campañas de CrowdFunding activas en El Arca de Noemí',
        'formulario-adoptante'      => 'Formulario con los datos necesarios para intentar adoptar a ',
        'ficha-apadrinamiento'      => 'Ficha individual para apadrinar a ',
        'politica-de-privacidad'    => 'Política de privacidad',
        'listado-apadrinamientos'   => 'Listado de todos los animales listos para apdrinar de "El Arca de Noemí',
    ];

    // Si estamos en ficha-adopcion y tenemos nombre del animal → título dinámico
    if ($pagina === 'ficha-adopcion' && !empty($nombreAnimal)) {
        echo $textos[$pagina] . $nombreAnimal;
        return;
    }

    // Si estamos en ficha-apadrinamiento y tenemos nombre del animal → título dinámico
    if ($pagina === 'ficha-apadrinamiento' && !empty($nombreAnimal)) {
        echo $textos[$pagina] . $nombreAnimal;
        return;
    }

    // Si estamos en formulario-adopciones y tenemos nombre del animal → título dinámico
    if ($pagina === 'formulario-adoptante' && !empty($nombreAnimal)) {
        echo $textos[$pagina] . $nombreAnimal;
        return;
    }

    // Imprime el texto correspondiente o uno por defecto
    echo $textos[$pagina] ?? 'El Arca de Noemi';
}

// Función para mostrar el CopyRight en el footer
function CopyrightRicardFS($startYear = 2024)
{
    $currentYear = date('Y');
    $yearDisplay = ($startYear == $currentYear) ? $currentYear : "$startYear – $currentYear";
    return "&copy; $yearDisplay El Arca de Noemí - Todos los derechos reservados";
}

// Función para las frases cortas de Noemí
function noemi_frase_random(PDO $pdo): string
{
    $stmt = $pdo->query("
        SELECT frase 
        FROM noemi_frases 
        WHERE activo = 1 
        ORDER BY RAND() 
        LIMIT 1
    ");

    return $stmt->fetchColumn() ?: 'Noemí esta descansando...';
}

// Función para las imágenes aleatorias de "Los bichillos de Noemí"
function noemi_bichillos_random(PDO $pdo): ?string
{
    $stmt = $pdo->query("
        SELECT bichillo 
        FROM noemi_bichillos 
        WHERE activo = 1 
        ORDER BY RAND() 
        LIMIT 1
    ");

    $bichillo = $stmt->fetchColumn();

    return $bichillo ?: null;
}

// Función para crear rutas absolutas
function base_url(): string
{
    // Detectar protocolo
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        ? 'https://'
        : 'http://';

    // Host (dominio + puerto)
    $host = $_SERVER['HTTP_HOST'];

    // Ruta absoluta del proyecto
    $projectPath = realpath(__DIR__ . '/..');

    // Ruta absoluta del DOCUMENT_ROOT
    $rootPath = realpath($_SERVER['DOCUMENT_ROOT']);

    // Calcular subcarpeta correctamente
    $subcarpeta = str_replace('\\', '/', $projectPath);
    $rootPath   = str_replace('\\', '/', $rootPath);

    $subcarpeta = str_replace($rootPath, '', $subcarpeta);

    // Asegurar que empieza con "/"
    $subcarpeta = '/' . ltrim($subcarpeta, '/');

    // Asegurar que NO termina con "/"
    return rtrim($protocolo . $host . $subcarpeta, '/');
}

// Genera rutas absolutas correctas para assets.
function asset(string $ruta): string
{
    // Asegura que base_url() NO termina con "/"
    $base = rtrim(base_url(), '/');

    // Asegura que la ruta SÍ empieza con "/"
    $ruta = '/' . ltrim($ruta, '/');

    return $base . $ruta;
}

// Registra eventos en un archivo de log con bloqueo de escritura.
function registrarLog(string $mensaje, string $nivel = 'INFO', ?int $usuarioId = null, ?string $modulo = null): void
{
    // Ruta del log
    $logDir = __DIR__ . '/../logs';
    $logFile = $logDir . '/app.log';

    if (!is_dir($logDir)) {
        @mkdir($logDir, 0775, true);
    }

    $fecha = date('Y-m-d H:i:s');
    $ip = obtenerIpCliente();
    $usuarioId = $usuarioId ?? (isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0);
    $modulo = $modulo ?? obtenerModuloActual();

    // Limpieza básica del mensaje para evitar saltos extraños
    $mensaje = str_replace(["\r", "\n"], ' ', $mensaje);

    $linea = sprintf("[%s] %s | %d | %s | %s | %s\n", $fecha, strtoupper($nivel), $usuarioId, $ip, $modulo, $mensaje);

    // Escritura con bloqueo para evitar condiciones de carrera
    $fh = @fopen($logFile, 'a');
    if ($fh) {
        @flock($fh, LOCK_EX);
        @fwrite($fh, $linea);
        @flock($fh, LOCK_UN);
        @fclose($fh);
    }
}

// IP del cliente considerando cabeceras comunes de proxies.
function obtenerIpCliente(): string
{
    $candidatas = [
        'HTTP_X_FORWARDED_FOR',
        'HTTP_CLIENT_IP',
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR',
    ];
    foreach ($candidatas as $key) {
        if (!empty($_SERVER[$key])) {
            // Tomar la primera IP si hay lista separada por comas
            $ip = explode(',', $_SERVER[$key])[0];
            return trim($ip);
        }
    }
    return '0.0.0.0';
}

// Determina el "módulo actual" para logging según el script.
function obtenerModuloActual(): string
{
    $script = isset($_SERVER['SCRIPT_NAME']) ? basename($_SERVER['SCRIPT_NAME']) : 'cli';
    return $script;
}

// Función para el sistema de mensajes de alerta modular
function mostrarAlerta(string $mensaje, string $tipo = 'success'): string
{
    $tipos = [
        'success' => [
            'icon' => 'fa-circle-check',
            'color' => 'var(--color-success)',
            'bg'    => 'var(--bg-success)'
        ],
        'error' => [
            'icon' => 'fa-circle-xmark',
            'color' => 'var(--color-danger)',
            'bg'    => 'var(--bg-danger)'
        ],
        'info' => [
            'icon' => 'fa-circle-info',
            'color' => 'var(--color-info)',
            'bg'    => 'var(--bg-info)'
        ],
        'warning' => [
            'icon' => 'fa-triangle-exclamation',
            'color' => 'var(--color-warning)',
            'bg'    => 'var(--bg-warning)'
        ]
    ];

    $t = $tipos[$tipo] ?? $tipos['success'];

    return '
        <div class="alerta-global" 
             style="
                background:' . $t['bg'] . ';
                border-left:4px solid ' . $t['color'] . ';
                color:' . $t['color'] . ';
             ">
            <i class="fa-solid ' . $t['icon'] . '"></i>
            ' . htmlspecialchars($mensaje) . '
        </div>
    ';
}

// Función para crear un sistema de paginación modular
function paginador($total_registros, $por_pagina, $pagina_actual, $filtros = [], $param_pagina = 'p')
{

    $total_paginas = max(1, ceil($total_registros / $por_pagina));

    // No queremos arrastrar el parámetro de página en los filtros
    unset($filtros[$param_pagina]);

    // Construir query string con el resto de filtros
    $query = '';
    if (!empty($filtros)) {
        $query = '&' . http_build_query($filtros);
    }

    $html = '<div class="paginacion">';

    // Anterior
    if ($pagina_actual > 1) {
        $html .= '<a class="btn-pag" href="?' . $param_pagina . '=' . ($pagina_actual - 1) . $query . '">Anterior</a>';
    }

    // Números
    for ($i = 1; $i <= $total_paginas; $i++) {
        $activo = ($i == $pagina_actual) ? 'activo' : '';
        $html .= '<a class="btn-pag ' . $activo . '" href="?' . $param_pagina . '=' . $i . $query . '">' . $i . '</a>';
    }

    // Siguiente
    if ($pagina_actual < $total_paginas) {
        $html .= '<a class="btn-pag" href="?' . $param_pagina . '=' . ($pagina_actual + 1) . $query . '">Siguiente</a>';
    }

    $html .= '</div>';

    return $html;
}

// Función para obtener un animal en adopción random para la página de inicio
function obtener_animal_adopcion_random(PDO $pdo)
{
    $sql = "
        SELECT 
            a.id,
            a.nombre,
            a.descripcion,

            r.nombre AS raza,
            e.nombre AS especie,

            (
                SELECT ruta
                FROM animales_fotos 
                WHERE id_animal = a.id AND es_principal = 1 
                LIMIT 1
            ) AS imagen_principal

        FROM animales a
        INNER JOIN razas_animales r ON r.id = a.id_raza
        INNER JOIN especies_animales e ON e.id = r.especie_id

        WHERE a.adoptable = 1
        ORDER BY RAND()
        LIMIT 1
    ";

    $stmt = $pdo->query($sql);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function obtener_animal_apadrinamiento_random(PDO $pdo)
{
    $sql = "
        SELECT
            a.id,
            a.nombre,
            a.historia,
            a.foto_principal,
            COALESCE(
                NULLIF(a.foto_principal, ''),
                (
                    SELECT ruta
                    FROM animales_fotos af
                    WHERE af.id_animal = a.id AND af.es_principal = 1
                    LIMIT 1
                )
            ) AS imagen_principal,
            e.nombre AS especie,
            r.nombre AS raza,
            (
                SELECT COUNT(*)
                FROM sponsors_animals sa
                WHERE sa.animal_id = a.id AND sa.estado = 'activo'
            ) AS total_padrinos
        FROM animals_sponsor a
        INNER JOIN especies_animales e ON a.especie_id = e.id
        LEFT JOIN razas_animales r ON a.raza_id = r.id
        WHERE a.estado = 'activo'
        ORDER BY RAND()
        LIMIT 1
    ";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    } catch (PDOException $e) {
        error_log('obtener_animal_apadrinamiento_random error: ' . $e->getMessage());
        return null;
    }
}

// Función para crear el slug de las fichas de apadrinamientos
function generarSlug($cadena)
{
    // Convertir a minúsculas
    $cadena = strtolower($cadena);

    // Reemplazar caracteres acentuados
    $acentos = [
        'á' => 'a',
        'é' => 'e',
        'í' => 'i',
        'ó' => 'o',
        'ú' => 'u',
        'ñ' => 'n',
        'ä' => 'a',
        'ë' => 'e',
        'ï' => 'i',
        'ö' => 'o',
        'ü' => 'u'
    ];
    $cadena = strtr($cadena, $acentos);

    // Reemplazar cualquier cosa que no sea letra, número o espacio por nada
    $cadena = preg_replace('/[^a-z0-9\s-]/', '', $cadena);

    // Reemplazar espacios múltiples por uno solo
    $cadena = preg_replace('/\s+/', ' ', $cadena);

    // Reemplazar espacios por guiones
    $cadena = str_replace(' ', '-', $cadena);

    // Eliminar guiones duplicados
    $cadena = preg_replace('/-+/', '-', $cadena);

    // Recortar guiones al inicio y final
    $cadena = trim($cadena, '-');

    return $cadena;
}

// Función para el logotipo de las plataformas de CrowdFounding
function obtenerLogoPlataforma($plataformas, $id)
{
    foreach ($plataformas as $p) {
        if ($p['id'] == $id) {
            return $p['logo'];
        }
    }
    return "";
}

// Función universal para cargar el editor visual de Quill
function editor_quill($nombreCampo, $valor = '')
{
    $id = htmlspecialchars($nombreCampo);

    return '
        <div class="quill-editor" data-target="descripcion" id="editor-descripcion"></div>
        <textarea id="descripcion" name="' . $id . '" class="editor-html form-control" style="display:none;">'
        . $valor .
        '</textarea>
    ';
}
