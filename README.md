<p align="center">
  <img src="/img/logo_20260320_0001.png" width="200" alt="Logo Arca de Noemí">
</p>

# 🐾 El Arca de Noemí – Plataforma de Gestión y Adopción Animal

![Estado](https://img.shields.io/badge/estado-en%20desarrollo-orange)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?logo=mysql&logoColor=white)
![Licencia](https://img.shields.io/badge/Licencia-MIT-green)
![Contribuciones](https://img.shields.io/badge/Contribuciones-bienvenidas-blue)

---

Bienvenido al repositorio oficial del proyecto **El Arca de Noemí**, una plataforma web diseñada para apoyar la labor de asociaciones protectoras de animales mediante herramientas modernas, accesibles y pensadas para el día a día de voluntarios y adoptantes.

Este proyecto nace con un objetivo claro: **facilitar la gestión interna y mejorar la visibilidad de los animales en adopción**, ofreciendo una experiencia intuitiva, rápida y emocionalmente conectada.

---

## ✨ Características principales

- 🐶 Gestión completa de animales
- 📸 Galería de imágenes optimizada
- 🧬 Clasificación por especies y razas
- ❤️ Flujo de adopciones
- 🧩 Backend modular y escalable
- 📄 Paginador universal
- 🛠️ Panel de administración intuitivo

---

## 🐕‍🦺 Base de datos

El proyecto utiliza un esquema relacional optimizado para consultas rápidas y relaciones claras.

### 📌 Tablas principales del sistema

- usuarios  
  Gestión de cuentas internas: acceso al panel, roles y credenciales.

- mensajes_contacto  
  Mensajes enviados desde el formulario de contacto público.

- intro_contacto  
  Texto editable que aparece en la página de contacto.

- politica_privacidad  
  Contenido editable de la política de privacidad.

- noemi_frases  
  Frases personalizadas que se muestran en la web.

- noemi_bichillos  
  Elementos decorativos o textos usados en la interfaz.

- asi_es_noemi  
  Contenido editable de la sección institucional “Así es Noemí”.

- especies_animales  
  Lista de especies disponibles (perro, gato, etc.).

- razas_animales  
  Razas asociadas a cada especie.

- animales  
  Ficha completa del animal: datos, estado, salud, fechas, adoptabilidad.

- adoptantes  
  Información de personas interesadas o que han adoptado.

- adopciones  
  Registro de adopciones realizadas y su estado.

- animales_fotos  
  Galería de fotos de cada animal, incluyendo la foto principal.

- adoptantes_formulario  
  Datos enviados desde el formulario de adopción.

- adoptantes_all (vista)  
  Vista combinada para consultas rápidas de adoptantes y formularios.

- animals_sponsor  
  Relación entre animales y patrocinadores.

- sponsors  
  Patrocinadores activos registrados en el sistema.

- sponsors_temp  
  Datos temporales de patrocinadores antes de validarse.

- sponsors_animals  
  Relación N:N entre patrocinadores y animales.

- sponsor_payments  
  Historial de pagos de patrocinio.

- sponsors_deleted  
  Registro histórico de patrocinadores eliminados.

- crowdfunding_plataformas  
  Plataformas externas donde se realizan campañas de recaudación.

- crowdfunding_recaudaciones  
  Entradas de recaudación vinculadas a campañas y plataformas.

---

## 📁 Estructura de carpetas del proyecto

```text
(raíz del proyecto)  
  admin/  
    css/
    includes/  
    modulos/
      adopciones/
        ajax/
      apadrinamientos/
        ajax/
      asi_es_noemi/
      bichillos/
      contacto/
      crowdfundig/
      logs/
        ajax/
      noemi_dice/
      politica/
      registros/
        ajax/
      tabla_base_datos/
      usuarios/
        ajax/
  ajax/
  config/  
    database.php  
    .env  
    envLoader.php  
  css/
    fontawesome/
      css/
      webfonts/
  img/
  includes/
    aside/
    docs/
    fpdf/
      doc/
      font/
      makefont/
      tutorial/
    PHPMailer/
    plantillas_email/
      contacto/
  js/
  logs/  
  uploads/
    adopciones/
    apadrinamientos/
    bichillos/
    crowdfunding/
  views/
README.md
index.php
.htaccess
```

_(Los archivos sensibles .env, database.php y envLoader.php no se incluyen en el repositorio.)_

---

## 🐾 Base de datos para colaboradores

Para facilitar el desarrollo en local, el proyecto incluye un archivo SQL con **toda la estructura de la base de datos**, sin datos sensibles y listo para importar.

### 📥 Archivo disponible

Ruta dentro del repositorio:

<a href="/database/estructura_completa.sql" target="_blank">Base de datos</a>

### 📌 ¿Qué contiene?

- Todas las tablas del proyecto  
- Todas las claves foráneas  
- La vista `adoptantes_all`  
- Relaciones circulares resueltas  
- Codificación `utf8mb4`  
- Estructura limpia y ordenada  

### 🧩 Cómo importarlo

1. Crear una base de datos vacía:

CREATE DATABASE noemi_y_su_arca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ciç

2. Importar el archivo:

En phpMyAdmin:  

- Seleccionar la base de datos  
- Ir a **Importar**  
- Subir `estructura_completa.sql`

O por terminal:

mysql -u usuario -p noemi_y_su_arca < database/estructura_completa.sql

### ✔️ Después de importar

Crear los archivos locales:

```text
/config/.env 
/config/database.php 
/config/envLoader.php
```

*(Estos archivos no se incluyen en el repositorio por seguridad.)*
---

## 🔧 Instalación avanzada (para colaboradores)

Esta guía permite configurar el proyecto en local con la misma estructura que el entorno principal, sin exponer archivos sensibles.

### 1️⃣ Archivos que deben crearse manualmente (no incluidos en el repositorio)

Dentro de la carpeta config/, crear:

### ✔️ config/.env

Archivo con credenciales privadas.

Ejemplo recomendado:

```php
DB_HOST=localhost  
DB_NAME=nombre_base_datos  
DB_USER=usuario  
DB_PASS=contraseña  
DB_CHARSET=utf8mb4

SMTP_HOST=smtp.servidor.com  
SMTP_PORT=587  
SMTP_SECURE=tls  
SMTP_USER_CONTACTO=tu_correo  
SMTP_PASS_CONTACTO=tu_contraseña
```

---

### ✔️ config/envLoader.php  
Archivo para cargar variables de entorno desde .env.

```php
<?php  
function cargarEnv($ruta) {  
    if (!file_exists($ruta)) return;  

    $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);  
    foreach ($lineas as $linea) {  
        if (strpos(trim($linea), '#') === 0) continue;  
        list($nombre, $valor) = explode('=', $linea, 2);  
        putenv(trim($nombre) . '=' . trim($valor));  
    }  
}  
```

---

### ✔️ config/database.php

Archivo que carga el .env y crea la conexión PDO.

Contenido recomendado:

```php
<?php
$env = parse_ini_file(__DIR__ . '/.env');

try {
    $pdo = new PDO(
        "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset={$env['DB_CHARSET']}",
        $env['DB_USER'],
        $env['DB_PASS'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
```

---

### 2️⃣ Crear la base de datos

CREATE DATABASE arca_noemi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

Importar el archivo SQL del proyecto:

mysql -u usuario -p noemi_y_su_arca < database/estructura_completa.sql

---

### 3️⃣ Configurar el entorno local

- Crear los archivos .env, database.php y envLoader.php  
- Ajustar credenciales  
- Verificar permisos de escritura en:  
  - img/  
  - logs/  
  - uploads/ (si existe)

---

### 4️⃣ Iniciar el servidor

php -S localhost:8081 -t public

O configurar Apache/Nginx con DocumentRoot → public.

---

### 5️⃣ Verificar instalación

Abrir en el navegador:

http://localhost:8081

Si carga la página principal sin errores, el entorno está listo.

---

## 🚀 Tecnologías utilizadas

- PHP 8+  
- MySQL / MariaDB  
- HTML5 + CSS3  
- JavaScript  
- MobileDetect  
- Quill Editor  
- Arquitectura modular propia  

---

## 🧪 Estado del proyecto

En desarrollo activo.

Actualmente se están implementando:

- Mejoras en el flujo de adopciones  
- Optimización del backend para Noemí  
- Integración universal del editor Quill  
- Nuevos filtros y vistas para listados de animales  

---

## 🤝 Colaboración

1. Haz un fork del repositorio  
2. Crea una rama con tu mejora  
3. Envía un pull request  

---

## 📬 Contacto

- Ricard FS – Desarrollo y arquitectura  
- Noemí – Coordinación y validación funcional  

---

## 👹 Autor

- Ricard FS, creador de:

1. El Arca de Noemí (En desarrollo)
2. La Gatopía de Miriam 
3. El Diablillo Sarcástico (En desarrollo)

---

## 🐾 Licencia

Este proyecto se distribuye bajo licencia MIT.
