CREATE DATABASE clinica
USE clinica;
-- --------------------------------------------------------
-- Base de datos: `clinica`
-- --------------------------------------------------------

-- Estructura de tabla: `doctor`
CREATE TABLE `doctor` (
  `doctor_id` INT(11) NOT NULL,
  `nombres` VARCHAR(255) NOT NULL,
  `apellidos` VARCHAR(255) NOT NULL,
  `especialidad` VARCHAR(100) NOT NULL,
  `telefono` VARCHAR(20) NOT NULL,
  `horario` VARCHAR(255) NOT NULL,
  `correo` VARCHAR(255) NOT NULL,
  `contrasena` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla: `doctor`
INSERT INTO `doctor` (`doctor_id`, `nombres`, `apellidos`, `especialidad`, `telefono`, `horario`, `correo`, `contrasena`) VALUES
(3, 'Jose', 'Mayhua', 'Dentista', '98584', '2pm - 4 pm', 'jose@gmail.com', '$2y$10$BAiJx80IGUnlIpwDKGsQHu1sQWXr1hYsvreuilALCNUzttPGuxAle');

-- Estructura de tabla: `login_doctor`
CREATE TABLE `login_doctor` (
  `doctor_id` INT(11) NOT NULL,
  `correo` VARCHAR(255) NOT NULL,
  `contrasena` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estructura de tabla: `login_usuario`
CREATE TABLE `login_usuario` (
  `usuario_id` INT(11) NOT NULL,
  `nombre_completo` VARCHAR(255) NOT NULL,
  `correo_electronico` VARCHAR(255) NOT NULL,
  `usuario` VARCHAR(50) NOT NULL,
  `contrasena` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla: `login_usuario`
INSERT INTO `login_usuario` (`usuario_id`, `nombre_completo`, `correo_electronico`, `usuario`, `contrasena`) VALUES
(1, '', '', '', '$2y$10$CgMVNEMLvixl65pA4LKnDeDDek4isEDDO5NQYR17tz.tu7x3e3QAe'),
(3, 'jose', 'joseadolfo@gmail.com', 'jose', '$2y$10$uT0xC9DuA10RlaAXE.wvQOanDNtPycUeqOxWsZ6zBloNdG55wpS3C'),
(4, 'fifi', 'fifi@gmail.com', 'fifi', '$2y$10$BekPyR90qrpyKjOuskcRZeY99wcUI8JMrndmwwg6UedG8ZTbx1rNC'),
(5, 'Neper', 'neper@gmail.com', 'neper', '$2y$10$HcVsAPLeuynfiP37.eRzhuE8jDksvIa2TNQp/4rc.6oEMefvpEjdq'),
(6, 'mayhua', 'mayhua@gmail.com', 'mayhua', '$2y$10$ySPaF9CPuqiRB.6r7T3lpuwH0iQNpfIUbEv8Ms8eLvVjahyKZq2Ny'),
(7, 'Jose Mayhua', 'josemayhua@gmail.com', 'josemayhua', '$2y$10$C05ABvx8PSSFwX/30CIDsesHFhV4LtoCmpmxPZ3drl2IMR9P8JG6S'),
(9, 'adolfo Mayhua palomino', 'joseadolfoo@gmail.com', 'josea', '$2y$10$Jpet8DrpegKlDJqUnz84.eI9yJN2Dy05MQJCV0gCb6WxPwLbONA1m'),
(10, 'henri Lopez Arias', 'henri@gmail.com', 'henri', '$2y$10$CoYcjNtibp8JvFaRMpEZUebDRkuIy4xoAfWmSCbrSEiYfH2nDcYlW'),
(11, 'Fernando alias Gatito', 'gato@gmail.com', 'gato', '$2y$10$.Hgdz9F2RZ0b3lVgiuJyCeYV5fZKVMqXuj8rKXmgZK8FMDIDyTViC'),
(12, 'Edison Junior Paucar', 'junior@gmail.com', 'junior', '$2y$10$IQG4mjpEKuRJtTaOJqb99eJM.ALiLkUbqloAYZYT/gEfOL9FcmQ7S');

-- Estructura de tabla: `registrar_citas`
CREATE TABLE `registrar_citas` (
  `cita_id` INT(11) NOT NULL,
  `dni` VARCHAR(20) NOT NULL,
  `fecha_nacimiento` DATE NOT NULL,
  `sexo` ENUM('M','F') NOT NULL,
  `direccion` VARCHAR(255) NOT NULL,
  `telefono` VARCHAR(20) NOT NULL,
  `especialidad` VARCHAR(100) NOT NULL,
  `usuario_id` INT(11) DEFAULT NULL,
  `doctor_id` INT(11) DEFAULT NULL,
  `insertar_nombre` VARCHAR(255) NOT NULL,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `estado` ENUM('Pendiente','Confirmada','Cancelada','Completada') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla: `registrar_citas`
INSERT INTO `registrar_citas` (`cita_id`, `dni`, `fecha_nacimiento`, `sexo`, `direccion`, `telefono`, `especialidad`, `usuario_id`, `doctor_id`, `insertar_nombre`, `fecha_creacion`, `estado`) VALUES
(5, '12334533', '2024-07-18', 'M', 'huansca', '987654321', 'dentista', 7, 3, 'Jose Mayhua', '2024-07-30 11:29:43', 'Confirmada'),
(6, '1234567', '2024-07-18', 'M', 'calle loreto', '1234211', 'dentista', 9, 3, 'adolfo Mayhua palomino', '2024-07-30 17:36:17', 'Pendiente'),
(13, '1234567234', '2024-07-24', 'M', 'calle loretos', '12342111', 'dentista', 9, 3, 'adolfo Mayhua palomino', '2024-07-30 17:58:09', 'Cancelada'),
(15, '123987', '2024-07-18', 'M', 'huancan', '1242421', 'Odontologo', 10, 3, 'henri Lopez Arias', '2024-07-30 18:58:30', 'Pendiente'),
(16, '73626860', '2005-01-30', 'M', 'calle real', '998161597', 'exodoncia', 11, 3, 'Fernando alias Gatito', '2024-07-31 02:35:17', 'Confirmada'),
(18, '718974772', '2004-01-31', 'M', 'calle real sin numero', '9257580411', 'ortodoxia', 12, 3, 'Edison Junior Paucar', '2024-07-31 20:26:50', 'Confirmada'),
(20, '81930495419254', '2025-05-31', 'M', 'dijsferwtf', '124312352135', 'Dentista', 3, 3, 'jose', '2025-05-08 20:09:11', 'Confirmada');

-- Estructura de tabla: `tabla_disponibilidad`
CREATE TABLE `tabla_disponibilidad` (
  `disponibilidad_id` INT(11) NOT NULL,
  `doctor_id` INT(11) DEFAULT NULL,
  `fecha` DATE NOT NULL,
  `hora_inicio` TIME NOT NULL,
  `hora_fin` TIME NOT NULL,
  `estado` ENUM('libre','ocupado','cita') DEFAULT 'libre'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla: `tabla_disponibilidad`
INSERT INTO `tabla_disponibilidad` (`disponibilidad_id`, `doctor_id`, `fecha`, `hora_inicio`, `hora_fin`, `estado`) VALUES
(2, 3, '2024-08-05', '00:00:00', '00:00:00', ''),
(3, 3, '2024-08-06', '00:00:00', '00:00:00', ''),
(4, 3, '2024-08-07', '00:00:00', '00:00:00', '');

-- Índices
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`doctor_id`),
  ADD UNIQUE KEY `correo` (`correo`);

ALTER TABLE `login_doctor`
  ADD PRIMARY KEY (`doctor_id`),
  ADD UNIQUE KEY `correo` (`correo`);

ALTER TABLE `login_usuario`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `correo_electronico` (`correo_electronico`);

ALTER TABLE `registrar_citas`
  ADD PRIMARY KEY (`cita_id`),
  ADD KEY `registrar_citas_ibfk_1` (`usuario_id`),
  ADD KEY `registrar_citas_ibfk_2` (`doctor_id`);

ALTER TABLE `tabla_disponibilidad`
  ADD PRIMARY KEY (`disponibilidad_id`),
  ADD KEY `tabla_disponibilidad_ibfk_1` (`doctor_id`);

-- AUTO_INCREMENT
ALTER TABLE `doctor`
  MODIFY `doctor_id` INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `login_usuario`
  MODIFY `usuario_id` INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE `registrar_citas`
  MODIFY `cita_id` INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

ALTER TABLE `tabla_disponibilidad`
  MODIFY `disponibilidad_id` INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- Claves foráneas
ALTER TABLE `login_doctor`
  ADD CONSTRAINT `login_doctor_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`doctor_id`);

ALTER TABLE `registrar_citas`
  ADD CONSTRAINT `registrar_citas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `login_usuario` (`usuario_id`),
  ADD CONSTRAINT `registrar_citas_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`doctor_id`);

ALTER TABLE `tabla_disponibilidad`
  ADD CONSTRAINT `tabla_disponibilidad_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`doctor_id`);

-- Final
COMMIT;

-- Configuración previa original restaurada
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
