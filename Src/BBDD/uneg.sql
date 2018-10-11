-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 25-07-2016 a las 16:47:44
-- Versión del servidor: 5.5.49-0+deb8u1
-- Versión de PHP: 5.6.23-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `uneg`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE IF NOT EXISTS `estudiantes` (
  `cedula` varchar(10) COLLATE utf8_spanish2_ci NOT NULL,
  `nombre` varchar(25) COLLATE utf8_spanish2_ci NOT NULL,
  `apellido` varchar(25) COLLATE utf8_spanish2_ci NOT NULL,
  `copias` int(11) NOT NULL DEFAULT '250',
  `clave` varchar(25) COLLATE utf8_spanish2_ci NOT NULL,
  `sede` varchar(25) COLLATE utf8_spanish2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`cedula`, `nombre`, `apellido`, `copias`, `clave`, `sede`) VALUES
('17999355', 'Jesus', 'Torres', 180, '123456', 'atlantico'),
('21249788', 'Jonathan', 'Cuotto', 200, '123456', 'atlantico'),
('24183392', 'Loyher', 'Velasquez', 150, '123456', 'atlantico'),
('24183684', 'Stalin', 'Sanchez', 100, '123456', 'atlantico'),
('25000123', 'Lola', 'Mento', 250, '123456', 'atlantico'),
('654321', 'Juan', 'Perez', 250, '123456', 'atlantico'),
('98754', 'Juana', 'Lopez', 250, '123456', 'atlantico');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotocopias`
--

CREATE TABLE IF NOT EXISTS `fotocopias` (
`id_fotocopia` int(11) NOT NULL,
  `cedula` varchar(10) COLLATE utf8_spanish2_ci NOT NULL,
  `copias` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `fotocopias`
--

INSERT INTO `fotocopias` (`id_fotocopia`, `cedula`, `copias`, `fecha`) VALUES
(1, '123456', 2, '2016-07-18 06:28:57'),
(2, '123456', 10, '2016-07-18 06:48:46'),
(3, '24183684', 78, '2016-07-25 12:40:58'),
(4, 'qqqqqq', 65, '2016-07-25 12:46:58'),
(5, '17999355', 98, '2016-07-25 12:49:20'),
(6, '24183684', 6, '2016-07-25 12:55:39'),
(7, 'wwwwww', 43, '2016-07-25 12:56:47'),
(8, '24183684', 32, '2016-07-25 12:57:15'),
(9, '123456', 25, '2016-07-25 16:30:08'),
(10, '123456', 25, '2016-07-25 16:31:03'),
(11, '123456', 500, '2016-07-25 16:31:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pendientes`
--

CREATE TABLE IF NOT EXISTS `pendientes` (
`id_pendiente` int(11) NOT NULL,
  `id_copia` int(11) NOT NULL,
  `accion` varchar(25) COLLATE utf8_spanish2_ci NOT NULL,
  `cedula` varchar(10) COLLATE utf8_spanish2_ci NOT NULL,
  `nombre` varchar(25) COLLATE utf8_spanish2_ci NOT NULL,
  `apellido` varchar(25) COLLATE utf8_spanish2_ci NOT NULL,
  `copias` int(11) NOT NULL,
  `clave` varchar(25) COLLATE utf8_spanish2_ci NOT NULL,
  `sede` varchar(25) COLLATE utf8_spanish2_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
 ADD PRIMARY KEY (`cedula`);

--
-- Indices de la tabla `fotocopias`
--
ALTER TABLE `fotocopias`
 ADD PRIMARY KEY (`id_fotocopia`);

--
-- Indices de la tabla `pendientes`
--
ALTER TABLE `pendientes`
 ADD PRIMARY KEY (`id_pendiente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `fotocopias`
--
ALTER TABLE `fotocopias`
MODIFY `id_fotocopia` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT de la tabla `pendientes`
--
ALTER TABLE `pendientes`
MODIFY `id_pendiente` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=29;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
