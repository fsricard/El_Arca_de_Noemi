-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 28-04-2026 a las 20:56:34
-- Versión del servidor: 8.4.3
-- Versión de PHP: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `estructura_completa`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adopciones`
--

CREATE TABLE `adopciones` (
  `id` int UNSIGNED NOT NULL,
  `id_animal` int UNSIGNED NOT NULL,
  `id_adoptante` int UNSIGNED DEFAULT NULL,
  `fecha_adopcion` date DEFAULT NULL,
  `estado` enum('pendiente','en_proceso','finalizada','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `notas` text COLLATE utf8mb4_unicode_ci,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adoptantes`
--

CREATE TABLE `adoptantes` (
  `id` int UNSIGNED NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `ciudad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provincia` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_postal` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `adoptantes_all`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `adoptantes_all` (
`activo` bigint
,`apellidos` varchar(150)
,`ciudad` varchar(255)
,`codigo_postal` varchar(255)
,`direccion` mediumtext
,`email` varchar(255)
,`id` int unsigned
,`id_formulario` int unsigned
,`nombre_completo` varchar(255)
,`origen` varchar(10)
,`provincia` varchar(255)
,`ruta_pdf` varchar(255)
,`telefono` varchar(255)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adoptantes_formulario`
--

CREATE TABLE `adoptantes_formulario` (
  `id` int UNSIGNED NOT NULL,
  `animal_id` int UNSIGNED NOT NULL,
  `adoptante_id` int UNSIGNED DEFAULT NULL,
  `nombre_completo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dni_pasaporte` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `edad` int DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `ciudad` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_postal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provincia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `animal_nombre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motivos_adopcion` text COLLATE utf8mb4_unicode_ci,
  `personas_en_casa` text COLLATE utf8mb4_unicode_ci,
  `familia_de_acuerdo` tinyint(1) DEFAULT NULL,
  `responsable_principal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ninos_tuvieron_animales` tinyint(1) DEFAULT NULL,
  `convivencia_ninos_opinion` text COLLATE utf8mb4_unicode_ci,
  `plan_familia_impacto` text COLLATE utf8mb4_unicode_ci,
  `alergias_en_casa` text COLLATE utf8mb4_unicode_ci,
  `capacidad_economica` tinyint(1) DEFAULT NULL,
  `asumir_gastos_vet` tinyint(1) DEFAULT NULL,
  `ha_tenido_animales` tinyint(1) DEFAULT NULL,
  `historia_animales_previos` text COLLATE utf8mb4_unicode_ci,
  `otros_animales` text COLLATE utf8mb4_unicode_ci,
  `chip_esterilizados` tinyint(1) DEFAULT NULL,
  `vacunas_en_regla` tinyint(1) DEFAULT NULL,
  `tipo_vivienda` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vivienda_propiedad` tinyint(1) DEFAULT NULL,
  `permite_animales_en_alquiler` tinyint(1) DEFAULT NULL,
  `patio_jardin_medidas` text COLLATE utf8mb4_unicode_ci,
  `interior_o_exterior` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profesion_situacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quien_asume_gastos` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tiempo_pasear` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `horas_solo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lugares_paseo` text COLLATE utf8mb4_unicode_ci,
  `mudanza_poblacion` text COLLATE utf8mb4_unicode_ci,
  `mudanza_pais` text COLLATE utf8mb4_unicode_ci,
  `vacaciones_cuidado` text COLLATE utf8mb4_unicode_ci,
  `por_que_adoptar` text COLLATE utf8mb4_unicode_ci,
  `tiempo_busqueda` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `como_conocio` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conoce_condiciones` tinyint(1) DEFAULT NULL,
  `firma_nombre_dni` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruta_pdf` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `procesado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_formulario` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `animales`
--

CREATE TABLE `animales` (
  `id` int UNSIGNED NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_raza` int UNSIGNED NOT NULL,
  `sexo` enum('macho','hembra','desconocido') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'desconocido',
  `edad` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `tamano` enum('pequeno','mediano','grande','muy_grande') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `estado_salud` text COLLATE utf8mb4_unicode_ci,
  `esterilizado` tinyint(1) NOT NULL DEFAULT '0',
  `vacunado` tinyint(1) NOT NULL DEFAULT '0',
  `desparasitado` tinyint(1) NOT NULL DEFAULT '0',
  `microchip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_ingreso` date NOT NULL,
  `fecha_rescate` date DEFAULT NULL,
  `adoptable` tinyint(1) NOT NULL DEFAULT '1',
  `id_adopcion` int UNSIGNED DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `animales_fotos`
--

CREATE TABLE `animales_fotos` (
  `id` int UNSIGNED NOT NULL,
  `id_animal` int UNSIGNED NOT NULL,
  `ruta` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_subida` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `animals_sponsor`
--

CREATE TABLE `animals_sponsor` (
  `id` int UNSIGNED NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `especie_id` int UNSIGNED NOT NULL,
  `raza_id` int UNSIGNED DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `foto_principal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mini_descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `historia` text COLLATE utf8mb4_unicode_ci,
  `slug` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('activo','oculto') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asi_es_noemi`
--

CREATE TABLE `asi_es_noemi` (
  `id` int NOT NULL,
  `titulo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenido` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `actualizado` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `crowdfunding_plataformas`
--

CREATE TABLE `crowdfunding_plataformas` (
  `id` int UNSIGNED NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `crowdfunding_recaudaciones`
--

CREATE TABLE `crowdfunding_recaudaciones` (
  `id` int UNSIGNED NOT NULL,
  `plataforma_id` int UNSIGNED NOT NULL,
  `cantidad_objetivo` decimal(10,2) NOT NULL,
  `moneda` enum('EUR','USD') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EUR',
  `cantidad_recaudada` decimal(10,2) DEFAULT NULL,
  `enlace` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especies_animales`
--

CREATE TABLE `especies_animales` (
  `id` int UNSIGNED NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intro_contacto`
--

CREATE TABLE `intro_contacto` (
  `id` int NOT NULL,
  `contenido` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `actualizado` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes_contacto`
--

CREATE TABLE `mensajes_contacto` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `asunto` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noemi_bichillos`
--

CREATE TABLE `noemi_bichillos` (
  `id` int UNSIGNED NOT NULL,
  `bichillo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noemi_frases`
--

CREATE TABLE `noemi_frases` (
  `id` int UNSIGNED NOT NULL,
  `frase` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `politica_privacidad`
--

CREATE TABLE `politica_privacidad` (
  `id` int NOT NULL,
  `contenido` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `actualizado` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `razas_animales`
--

CREATE TABLE `razas_animales` (
  `id` int UNSIGNED NOT NULL,
  `especie_id` int UNSIGNED NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sponsors`
--

CREATE TABLE `sponsors` (
  `id` int UNSIGNED NOT NULL,
  `nombre_apellidos` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sponsors_animals`
--

CREATE TABLE `sponsors_animals` (
  `id` int UNSIGNED NOT NULL,
  `sponsor_id` int UNSIGNED NOT NULL,
  `animal_id` int UNSIGNED NOT NULL,
  `paypal_subscription_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('activo','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `fecha_inicio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_cancelacion` datetime DEFAULT NULL,
  `nota` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sponsors_deleted`
--

CREATE TABLE `sponsors_deleted` (
  `id` int UNSIGNED NOT NULL,
  `nombre_apellidos` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `ciudad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provincia` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_postal` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pais` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci,
  `fecha_registro` datetime DEFAULT NULL,
  `fecha_eliminacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `datos_json` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sponsors_temp`
--

CREATE TABLE `sponsors_temp` (
  `id` int UNSIGNED NOT NULL,
  `animal_id` int UNSIGNED NOT NULL,
  `nombre_apellidos` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci,
  `paypal_subscription_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('pendiente','cancelado','confirmado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sponsor_payments`
--

CREATE TABLE `sponsor_payments` (
  `id` int UNSIGNED NOT NULL,
  `sponsor_id` int UNSIGNED NOT NULL,
  `relation_id` int UNSIGNED DEFAULT NULL,
  `subscription_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'EUR',
  `paypal_sale_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'completed',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clave` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('admin','visitante') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'visitante',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `adopciones`
--
ALTER TABLE `adopciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_animal` (`id_animal`),
  ADD KEY `id_adoptante` (`id_adoptante`);

--
-- Indices de la tabla `adoptantes`
--
ALTER TABLE `adoptantes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `adoptantes_formulario`
--
ALTER TABLE `adoptantes_formulario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_adoptante_formulario_adoptante` (`adoptante_id`);

--
-- Indices de la tabla `animales`
--
ALTER TABLE `animales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_raza` (`id_raza`),
  ADD KEY `id_adopcion` (`id_adopcion`);

--
-- Indices de la tabla `animales_fotos`
--
ALTER TABLE `animales_fotos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_fotos_animal` (`id_animal`);

--
-- Indices de la tabla `animals_sponsor`
--
ALTER TABLE `animals_sponsor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_animals_sponsor_slug` (`slug`),
  ADD KEY `idx_animals_sponsor_especie` (`especie_id`),
  ADD KEY `idx_animals_sponsor_raza` (`raza_id`);

--
-- Indices de la tabla `asi_es_noemi`
--
ALTER TABLE `asi_es_noemi`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `crowdfunding_plataformas`
--
ALTER TABLE `crowdfunding_plataformas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `crowdfunding_recaudaciones`
--
ALTER TABLE `crowdfunding_recaudaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_plataforma` (`plataforma_id`);

--
-- Indices de la tabla `especies_animales`
--
ALTER TABLE `especies_animales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `intro_contacto`
--
ALTER TABLE `intro_contacto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mensajes_contacto`
--
ALTER TABLE `mensajes_contacto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `noemi_bichillos`
--
ALTER TABLE `noemi_bichillos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `noemi_frases`
--
ALTER TABLE `noemi_frases`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `politica_privacidad`
--
ALTER TABLE `politica_privacidad`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `razas_animales`
--
ALTER TABLE `razas_animales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `especie_id` (`especie_id`,`nombre`);

--
-- Indices de la tabla `sponsors`
--
ALTER TABLE `sponsors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sponsors_email` (`email`),
  ADD KEY `idx_sponsors_nombre` (`nombre_apellidos`);

--
-- Indices de la tabla `sponsors_animals`
--
ALTER TABLE `sponsors_animals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sa_sponsor` (`sponsor_id`),
  ADD KEY `idx_sa_animal` (`animal_id`),
  ADD KEY `idx_sa_subscription` (`paypal_subscription_id`),
  ADD KEY `idx_sa_estado` (`estado`);

--
-- Indices de la tabla `sponsors_deleted`
--
ALTER TABLE `sponsors_deleted`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sponsors_temp`
--
ALTER TABLE `sponsors_temp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sponsors_temp_email` (`email`),
  ADD KEY `idx_sponsors_temp_animal` (`animal_id`);

--
-- Indices de la tabla `sponsor_payments`
--
ALTER TABLE `sponsor_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sp_sponsor` (`sponsor_id`),
  ADD KEY `idx_sp_relation` (`relation_id`),
  ADD KEY `idx_sp_subscription` (`subscription_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `adopciones`
--
ALTER TABLE `adopciones`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `adoptantes`
--
ALTER TABLE `adoptantes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `adoptantes_formulario`
--
ALTER TABLE `adoptantes_formulario`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `animales`
--
ALTER TABLE `animales`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `animales_fotos`
--
ALTER TABLE `animales_fotos`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `animals_sponsor`
--
ALTER TABLE `animals_sponsor`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asi_es_noemi`
--
ALTER TABLE `asi_es_noemi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `crowdfunding_plataformas`
--
ALTER TABLE `crowdfunding_plataformas`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `crowdfunding_recaudaciones`
--
ALTER TABLE `crowdfunding_recaudaciones`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `especies_animales`
--
ALTER TABLE `especies_animales`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `intro_contacto`
--
ALTER TABLE `intro_contacto`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mensajes_contacto`
--
ALTER TABLE `mensajes_contacto`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `noemi_bichillos`
--
ALTER TABLE `noemi_bichillos`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `noemi_frases`
--
ALTER TABLE `noemi_frases`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `politica_privacidad`
--
ALTER TABLE `politica_privacidad`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `razas_animales`
--
ALTER TABLE `razas_animales`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sponsors`
--
ALTER TABLE `sponsors`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sponsors_animals`
--
ALTER TABLE `sponsors_animals`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sponsors_temp`
--
ALTER TABLE `sponsors_temp`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sponsor_payments`
--
ALTER TABLE `sponsor_payments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Estructura para la vista `adoptantes_all`
--
DROP TABLE IF EXISTS `adoptantes_all`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `adoptantes_all`  AS SELECT `a`.`id` AS `id`, (concat(`a`.`nombre`,' ',`a`.`apellidos`) collate utf8mb4_unicode_ci) AS `nombre_completo`, (`a`.`apellidos` collate utf8mb4_unicode_ci) AS `apellidos`, `a`.`telefono` AS `telefono`, `a`.`email` AS `email`, `a`.`direccion` AS `direccion`, `a`.`ciudad` AS `ciudad`, `a`.`provincia` AS `provincia`, `a`.`codigo_postal` AS `codigo_postal`, ('manual' collate utf8mb4_unicode_ci) AS `origen`, 1 AS `activo`, NULL AS `id_formulario`, NULL AS `ruta_pdf` FROM `adoptantes` AS `a`union all select `f`.`id` AS `id`,(`f`.`nombre_completo` collate utf8mb4_unicode_ci) AS `nombre_completo`,('' collate utf8mb4_unicode_ci) AS `apellidos`,`f`.`telefono` AS `telefono`,`f`.`email` AS `email`,`f`.`direccion` AS `direccion`,`f`.`ciudad` AS `ciudad`,`f`.`provincia` AS `provincia`,`f`.`codigo_postal` AS `codigo_postal`,('formulario' collate utf8mb4_unicode_ci) AS `origen`,0 AS `activo`,`f`.`id` AS `id_formulario`,`f`.`ruta_pdf` AS `ruta_pdf` from `adoptantes_formulario` `f` where (`f`.`procesado` = 0)  ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `adopciones`
--
ALTER TABLE `adopciones`
  ADD CONSTRAINT `fk_adopciones_adoptante` FOREIGN KEY (`id_adoptante`) REFERENCES `adoptantes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_adopciones_animal` FOREIGN KEY (`id_animal`) REFERENCES `animales` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `adoptantes_formulario`
--
ALTER TABLE `adoptantes_formulario`
  ADD CONSTRAINT `fk_adoptante_formulario_adoptante` FOREIGN KEY (`adoptante_id`) REFERENCES `adoptantes` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `animales`
--
ALTER TABLE `animales`
  ADD CONSTRAINT `fk_animales_adopcion` FOREIGN KEY (`id_adopcion`) REFERENCES `adopciones` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_animales_raza` FOREIGN KEY (`id_raza`) REFERENCES `razas_animales` (`id`) ON DELETE RESTRICT;

--
-- Filtros para la tabla `animales_fotos`
--
ALTER TABLE `animales_fotos`
  ADD CONSTRAINT `fk_fotos_animal` FOREIGN KEY (`id_animal`) REFERENCES `animales` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `animals_sponsor`
--
ALTER TABLE `animals_sponsor`
  ADD CONSTRAINT `fk_sponsor_especie` FOREIGN KEY (`especie_id`) REFERENCES `especies_animales` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sponsor_raza` FOREIGN KEY (`raza_id`) REFERENCES `razas_animales` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `crowdfunding_recaudaciones`
--
ALTER TABLE `crowdfunding_recaudaciones`
  ADD CONSTRAINT `fk_plataforma` FOREIGN KEY (`plataforma_id`) REFERENCES `crowdfunding_plataformas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `razas_animales`
--
ALTER TABLE `razas_animales`
  ADD CONSTRAINT `fk_razas_especie` FOREIGN KEY (`especie_id`) REFERENCES `especies_animales` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sponsors_animals`
--
ALTER TABLE `sponsors_animals`
  ADD CONSTRAINT `fk_sa_animal` FOREIGN KEY (`animal_id`) REFERENCES `animals_sponsor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sa_sponsor` FOREIGN KEY (`sponsor_id`) REFERENCES `sponsors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sponsor_payments`
--
ALTER TABLE `sponsor_payments`
  ADD CONSTRAINT `fk_sp_relation` FOREIGN KEY (`relation_id`) REFERENCES `sponsors_animals` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sp_sponsor` FOREIGN KEY (`sponsor_id`) REFERENCES `sponsors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
