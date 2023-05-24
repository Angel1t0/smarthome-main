-- Base de datos: `focos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bulb`
--

CREATE TABLE `bulb` (
  `id` int(11) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` bit(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `bulb`
--

INSERT INTO `bulb` (`id`, `location`, `name`, `status`) VALUES
(1, 'Sala', 'Foco 3', b'1'),
(2, 'Sala', 'dos', b'0'),
(3, 'Sala', 'dos', b'0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `user` varchar(255) DEFAULT NULL,
  `pass` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`user`, `pass`) VALUES
('admin', 'admin123'),
('demo', '{bcrypt}$2a$10$qhp8wn2miOh.iHcsC8jNCeLX3jNiBI5wcq0a7iTrz9c/or6BNesDO'),
('user', '123456789');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bulb`
--
ALTER TABLE `bulb`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bulb`
--
ALTER TABLE `bulb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12234;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
