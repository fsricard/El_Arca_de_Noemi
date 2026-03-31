<?php
// Función para restringir contenido solo para el rol "admin"
function tienePermiso(): bool {
    $rolesPermitidos = ['admin']; 
    
    return in_array($_SESSION['rol'], $rolesPermitidos, true);
}

// Función para detectar dispositos móviles
function esSoloMovil() {
    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
    return preg_match('/(android.*mobile|iphone|ipod|blackberry|windows phone|webos)/i', $ua);
}

// Función para imprimir los textos dinámicos en el header del BackEnd
function tituloPagina($pagina) {
    // Array asociativo de títulos
    $titulos = [
        'logs'                                      => 'Registro de logs',
        'contacto'                                  => 'Mensajes de contacto',
        'usuarios'                                  => 'Gestión de Usuarios',
        'dashboard'                                 => 'Panel de Control',
        'noemi_dice'                                => 'Frases de Noemí',
        'asi_es_noemi'                              => 'Así es Noemí',
        'contacto_intro'                            => 'Invocación al contacto',
        'noemi_bichillos'                           => 'Gestión de los bichillos de Noemí',
        'tablas_de_datos'                           => 'Tablas de la base de datos',
        'adopciones_editar'                         => 'Editar adopción',
        'noemi_dice_listado'                        => 'Listado de las frases de Noemí',
        'adopciones_listado'                        => 'Listado de adopciones',
        'tablas_de_datos_ver'                       => 'Contenido de la tabla de la base datos',
        'adopciones_adoptante'                      => 'Incluir un nuevo adoptante',
        'adopciones_adoptante'                      => 'Incluir un nuevo adoptante',
        'politica_de_privacidad'                    => 'Política de privacidad',
        'adopciones_incluir_raza'                   => 'Incluir nuevo animal',
        'noemi_bichillos_listado'                   => 'Listado de los bichillos de Noemí',
        'sistema_adopciones_crear'                  => 'Crear nueva adopción',
        'adopciones_incluir_animal'                 => 'Gestión de Adopciones',
        'sistema_adopciones_iniciar'                => 'Iniciar nueva adopción',
        'sistema_adopciones_por_adoptante'          => 'Adopciones por adoptante',
        'sistema_adopciones_editar_adoptante'       => 'Edición de adoptantes',
        'sistema_adopciones_listado_adoptantes'     => 'Listado de adoptantes'
    ];

    // Si existe en el array, devolvemos el título; si no, uno genérico
    return $titulos[$pagina] ?? 'Administración';
}

// Función para imprimir textos personalizados en "header.php" del FrontEnd
function mostrarTextoPersonalizado() {
    // Recupera la ruta desde la variable global
    $pagina = $GLOBALS['pagina_actual'] ?? '';

    // Define los textos personalizados
    $textos = [
        ''                          => 'El Arca de Noemi',
        '404'                       => '!!Vaya por Dios¡¡, que situación más vergonzosa',
        'inicio'                    => '',
        'contacto'                  => 'Contacta con Noemi',
        'asi-es-noemi'              => '',
        'politica-de-privacidad'    => 'Política de privacidad',
    ];

    // Imprime el texto correspondiente o uno por defecto
    echo $textos[$pagina] ?? 'El Arca de Noemi';
}

// Función para mostrar el CopyRight en el footer
function CopyrightRicardFS($startYear = 2024) {
    $currentYear = date('Y');
    $yearDisplay = ($startYear == $currentYear) ? $currentYear : "$startYear – $currentYear";
    return "&copy; $yearDisplay El Arca de Noemí - Todos los derechos reservados";
}

// Función para las frases cortas de Noemí
function noemi_frase_random(PDO $pdo): string {
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
function noemi_bichillos_random(PDO $pdo): string {
    $stmt = $pdo->query("
        SELECT bichillo 
        FROM noemi_bichillos 
        WHERE activo = 1 
        ORDER BY RAND() 
        LIMIT 1
    ");

    return $stmt->fetchColumn() ?: 'Todos los bichillos de Noemí están descansando ahora mismo...';
}

// Función para crear rutas absolutas
function base_url(): string {
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];

    // Detectar la carpeta raíz del proyecto
    // DOCUMENT_ROOT = /var/www/html
    // __DIR__ = /var/www/html/config
    // Resultado = / (raíz del proyecto)
    $rootPath = realpath($_SERVER['DOCUMENT_ROOT']);
    $projectPath = realpath(__DIR__ . '/..');

    // Calcular subcarpeta si el proyecto no está en la raíz del servidor
    $subcarpeta = str_replace($rootPath, '', $projectPath);

    return rtrim($protocolo . $host . $subcarpeta, '/');
}

// Genera rutas absolutas correctas para assets.
function asset(string $ruta): string {
    return base_url() . '/' . ltrim($ruta, '/');
}

// Registra eventos en un archivo de log con bloqueo de escritura.
function registrarLog(string $mensaje, string $nivel = 'INFO', ?int $usuarioId = null, ?string $modulo = null): void {
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
function obtenerIpCliente(): string {
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
function obtenerModuloActual(): string {
    $script = isset($_SERVER['SCRIPT_NAME']) ? basename($_SERVER['SCRIPT_NAME']) : 'cli';
    return $script;
}

// Función para el sistema de mensajes de alerta modular
function mostrarAlerta(string $mensaje, string $tipo = 'success'): string {
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
function paginador($total_registros, $por_pagina, $pagina_actual, $filtros = [], $param_pagina = 'p') {

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