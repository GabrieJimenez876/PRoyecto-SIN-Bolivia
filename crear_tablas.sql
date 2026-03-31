CREATE TABLE usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    rol ENUM('cliente','soporte_n1','soporte_n2','soporte_n3','admin'),
    estado BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE incidentes (
    id_incidente INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(200),
    descripcion TEXT,
    prioridad ENUM('baja','media','alta','critica'),
    estado ENUM('abierto','en_proceso','resuelto','cerrado'),
    id_usuario INT,
    fecha_creacion TIMESTAMP,
    fecha_cierre TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

CREATE TABLE tickets (
    id_ticket INT PRIMARY KEY AUTO_INCREMENT,
    id_incidente INT,
    nivel ENUM('nivel1','nivel2','nivel3'),
    asignado_a INT,
    estado ENUM('pendiente','en_atencion','resuelto'),
    fecha_asignacion TIMESTAMP,
    FOREIGN KEY (id_incidente) REFERENCES incidentes(id_incidente),
    FOREIGN KEY (asignado_a) REFERENCES usuarios(id_usuario)
);

CREATE TABLE historial_incidente (
    id_historial INT PRIMARY KEY AUTO_INCREMENT,
    id_incidente INT,
    accion TEXT,
    fecha TIMESTAMP,
    id_usuario INT,
    FOREIGN KEY (id_incidente) REFERENCES incidentes(id_incidente),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

CREATE TABLE sla_metricas (
    id_sla INT PRIMARY KEY AUTO_INCREMENT,
    id_incidente INT,
    tiempo_respuesta INT, -- en minutos
    tiempo_resolucion INT,
    cumplido BOOLEAN,
    FOREIGN KEY (id_incidente) REFERENCES incidentes(id_incidente)
);

CREATE TABLE base_conocimiento (
    id_articulo INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(200),
    descripcion TEXT,
    solucion TEXT,
    fecha_creacion TIMESTAMP,
    creado_por INT,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id_usuario)
);

CREATE TABLE categorias_incidente (
    id_categoria INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100)
);

ALTER TABLE incidentes ADD id_categoria INT;
ALTER TABLE incidentes 
ADD FOREIGN KEY (id_categoria) REFERENCES categorias_incidente(id_categoria);
