/*Base de datos en SQL Server*/
CREATE DATABASE XVEILEEN;
USE XVEILEEN;

CREATE TABLE USUARIO(
    telefono CHAR(11) NOT NULL,
    contrasena VARCHAR(20) NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    monto_total DECIMAL(18,2) NOT NULL,
    monto_pagado DECIMAL(18,2),
    monto_restante DECIMAL(18,2) NOT NULL,
    CONSTRAINT PK_TELEFONO PRIMARY KEY(telefono)
);

CREATE TABLE COMPROBANTE(
    id_comprobante INT IDENTITY(1,1),
    archivo_comprobante VARBINARY(MAX) NOT NULL,
    CONSTRAINT PK_COMPROBANTE PRIMARY KEY(id_comprobante)
);

CREATE TABLE COMPROBANTE_USUARIO(
    comprobante INT NOT NULL,
    telefono CHAR(11) NOT NULL,
    CONSTRAINT FK_COMPROBANTE_TELEFONO_comprobante FOREIGN KEY (comprobante) REFERENCES COMPROBANTE(id_comprobante),
    CONSTRAINT FK_COMPROBANTE_TELEFONO_telefono FOREIGN KEY (telefono) REFERENCES USUARIO(telefono),
    CONSTRAINT PK_COMPROBANTE_TELEFONO PRIMARY KEY(comprobante, telefono)
);


/*Base de datos en phpmyadmin*/
CREATE DATABASE XVEILEEN;
USE XVEILEEN;


CREATE TABLE USUARIO (
    telefono CHAR(11) NOT NULL,
    contrasena VARCHAR(20) NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    monto_total DECIMAL(18,2) NOT NULL,
    monto_pagado DECIMAL(18,2),
    monto_restante DECIMAL(18,2) NOT NULL,
    PRIMARY KEY (telefono)
);


CREATE TABLE COMPROBANTE (
    id_comprobante INT AUTO_INCREMENT,
    archivo_comprobante LONGBLOB NOT NULL,
    PRIMARY KEY (id_comprobante)
);


CREATE TABLE COMPROBANTE_USUARIO (
    comprobante INT NOT NULL,
    telefono CHAR(11) NOT NULL,
    FOREIGN KEY (comprobante) REFERENCES COMPROBANTE(id_comprobante),
    FOREIGN KEY (telefono) REFERENCES USUARIO(telefono),
    PRIMARY KEY (comprobante, telefono)
);