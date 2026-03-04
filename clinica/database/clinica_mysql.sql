-- ============================================================
--  BASE DE DATOS: CLÍNICA
--  Versión MySQL 8+ (convertido desde PostgreSQL)
--  Sistema Web Clínico - Producción
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS clinica_db;
CREATE DATABASE clinica_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE clinica_db;

-- ============================================================
--  TABLA: usuarios (autenticación del sistema)
-- ============================================================
CREATE TABLE usuarios (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol           ENUM('admin','medico','recepcionista') NOT NULL DEFAULT 'recepcionista',
    id_empleado   VARCHAR(20)  NULL,
    activo        TINYINT(1)   NOT NULL DEFAULT 1,
    ultimo_login  DATETIME     NULL,
    token_reset   VARCHAR(100) NULL,
    token_expira  DATETIME     NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_usuarios_email (email),
    INDEX idx_usuarios_rol   (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  R7 · AREA
-- ============================================================
CREATE TABLE area (
    cod_area     VARCHAR(20)  NOT NULL,
    nombre_area  VARCHAR(100) NOT NULL,
    CONSTRAINT pk_area     PRIMARY KEY (cod_area),
    CONSTRAINT uq_area_nom UNIQUE      (nombre_area)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  R1 · PERSONA  (superclase ISA)
-- ============================================================
CREATE TABLE persona (
    identificacion  VARCHAR(20)  NOT NULL,
    nombre          VARCHAR(100) NOT NULL,
    direccion       VARCHAR(200),
    telefono        VARCHAR(20),
    CONSTRAINT pk_persona PRIMARY KEY (identificacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  R2 · EMPLEADO  (subclase de PERSONA)
-- ============================================================
CREATE TABLE empleado (
    identificacion  VARCHAR(20)    NOT NULL,
    cargo           VARCHAR(80)    NOT NULL,
    fecha_ingreso   DATE           NOT NULL,
    salario         DECIMAL(12,2)  NOT NULL CHECK (salario >= 0),
    jefe_id         VARCHAR(20)    NULL,
    CONSTRAINT pk_empleado    PRIMARY KEY (identificacion),
    CONSTRAINT fk_emp_persona FOREIGN KEY (identificacion)
        REFERENCES persona(identificacion)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_emp_jefe    FOREIGN KEY (jefe_id)
        REFERENCES empleado(identificacion)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_emp_jefe (jefe_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  R3 · MEDICO  (subclase de EMPLEADO)
-- ============================================================
CREATE TABLE medico (
    identificacion  VARCHAR(20)  NOT NULL,
    especialidad    VARCHAR(100) NOT NULL,
    nro_licencia    VARCHAR(50)  NOT NULL,
    universidad     VARCHAR(150),
    disponible      TINYINT(1)   NOT NULL DEFAULT 1,
    CONSTRAINT pk_medico       PRIMARY KEY (identificacion),
    CONSTRAINT fk_med_empleado FOREIGN KEY (identificacion)
        REFERENCES empleado(identificacion)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT uq_med_licencia UNIQUE (nro_licencia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  R4 · ENFERMERA  (subclase de EMPLEADO)
-- ============================================================
CREATE TABLE enfermera (
    identificacion    VARCHAR(20)  NOT NULL,
    anios_experiencia INT          CHECK (anios_experiencia >= 0),
    tipo              ENUM('auxiliar','asistente','jefe') NOT NULL,
    cod_area          VARCHAR(20)  NOT NULL,
    CONSTRAINT pk_enfermera    PRIMARY KEY (identificacion),
    CONSTRAINT fk_enf_empleado FOREIGN KEY (identificacion)
        REFERENCES empleado(identificacion)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_enf_area     FOREIGN KEY (cod_area)
        REFERENCES area(cod_area)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_enf_area (cod_area)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  R5 · HABILIDAD_ENFERMERA
-- ============================================================
CREATE TABLE habilidad_enfermera (
    identificacion  VARCHAR(20)  NOT NULL,
    habilidad       VARCHAR(100) NOT NULL,
    CONSTRAINT pk_habilidad PRIMARY KEY (identificacion, habilidad),
    CONSTRAINT fk_hab_enf   FOREIGN KEY (identificacion)
        REFERENCES enfermera(identificacion)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  R6 · PACIENTE  (subclase de PERSONA)
-- ============================================================
CREATE TABLE paciente (
    identificacion   VARCHAR(20)  NOT NULL,
    nss              VARCHAR(20)  NOT NULL,
    fecha_nacimiento DATE,
    labor            VARCHAR(100),
    eps              VARCHAR(100),
    fecha_afiliacion DATE,
    CONSTRAINT pk_paciente    PRIMARY KEY (identificacion),
    CONSTRAINT fk_pac_persona FOREIGN KEY (identificacion)
        REFERENCES persona(identificacion)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT uq_pac_nss     UNIQUE (nss)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  R8 · CAMA
-- ============================================================
CREATE TABLE cama (
    nro_cama  INT          NOT NULL,
    cod_area  VARCHAR(20)  NOT NULL,
    estado    ENUM('libre','ocupada') NOT NULL DEFAULT 'libre',
    CONSTRAINT pk_cama      PRIMARY KEY (nro_cama, cod_area),
    CONSTRAINT fk_cama_area FOREIGN KEY (cod_area)
        REFERENCES area(cod_area)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_cama_area (cod_area)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  R9 · HISTORIA_CLINICA
-- ============================================================
CREATE TABLE historia_clinica (
    id_historia  INT          AUTO_INCREMENT,
    id_paciente  VARCHAR(20)  NOT NULL,
    CONSTRAINT pk_historia    PRIMARY KEY (id_historia),
    CONSTRAINT fk_hc_paciente FOREIGN KEY (id_paciente)
        REFERENCES paciente(identificacion)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT uq_hc_paciente UNIQUE (id_paciente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  R10 · CONSULTA
-- ============================================================
CREATE TABLE consulta (
    id_historia    INT           NOT NULL,
    nro_consulta   INT           NOT NULL,
    fecha_consulta DATE          NOT NULL,
    precio         DECIMAL(10,2) NOT NULL CHECK (precio >= 0),
    resumen        TEXT,
    id_medico      VARCHAR(20)   NOT NULL,
    diagnostico    TEXT,
    tratamiento    TEXT,
    CONSTRAINT pk_consulta    PRIMARY KEY (id_historia, nro_consulta),
    CONSTRAINT fk_cons_hc     FOREIGN KEY (id_historia)
        REFERENCES historia_clinica(id_historia)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_cons_medico FOREIGN KEY (id_medico)
        REFERENCES medico(identificacion)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_cons_fecha  (fecha_consulta),
    INDEX idx_cons_medico (id_medico)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  CITAS (nueva tabla para agendar)
-- ============================================================
CREATE TABLE cita (
    id_cita        INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente    VARCHAR(20)   NOT NULL,
    id_medico      VARCHAR(20)   NOT NULL,
    fecha_cita     DATE          NOT NULL,
    hora_cita      TIME          NOT NULL,
    motivo         VARCHAR(300),
    estado         ENUM('pendiente','confirmada','cancelada','completada') NOT NULL DEFAULT 'pendiente',
    notas          TEXT,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cita_pac   FOREIGN KEY (id_paciente)
        REFERENCES paciente(identificacion)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_cita_med   FOREIGN KEY (id_medico)
        REFERENCES medico(identificacion)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_cita_fecha    (fecha_cita),
    INDEX idx_cita_paciente (id_paciente),
    INDEX idx_cita_medico   (id_medico)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  R11 · ASIGNACION_CAMA
-- ============================================================
CREATE TABLE asignacion_cama (
    id_paciente      VARCHAR(20)  NOT NULL,
    nro_cama         INT          NOT NULL,
    cod_area         VARCHAR(20)  NOT NULL,
    fecha_asignacion DATE         NOT NULL,
    duracion_dias    INT          CHECK (duracion_dias > 0),
    CONSTRAINT pk_asig_cama PRIMARY KEY (id_paciente, nro_cama, cod_area, fecha_asignacion),
    CONSTRAINT fk_asig_pac  FOREIGN KEY (id_paciente)
        REFERENCES paciente(identificacion)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_asig_cama FOREIGN KEY (nro_cama, cod_area)
        REFERENCES cama(nro_cama, cod_area)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_asig_cama (nro_cama, cod_area)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  R12 · CAMPANIA
-- ============================================================
CREATE TABLE campania (
    cod_campania      VARCHAR(20)  NOT NULL,
    nombre            VARCHAR(150) NOT NULL,
    objetivo          TEXT,
    fecha_realizacion DATE         NOT NULL,
    id_medico_resp    VARCHAR(20)  NOT NULL,
    CONSTRAINT pk_campania    PRIMARY KEY (cod_campania),
    CONSTRAINT fk_camp_medico FOREIGN KEY (id_medico_resp)
        REFERENCES medico(identificacion)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_camp_medico (id_medico_resp),
    INDEX idx_camp_fecha  (fecha_realizacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  R13 · PARTICIPACION_CAMPANIA
-- ============================================================
CREATE TABLE participacion_campania (
    id_paciente   VARCHAR(20)  NOT NULL,
    cod_campania  VARCHAR(20)  NOT NULL,
    CONSTRAINT pk_part_camp PRIMARY KEY (id_paciente, cod_campania),
    CONSTRAINT fk_part_pac  FOREIGN KEY (id_paciente)
        REFERENCES paciente(identificacion)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_part_camp FOREIGN KEY (cod_campania)
        REFERENCES campania(cod_campania)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  VISTAS
-- ============================================================
CREATE VIEW v_medico_completo AS
SELECT
    p.identificacion, p.nombre, p.direccion, p.telefono,
    e.cargo, e.fecha_ingreso, e.salario, e.jefe_id,
    m.especialidad, m.nro_licencia, m.universidad, m.disponible
FROM persona p
JOIN empleado e ON e.identificacion = p.identificacion
JOIN medico   m ON m.identificacion = e.identificacion;

CREATE VIEW v_paciente_completo AS
SELECT
    p.identificacion, p.nombre, p.direccion, p.telefono,
    pa.nss, pa.fecha_nacimiento,
    TIMESTAMPDIFF(YEAR, pa.fecha_nacimiento, CURDATE()) AS edad,
    pa.labor, pa.eps, pa.fecha_afiliacion
FROM persona  p
JOIN paciente pa ON pa.identificacion = p.identificacion;

CREATE VIEW v_historia_consultas AS
SELECT
    p.nombre          AS nombre_paciente,
    pa.nss,
    hc.id_historia,
    c.nro_consulta,
    c.fecha_consulta,
    c.precio,
    c.resumen,
    c.diagnostico,
    c.tratamiento,
    mp.nombre         AS nombre_medico,
    m.especialidad
FROM paciente         pa
JOIN persona           p  ON  p.identificacion = pa.identificacion
JOIN historia_clinica  hc ON hc.id_paciente    = pa.identificacion
JOIN consulta          c  ON  c.id_historia    = hc.id_historia
JOIN medico            m  ON  m.identificacion = c.id_medico
JOIN persona           mp ON mp.identificacion = m.identificacion;

CREATE VIEW v_citas_completo AS
SELECT
    ci.id_cita, ci.fecha_cita, ci.hora_cita, ci.motivo, ci.estado,
    p.nombre AS nombre_paciente, pa.eps,
    mp.nombre AS nombre_medico, m.especialidad
FROM cita ci
JOIN paciente pa  ON pa.identificacion = ci.id_paciente
JOIN persona  p   ON  p.identificacion = pa.identificacion
JOIN medico   m   ON  m.identificacion = ci.id_medico
JOIN persona  mp  ON mp.identificacion = m.identificacion;

-- ============================================================
--  DATOS DE PRUEBA
-- ============================================================
INSERT INTO area VALUES
    ('PED', 'Pediatría'),
    ('URG', 'Urgencias'),
    ('MED', 'Medicina General'),
    ('PSI', 'Psicología'),
    ('CIR', 'Cirugía');

INSERT INTO persona VALUES
    ('EMP001', 'Dr. Carlos Rodríguez',  'Calle 5 #12-30, Cali',  '3101234567'),
    ('EMP002', 'Dra. Laura Martínez',   'Av. 6N #24-15, Cali',   '3209876543'),
    ('EMP003', 'Dr. Andrés Pérez',      'Cra 8 #45-20, Cali',    '3155556677'),
    ('EMP004', 'Enf. María González',   'Calle 15 #8-90, Cali',  '3001112233'),
    ('EMP005', 'Enf. Juliana Torres',   'Cra 3 #10-55, Cali',    '3124445566');

INSERT INTO empleado VALUES
    ('EMP001', 'Médico Especialista', '2018-03-15', 8500000.00, NULL),
    ('EMP002', 'Médico General',      '2020-07-01', 6800000.00, 'EMP001'),
    ('EMP003', 'Médico Cirujano',     '2015-11-20', 9200000.00, 'EMP001'),
    ('EMP004', 'Enfermera Jefe',      '2019-02-10', 4200000.00, 'EMP001'),
    ('EMP005', 'Enfermera Asistente', '2022-08-05', 3100000.00, 'EMP004');

INSERT INTO medico VALUES
    ('EMP001', 'Pediatría',        'LIC-00123', 'Universidad del Valle', 1),
    ('EMP002', 'Medicina General', 'LIC-00456', 'Universidad Javeriana', 1),
    ('EMP003', 'Cirugía General',  'LIC-00789', 'Universidad Nacional',  1);

INSERT INTO enfermera VALUES
    ('EMP004', 12, 'jefe',      'URG'),
    ('EMP005',  3, 'asistente', 'PED');

INSERT INTO habilidad_enfermera VALUES
    ('EMP004', 'Aplicar inyecciones'),
    ('EMP004', 'Administrar medicamentos'),
    ('EMP004', 'Curar heridas'),
    ('EMP004', 'Manejo de desfibrilador'),
    ('EMP005', 'Aplicar inyecciones'),
    ('EMP005', 'Curar heridas'),
    ('EMP005', 'Toma de signos vitales');

INSERT INTO persona VALUES
    ('PAC001', 'Juan Camilo Díaz',    'Cra 9 #33-12, Cali',   '3167778899'),
    ('PAC002', 'Ana Sofía Vargas',    'Calle 20 #5-44, Cali', '3012223344'),
    ('PAC003', 'Pedro Augusto Reyes', 'Av. 3N #18-07, Cali',  '3189990011');

INSERT INTO paciente VALUES
    ('PAC001', 'NSS-001-2024', '1990-05-14', 'Ingeniero',  'Sura',      '2010-01-15'),
    ('PAC002', 'NSS-002-2024', '1985-11-22', 'Docente',    'Compensar', '2015-06-01'),
    ('PAC003', 'NSS-003-2024', '2000-03-08', 'Estudiante', 'Sanitas',   '2020-09-20');

INSERT INTO cama VALUES
    (101, 'PED', 'libre'),
    (102, 'PED', 'ocupada'),
    (201, 'URG', 'libre'),
    (202, 'URG', 'ocupada'),
    (301, 'MED', 'libre');

INSERT INTO historia_clinica (id_paciente) VALUES
    ('PAC001'), ('PAC002'), ('PAC003');

INSERT INTO consulta VALUES
    (1, 1, '2025-11-10', 80000.00, 'Dolor de cabeza frecuente y fiebre',        'EMP001', 'Migraña tensional', 'Ibuprofeno 400mg cada 8h por 5 días'),
    (1, 2, '2025-12-05', 80000.00, 'Control post-tratamiento, evolución buena', 'EMP001', 'Migraña en remisión', 'Continuar tratamiento'),
    (2, 1, '2026-01-15', 65000.00, 'Revisión anual, paciente sana',             'EMP002', 'Sin hallazgos', 'Control en 6 meses'),
    (3, 1, '2026-02-01', 80000.00, 'Fractura de radio — trauma laboral',        'EMP003', 'Fractura radio distal', 'Inmovilización con yeso 4 semanas'),
    (3, 2, '2026-02-10', 80000.00, 'Control postoperatorio, evolución normal',  'EMP003', 'Fractura en consolidación', 'Fisioterapia');

INSERT INTO cita (id_paciente, id_medico, fecha_cita, hora_cita, motivo, estado, notas, created_at) VALUES
    ('PAC001', 'EMP001', CURDATE(), '09:00:00', 'Control rutinario', 'confirmada', NULL, NOW()),
    ('PAC002', 'EMP002', CURDATE(), '10:30:00', 'Chequeo general', 'pendiente', NULL, NOW()),
    ('PAC003', 'EMP003', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:00:00', 'Retiro de yeso', 'pendiente', NULL, NOW());

INSERT INTO asignacion_cama VALUES
    ('PAC001', 101, 'PED', '2025-11-10', 3),
    ('PAC003', 202, 'URG', '2026-02-01', 5),
    ('PAC001', 301, 'MED', '2025-12-05', 1);

INSERT INTO campania VALUES
    ('CAMP-01', 'Prevención Dengue 2025',       'Reducir casos de dengue en zona urbana', '2025-08-20', 'EMP001'),
    ('CAMP-02', 'Vacunación Antigripal 2025',    'Inmunizar adultos mayores',              '2025-09-15', 'EMP002'),
    ('CAMP-03', 'Salud Mental Comunitaria 2026', 'Detectar depresión y ansiedad',          '2026-01-28', 'EMP002');

INSERT INTO participacion_campania VALUES
    ('PAC001', 'CAMP-01'), ('PAC001', 'CAMP-02'), ('PAC001', 'CAMP-03'),
    ('PAC002', 'CAMP-02'),
    ('PAC003', 'CAMP-01');

-- Usuario admin por defecto (contraseña: Admin2024!)
-- Hash generado con password_hash('Admin2024!', PASSWORD_BCRYPT)
INSERT INTO usuarios (username, email, password_hash, rol) VALUES
    ('admin', 'admin@clinica.com', '$2y$10$UdVFWcRfVCeLbzlFAv/5AeBK2jw3SiCiUm7SZBhdp.YlSSeFSdcS.', 'admin');

-- ============================================================
--  FIN DEL SCRIPT
-- ============================================================
