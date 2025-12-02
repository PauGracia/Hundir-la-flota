-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS hundir_flota;
USE hundir_flota;

-- Tabla: usuario
CREATE TABLE IF NOT EXISTS usuario (
    id INT NOT NULL AUTO_INCREMENT,
    nombreUsuario VARCHAR(50) NOT NULL UNIQUE,
    pass VARCHAR(255) NOT NULL,
    imagenPerfil VARCHAR(255) DEFAULT 'assets/img/default-avatar.png',
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    victorias INT DEFAULT 0,
    PRIMARY KEY (id)
);

-- Tabla: almirantes
CREATE TABLE IF NOT EXISTS almirantes (
    id INT NOT NULL AUTO_INCREMENT,
    nombreAlmirante VARCHAR(75) NOT NULL,
    imagenAlmirante VARCHAR(100) DEFAULT NULL,
    victorias INT DEFAULT 0,
    PRIMARY KEY (id)
);

-- Tabla: partidas
CREATE TABLE IF NOT EXISTS partidas (
    idPartida INT NOT NULL AUTO_INCREMENT,
    nombreUsuario VARCHAR(50) NOT NULL,
    nombreOponente VARCHAR(50) DEFAULT NULL,
    idAlmirante INT DEFAULT NULL,
    estado ENUM('colocando','batalla','finalizada') DEFAULT 'colocando',
    ganador VARCHAR(50) DEFAULT NULL,
    puntos INT DEFAULT 0,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    flotaJugador TEXT DEFAULT NULL,
    flotaEnemigo TEXT DEFAULT NULL,
    estadoTablero TEXT DEFAULT NULL,
    tiempo INT DEFAULT 0,
    PRIMARY KEY (idPartida),
    KEY nombreUsuario_idx (nombreUsuario),
    KEY idAlmirante_idx (idAlmirante),
    CONSTRAINT fk_partidas_almirantes FOREIGN KEY (idAlmirante) REFERENCES almirantes(id)
);

-- Tabla: barcos
CREATE TABLE IF NOT EXISTS barcos (
    idBarco INT NOT NULL AUTO_INCREMENT,
    idPartida INT NOT NULL,
    propietario ENUM('jugador','enemigo') DEFAULT NULL,
    tipoBarco ENUM('portaaviones','acorazado','destructor','fragata','corbeta') DEFAULT NULL,
    size INT NOT NULL,
    ancho INT NOT NULL,
    alto INT NOT NULL,
    orientacion ENUM('horizontal','vertical') NOT NULL,
    xInicio INT NOT NULL,
    yInicio INT NOT NULL,
    danio INT DEFAULT 0,
    PRIMARY KEY (idBarco),
    KEY idPartida_idx (idPartida),
    CONSTRAINT fk_barcos_partidas FOREIGN KEY (idPartida) REFERENCES partidas(idPartida)
);

-- Tabla: disparos
CREATE TABLE IF NOT EXISTS disparos (
    idDisparo INT NOT NULL AUTO_INCREMENT,
    idPartida INT NOT NULL,
    propietario ENUM('jugador','enemigo') DEFAULT NULL,
    posX INT NOT NULL,
    posY INT NOT NULL,
    resultado ENUM('agua','tocado','hundido') DEFAULT NULL,
    PRIMARY KEY (idDisparo),
    KEY idPartida_idx (idPartida),
    CONSTRAINT fk_disparos_partidas FOREIGN KEY (idPartida) REFERENCES partidas(idPartida)
);
