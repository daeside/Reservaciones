-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-10-2018 a las 15:58:32
-- Versión del servidor: 10.1.36-MariaDB
-- Versión de PHP: 5.6.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `reservaciones`
--

DROP DATABASE IF EXISTS `reservaciones`;
CREATE DATABASE IF NOT EXISTS `reservaciones` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;
USE `reservaciones`;

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `busquedas` ()  NO SQL
SELECT restaurante.nombre AS nombre_restaurante, mesas.capacidad, reservaciones.id, reservaciones.cuarto, reservaciones.nombre, reservaciones.comentarios, reservaciones.fecha, DATE_FORMAT(horario.hora, '%h:%i') as hora, mesas.numero FROM mesas INNER JOIN horarios_mesas INNER JOIN reservaciones INNER JOIN horario INNER JOIN restaurante WHERE reservaciones.estatus != 0 AND reservaciones.id_mesa = mesas.id AND reservaciones.id_horario = horario.id AND reservaciones.id_horario = horarios_mesas.id_horario AND mesas.id = horarios_mesas.id_mesa AND reservaciones.fecha >= CURDATE() AND reservaciones.estatus != 0 AND mesas.id_restaurante = restaurante.id AND reservaciones.id_restaurante = restaurante.id ORDER BY reservaciones.fecha, reservaciones.id_horario$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `cerrar_abrir_rest` (IN `tipo` INT, IN `idres` INT, IN `cierre` DATE, IN `apertura` DATE)  NO SQL
IF tipo = 1 THEN
DELETE FROM cierre_restaurante WHERE cierre_restaurante.id_restaurante = idres AND cierre_restaurante.fecha_cierre = cierre AND cierre_restaurante.fecha_apertura = apertura;
ELSE
INSERT INTO cierre_restaurante(cierre_restaurante.id_restaurante, cierre_restaurante.fecha_cierre, cierre_restaurante.fecha_apertura) VALUES(idres, cierre, apertura);
END IF$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `estatus_rest` (IN `restaurante` INT)  NO SQL
SELECT DISTINCT restaurante.nombre, cierre_restaurante.fecha_cierre AS cierre, cierre_restaurante.fecha_apertura AS apertura FROM cierre_restaurante INNER JOIN restaurante WHERE cierre_restaurante.id_restaurante = restaurante.id AND restaurante.id = restaurante ORDER BY cierre_restaurante.fecha_cierre$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `horarios_12` (IN `restaurante` INT)  NO SQL
IF restaurante = 3 THEN
SELECT id, DATE_FORMAT(hora, '%h:%i') as hora FROM horario WHERE horario.id_restaurante = 3;
ELSE
SELECT id, DATE_FORMAT(hora, '%h:%i') as hora FROM horario WHERE horario.id_restaurante = !3;
END IF$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `mesas_disponibles` (IN `id_restaurante` INT, IN `id_horario` INT, IN `fecha` DATE)  NO SQL
SELECT mesas.id, mesas.capacidad, mesas.numero FROM mesas INNER JOIN horarios_mesas WHERE mesas.id = horarios_mesas.id_mesa AND horarios_mesas.id_horario = id_horario AND mesas.id_restaurante = id_restaurante AND mesas.id NOT IN (SELECT reservaciones.id_mesa FROM mesas INNER JOIN horarios_mesas INNER JOIN reservaciones INNER JOIN horario WHERE reservaciones.fecha = fecha AND reservaciones.id_horario = id_horario AND reservaciones.id_restaurante = id_restaurante AND reservaciones.estatus != 0 AND reservaciones.id_mesa = mesas.id AND reservaciones.id_horario = horario.id AND reservaciones.id_horario = horarios_mesas.id_horario AND mesas.id = horarios_mesas.id_mesa)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `mesas_ocupadas` (IN `id_restaurante` INT, IN `fecha` DATE, IN `id_horario` INT)  NO SQL
SELECT reservaciones.id_mesa AS id, mesas.capacidad, reservaciones.id AS id_reserva, reservaciones.cuarto, reservaciones.nombre, reservaciones.comentarios, reservaciones.fecha, DATE_FORMAT(horario.hora, '%h:%i') as hora, mesas.numero FROM mesas INNER JOIN horarios_mesas INNER JOIN reservaciones INNER JOIN horario WHERE reservaciones.fecha = fecha AND reservaciones.id_horario = id_horario AND reservaciones.id_restaurante = id_restaurante AND reservaciones.estatus != 0 AND reservaciones.id_mesa = mesas.id AND reservaciones.id_horario = horario.id AND reservaciones.id_horario = horarios_mesas.id_horario AND mesas.id = horarios_mesas.id_mesa$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `reportes` (IN `restaurante` INT, IN `fecha` DATE, IN `tipo` INT)  NO SQL
IF tipo = 1 THEN
SELECT DATE_FORMAT(hora, '%h:%i') AS hora, reservaciones.id_mesa AS id_mesa, mesas.capacidad, reservaciones.id, reservaciones.cuarto, reservaciones.folio, reservaciones.nombre, reservaciones.comentarios, reservaciones.fecha, reservaciones.operador, usuarios.nombre AS nombre_ope, usuarios.apellido, reservaciones.fecha_reserva, mesas.numero FROM mesas INNER JOIN horarios_mesas INNER JOIN reservaciones INNER JOIN horario INNER JOIN usuarios WHERE reservaciones.id_mesa = mesas.id AND reservaciones.id_horario = horario.id AND reservaciones.id_mesa = horarios_mesas.id_mesa AND reservaciones.id_horario = horarios_mesas.id_horario AND reservaciones.id_restaurante = restaurante AND reservaciones.fecha_reserva = fecha AND reservaciones.estatus != 0 AND reservaciones.operador = usuarios.id ORDER BY horario.hora;
ELSE
SELECT DATE_FORMAT(hora, '%h:%i') AS hora, reservaciones.id_mesa AS id_mesa, mesas.capacidad, reservaciones.id, reservaciones.cuarto, reservaciones.folio, reservaciones.nombre, reservaciones.comentarios, reservaciones.fecha, reservaciones.operador, usuarios.nombre AS nombre_ope, usuarios.apellido, reservaciones.fecha_reserva, mesas.numero FROM mesas INNER JOIN horarios_mesas INNER JOIN reservaciones INNER JOIN horario INNER JOIN usuarios WHERE reservaciones.id_mesa = mesas.id AND reservaciones.id_horario = horario.id AND reservaciones.id_mesa = horarios_mesas.id_mesa AND reservaciones.id_horario = horarios_mesas.id_horario AND reservaciones.id_restaurante = restaurante AND reservaciones.estatus != 0 AND reservaciones.operador = usuarios.id ORDER BY reservaciones.fecha, horario.hora;
END IF$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `restaurantes_cerrados` (IN `fecha` DATE)  NO SQL
SELECT * FROM restaurante WHERE restaurante.id NOT IN (SELECT DISTINCT cierre_restaurante.id_restaurante FROM cierre_restaurante INNER JOIN restaurante WHERE restaurante.id = cierre_restaurante.id_restaurante AND cierre_restaurante.fecha_cierre <= fecha AND cierre_restaurante.fecha_apertura >= fecha)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `tickets` (IN `id` INT)  NO SQL
SELECT restaurante.nombre AS nombre_restaurante, reservaciones.id, reservaciones.fecha, reservaciones.cuarto, reservaciones.nombre, DATE_FORMAT(hora, '%h:%i') AS hora, mesas.numero AS id_mesa, usuarios.nombre AS nombre_ope, usuarios.apellido, reservaciones.comentarios FROM mesas INNER JOIN horarios_mesas INNER JOIN reservaciones INNER JOIN horario INNER JOIN usuarios INNER JOIN restaurante WHERE reservaciones.id_mesa = mesas.id AND reservaciones.id_horario = horario.id AND reservaciones.id_mesa = horarios_mesas.id_mesa AND reservaciones.id_horario = horarios_mesas.id_horario AND reservaciones.operador = usuarios.id AND reservaciones.id_restaurante = restaurante.id AND reservaciones.id = id$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cierre_restaurante`
--

CREATE TABLE `cierre_restaurante` (
  `id_restaurante` int(10) NOT NULL,
  `fecha_cierre` date NOT NULL,
  `fecha_apertura` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cierre_restaurante`
--

INSERT INTO `cierre_restaurante` (`id_restaurante`, `fecha_cierre`, `fecha_apertura`) VALUES
(5, '2018-10-31', '2018-11-03'),
(5, '2018-11-10', '2018-11-15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horario`
--

CREATE TABLE `horario` (
  `id` int(10) NOT NULL,
  `hora` time NOT NULL,
  `id_restaurante` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `horario`
--

INSERT INTO `horario` (`id`, `hora`, `id_restaurante`) VALUES
(1, '17:30:00', 0),
(2, '17:50:00', 0),
(3, '18:00:00', 3),
(4, '18:10:00', 0),
(5, '18:20:00', 3),
(6, '18:30:00', 0),
(7, '18:40:00', 3),
(8, '18:50:00', 0),
(9, '19:00:00', 3),
(10, '19:10:00', 0),
(11, '19:20:00', 3),
(12, '19:30:00', 0),
(13, '19:40:00', 3),
(14, '19:50:00', 0),
(15, '20:00:00', 3),
(16, '20:10:00', 0),
(17, '20:20:00', 3),
(18, '20:30:00', 0),
(19, '20:40:00', 3),
(20, '20:50:00', 0),
(21, '21:00:00', 3),
(22, '21:10:00', 0),
(23, '21:20:00', 3),
(24, '21:30:00', 0),
(25, '21:40:00', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios_mesas`
--

CREATE TABLE `horarios_mesas` (
  `id_mesa` int(10) NOT NULL,
  `id_horario` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `horarios_mesas`
--

INSERT INTO `horarios_mesas` (`id_mesa`, `id_horario`) VALUES
(1, 2),
(1, 6),
(1, 10),
(1, 12),
(1, 14),
(1, 18),
(1, 24),
(2, 1),
(2, 4),
(3, 6),
(3, 20),
(4, 1),
(4, 14),
(5, 4),
(5, 18),
(6, 8),
(6, 22),
(7, 1),
(7, 14),
(8, 10),
(8, 24),
(9, 4),
(9, 18),
(10, 10),
(10, 24),
(11, 2),
(11, 16),
(12, 6),
(12, 20),
(13, 2),
(13, 16),
(14, 1),
(14, 14),
(15, 12),
(16, 10),
(16, 24),
(17, 6),
(17, 20),
(18, 2),
(18, 16),
(19, 6),
(19, 20),
(20, 10),
(20, 24),
(21, 12),
(22, 2),
(22, 16),
(23, 8),
(23, 22),
(24, 12),
(25, 4),
(25, 18),
(26, 8),
(26, 22),
(27, 12),
(28, 8),
(28, 18),
(29, 4),
(29, 16),
(29, 22),
(30, 8),
(30, 12),
(31, 20),
(31, 24),
(32, 12),
(33, 2),
(33, 16),
(34, 10),
(34, 12),
(34, 14),
(35, 6),
(35, 14),
(35, 16),
(35, 20),
(36, 20),
(37, 10),
(38, 2),
(38, 16),
(39, 4),
(39, 18),
(40, 6),
(40, 20),
(41, 8),
(41, 22),
(42, 1),
(42, 14),
(43, 4),
(43, 18),
(44, 10),
(44, 24),
(45, 10),
(45, 24),
(46, 6),
(46, 20),
(47, 2),
(47, 16),
(48, 10),
(48, 24),
(49, 12),
(50, 2),
(50, 16),
(51, 12),
(52, 2),
(52, 16),
(53, 4),
(53, 18),
(54, 8),
(54, 22),
(55, 18),
(56, 8),
(56, 22),
(57, 12),
(58, 8),
(58, 22),
(59, 12),
(60, 1),
(60, 14),
(61, 5),
(61, 13),
(62, 17),
(62, 21),
(63, 7),
(64, 17),
(65, 9),
(66, 11),
(66, 23),
(67, 3),
(67, 23),
(67, 25),
(68, 19),
(69, 7),
(69, 19),
(70, 19),
(71, 9),
(72, 19),
(73, 3),
(73, 15),
(74, 3),
(74, 15),
(75, 11),
(75, 17),
(76, 25),
(77, 11),
(77, 23),
(78, 17),
(79, 3),
(80, 11),
(80, 23),
(81, 9),
(82, 25),
(83, 4),
(83, 6),
(84, 1),
(84, 2),
(84, 4),
(84, 6),
(85, 4),
(85, 18),
(86, 6),
(86, 20),
(87, 10),
(87, 24),
(88, 1),
(88, 14),
(89, 8),
(89, 22),
(90, 12),
(91, 6),
(91, 20),
(92, 12),
(93, 8),
(93, 22),
(94, 1),
(94, 14),
(95, 12),
(96, 2),
(96, 16),
(97, 10),
(97, 24),
(98, 8),
(98, 22),
(99, 4),
(99, 18),
(100, 1),
(100, 14),
(101, 4),
(101, 18),
(102, 6),
(102, 20),
(103, 2),
(103, 16),
(104, 10),
(104, 24),
(105, 1),
(105, 14),
(106, 4),
(106, 18),
(107, 4),
(107, 8),
(107, 22),
(108, 1),
(108, 14),
(109, 12),
(110, 4),
(110, 8),
(110, 18),
(111, 10),
(111, 24),
(112, 10),
(112, 24),
(113, 18),
(114, 8),
(114, 22),
(115, 16),
(115, 18),
(116, 10),
(116, 24),
(117, 2),
(117, 14),
(117, 16),
(118, 12),
(119, 6),
(119, 20),
(120, 1),
(120, 14),
(121, 6),
(121, 20),
(122, 1),
(122, 14),
(123, 8),
(123, 12),
(123, 22),
(124, 12),
(125, 10),
(125, 16),
(126, 8),
(126, 18),
(126, 22),
(127, 6),
(127, 20),
(128, 2),
(128, 16);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(10) NOT NULL,
  `id_restaurante` int(10) NOT NULL,
  `numero` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `capacidad` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `id_restaurante`, `numero`, `capacidad`) VALUES
(1, 1, '008', 2),
(2, 1, '009', 4),
(3, 1, '010', 4),
(4, 1, '011', 4),
(5, 1, '012', 4),
(6, 1, '014', 8),
(7, 1, '015', 8),
(8, 1, '016', 4),
(9, 1, '020', 6),
(10, 1, '021', 6),
(11, 1, '022', 6),
(12, 1, '023', 6),
(13, 1, '024', 4),
(14, 1, '025', 4),
(15, 1, '030', 2),
(16, 1, '031', 2),
(17, 1, '032', 4),
(18, 1, '040', 4),
(19, 1, '041', 4),
(20, 1, '042', 4),
(21, 1, '043', 4),
(22, 1, '045', 4),
(23, 1, '050', 6),
(24, 1, '051', 6),
(25, 1, '053', 6),
(26, 1, '054', 5),
(27, 1, '055', 5),
(28, 2, '006', 5),
(29, 2, '007', 2),
(30, 2, '008', 4),
(31, 2, '009', 2),
(32, 2, '010', 2),
(33, 2, '011', 2),
(34, 2, '012', 4),
(35, 2, '014', 4),
(36, 2, '015', 4),
(37, 2, '020', 4),
(38, 2, '022', 4),
(39, 2, '023', 4),
(40, 2, '031', 2),
(41, 2, '032', 2),
(42, 2, '040', 9),
(43, 2, '041', 5),
(44, 2, '042', 5),
(45, 2, '043', 5),
(46, 2, '050', 10),
(47, 2, '052', 2),
(48, 2, '053', 2),
(49, 2, '054', 2),
(50, 2, '060', 4),
(51, 2, '061', 4),
(52, 2, '063', 4),
(53, 2, '064', 4),
(54, 2, '065', 4),
(55, 2, '070', 4),
(56, 2, '071', 4),
(57, 2, '072', 4),
(58, 2, '080', 2),
(59, 2, '082', 2),
(60, 2, '091', 6),
(61, 3, '007', 10),
(62, 3, '008', 6),
(63, 3, '009', 4),
(64, 3, '010', 2),
(65, 3, '011', 4),
(66, 3, '012', 4),
(67, 3, '013', 2),
(68, 3, '015', 4),
(69, 3, '016', 2),
(70, 3, '020', 2),
(71, 3, '021', 2),
(72, 3, '022', 2),
(73, 3, '023', 2),
(74, 3, '024', 2),
(75, 3, '025', 2),
(76, 3, '033', 2),
(77, 3, '034', 2),
(78, 3, '035', 4),
(79, 3, '040', 4),
(80, 3, '041', 4),
(81, 3, '044', 4),
(82, 3, '045', 4),
(83, 4, '008', 2),
(84, 4, '009', 4),
(85, 4, '010', 2),
(86, 4, '011', 2),
(87, 4, '012', 2),
(88, 4, '014', 2),
(89, 4, '016', 2),
(90, 4, '017', 2),
(91, 4, '018', 2),
(92, 4, '021', 4),
(93, 4, '023', 4),
(94, 4, '024', 4),
(95, 4, '025', 4),
(96, 4, '026', 4),
(97, 4, '027', 4),
(98, 4, '028', 4),
(99, 4, '029', 4),
(100, 4, '030', 6),
(101, 4, '031', 4),
(102, 4, '032', 5),
(103, 4, '034', 8),
(104, 4, '035', 8),
(105, 5, '010', 4),
(106, 5, '011', 4),
(107, 5, '012', 4),
(108, 5, '014', 4),
(109, 5, '020', 6),
(110, 5, '022', 2),
(111, 5, '023', 6),
(112, 5, '027', 4),
(113, 5, '030', 5),
(114, 5, '031', 4),
(115, 5, '032', 4),
(116, 5, '033', 5),
(117, 5, '034', 5),
(118, 5, '036', 4),
(119, 5, '037', 4),
(120, 5, '040', 2),
(121, 5, '042', 4),
(122, 5, '043', 2),
(123, 5, '044', 4),
(124, 5, '045', 4),
(125, 5, '046', 2),
(126, 5, '047', 2),
(127, 5, '048', 8),
(128, 5, '049', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservaciones`
--

CREATE TABLE `reservaciones` (
  `id` int(10) NOT NULL,
  `folio` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `cuarto` int(5) NOT NULL,
  `nombre` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `id_restaurante` int(2) NOT NULL,
  `id_mesa` int(4) NOT NULL,
  `id_horario` int(10) NOT NULL,
  `fecha` date NOT NULL,
  `comentarios` text COLLATE utf8_spanish_ci NOT NULL,
  `estatus` int(1) NOT NULL,
  `fecha_reserva` date NOT NULL,
  `operador` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `reservaciones`
--

INSERT INTO `reservaciones` (`id`, `folio`, `cuarto`, `nombre`, `id_restaurante`, `id_mesa`, `id_horario`, `fecha`, `comentarios`, `estatus`, `fecha_reserva`, `operador`) VALUES
(1, '1', 123, 'SDFG', 1, 2, 1, '2018-10-30', 'SD', 1, '2018-10-30', 1),
(2, '1', 23, 'SDFG', 1, 14, 1, '2018-10-30', 'XFGHGF', 1, '2018-10-30', 1),
(3, '1', 1010, '23DS', 1, 7, 1, '2018-10-30', 'SDFG', 1, '2018-10-30', 1),
(4, '1', 1010, 'QER', 2, 44, 10, '2018-10-31', 'DG', 1, '2018-10-30', 1),
(5, '1', 1010, 'SDFJ', 4, 99, 4, '2018-11-01', 'QRTYUIOJHG', 1, '2018-10-30', 1),
(6, '1', 1010, 'JAIME', 1, 4, 1, '2018-10-31', 'ASDFGH', 0, '2018-10-31', 1),
(7, '0001221', 1, 'GSH', 1, 1, 6, '2018-10-31', 'SDF', 1, '2018-10-31', 1),
(8, '0001221', 1, 'GERENCIA', 1, 1, 2, '2018-11-01', 'SDFGH', 1, '2018-10-31', 1),
(9, '1', 2020, 'LUIS', 1, 14, 1, '2018-10-31', 'CVGFD', 0, '2018-10-31', 1),
(10, '1', 2020, 'LUIS', 5, 114, 8, '2018-10-31', 'ADF', 1, '2018-10-31', 1),
(11, '2345', 1, 'JAIME', 1, 5, 4, '2018-10-31', ',MNBVC', 1, '2018-10-31', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `restaurante`
--

CREATE TABLE `restaurante` (
  `id` int(10) NOT NULL,
  `nombre` varchar(50) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `restaurante`
--

INSERT INTO `restaurante` (`id`, `nombre`) VALUES
(1, 'Wayne\'s Boots'),
(2, 'La Piazza'),
(3, 'Sans Soucis'),
(4, 'Los Gallos'),
(5, 'Fisherman\'s');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) NOT NULL,
  `nombre` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `apellido` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `password` text COLLATE utf8_spanish_ci NOT NULL,
  `privilegios` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `password`, `privilegios`) VALUES
(1, 'admin', 'admin', '$2y$08$amw2K2FYTHV1TzZ4MVVae.9o9tlrXUvfVjNEHaDX85sjLhftJNX8i', 1),
(2, 'prueba', 'prueba', '$2y$08$SE9GcytaN3lJMHdTdkRBRe8ZI7XC5AGDlAOO6bwUVNQypPCVk8L7u', 2),
(3, 'lopez', 'lopez', '$2y$08$bFJjaFVyQ2wycHRndVkzS.J1LeyQUovchoHivYSWE5VtAYWUStNhO', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `horario`
--
ALTER TABLE `horario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reservaciones`
--
ALTER TABLE `reservaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `restaurante`
--
ALTER TABLE `restaurante`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `horario`
--
ALTER TABLE `horario`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT de la tabla `reservaciones`
--
ALTER TABLE `reservaciones`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `restaurante`
--
ALTER TABLE `restaurante`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
