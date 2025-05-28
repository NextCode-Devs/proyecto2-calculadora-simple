-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-05-2025 a las 21:01:24
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `finanzas_personales`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuraciones`
--

CREATE TABLE `configuraciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL COMMENT 'NULL = configuración global',
  `clave` varchar(50) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuraciones`
--

INSERT INTO `configuraciones` (`id`, `usuario_id`, `clave`, `valor`, `actualizado_en`) VALUES
(1, NULL, 'limite_gastos_global', 5000.00, '2025-05-20 04:42:35'),
(2, NULL, 'habilitar_alertas', 1.00, '2025-05-20 04:42:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transacciones`
--

CREATE TABLE `transacciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('ingreso','gasto','pago') NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha` date NOT NULL DEFAULT curdate(),
  `categoria` varchar(50) DEFAULT 'otros',
  `fecha_programada` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL COMMENT 'Hash bcrypt generado por el backend',
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estructura Stand-in para la vista `vista_comparacion_mensual`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_comparacion_mensual` (
`usuario_id` int(11)
,`anio_actual` int(4)
,`mes_actual` int(2)
,`gasto_mes_actual` decimal(32,2)
,`gasto_mes_anterior` decimal(32,2)
,`diferencia` decimal(33,2)
,`porcentaje_cambio` decimal(39,2)
,`limite_gastos` decimal(10,2)
,`estado_limite` varchar(20)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_resumen_mensual`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_resumen_mensual` (
`usuario_id` int(11)
,`anio` int(4)
,`mes` int(2)
,`total_ingresos` decimal(32,2)
,`total_gastos` decimal(32,2)
,`balance` decimal(32,2)
,`limite_gastos` decimal(10,2)
,`alertas_habilitadas` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_comparacion_mensual`
--
DROP TABLE IF EXISTS `vista_comparacion_mensual`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_comparacion_mensual`  AS SELECT `a`.`usuario_id` AS `usuario_id`, `a`.`anio` AS `anio_actual`, `a`.`mes` AS `mes_actual`, `a`.`total_gastos` AS `gasto_mes_actual`, `b`.`total_gastos` AS `gasto_mes_anterior`, `a`.`total_gastos`- `b`.`total_gastos` AS `diferencia`, CASE WHEN `b`.`total_gastos` = 0 THEN NULL ELSE round((`a`.`total_gastos` - `b`.`total_gastos`) / `b`.`total_gastos` * 100,2) END AS `porcentaje_cambio`, `a`.`limite_gastos` AS `limite_gastos`, CASE WHEN `a`.`alertas_habilitadas` = 0 THEN 'ALERTAS DESACTIVADAS' WHEN `a`.`total_gastos` > `a`.`limite_gastos` THEN 'EXCEDIDO' WHEN `a`.`total_gastos` > `a`.`limite_gastos` * 0.8 THEN 'CERCA DEL LÍMITE' ELSE 'DENTRO DEL LÍMITE' END AS `estado_limite` FROM (`vista_resumen_mensual` `a` left join `vista_resumen_mensual` `b` on(`a`.`usuario_id` = `b`.`usuario_id` and (`a`.`anio` = `b`.`anio` and `a`.`mes` = `b`.`mes` + 1 or `a`.`anio` = `b`.`anio` + 1 and `a`.`mes` = 1 and `b`.`mes` = 12))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_resumen_mensual`
--
DROP TABLE IF EXISTS `vista_resumen_mensual`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_resumen_mensual`  AS SELECT `t`.`usuario_id` AS `usuario_id`, year(`t`.`fecha`) AS `anio`, month(`t`.`fecha`) AS `mes`, sum(case when `t`.`tipo` = 'ingreso' then `t`.`monto` else 0 end) AS `total_ingresos`, sum(case when `t`.`tipo` = 'gasto' then `t`.`monto` else 0 end) AS `total_gastos`, sum(case when `t`.`tipo` = 'ingreso' then `t`.`monto` else -`t`.`monto` end) AS `balance`, coalesce((select `c`.`valor` from `configuraciones` `c` where `c`.`usuario_id` = `t`.`usuario_id` and `c`.`clave` = 'limite_gastos_personal' limit 1),(select `c`.`valor` from `configuraciones` `c` where `c`.`usuario_id` is null and `c`.`clave` = 'limite_gastos_global' limit 1)) AS `limite_gastos`, (select `c`.`valor` from `configuraciones` `c` where `c`.`usuario_id` = `t`.`usuario_id` and `c`.`clave` = 'habilitar_alertas' limit 1) AS `alertas_habilitadas` FROM `transacciones` AS `t` GROUP BY `t`.`usuario_id`, year(`t`.`fecha`), month(`t`.`fecha`) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`,`clave`);

--
-- Indices de la tabla `transacciones`
--
ALTER TABLE `transacciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_transacciones_usuario` (`usuario_id`),
  ADD KEY `idx_transacciones_tipo` (`tipo`),
  ADD KEY `idx_transacciones_fecha` (`fecha`),
  ADD KEY `idx_transacciones_categoria` (`categoria`),
  ADD KEY `idx_transacciones_usuario_tipo_fecha` (`usuario_id`,`tipo`,`fecha`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `transacciones`
--
ALTER TABLE `transacciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  ADD CONSTRAINT `configuraciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `transacciones`
--
ALTER TABLE `transacciones`
  ADD CONSTRAINT `transacciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
