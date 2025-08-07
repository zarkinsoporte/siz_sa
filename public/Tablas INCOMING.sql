Tablas INCOMING
1. Siz_Incoming (Inspecci�n de Materiales)
Esta tabla ser� el registro principal de cada inspecci�n de material recibida en una Nota de Entrada. Representa una l�nea de material de la consulta GETENTRADAMATERIAL_CALIDAD.
SQL
CREATE TABLE Siz_Incoming (
    INC_id INT IDENTITY(1,1) PRIMARY KEY, -- Identificador �nico de la inspecci�n de este material (auto-incrementable).
    INC_docNum INT NOT NULL, -- N�mero de la Nota de Entrada (NE) a la que pertenece este material.
    INC_fechaRecepcion DATETIME NOT NULL, -- Fecha en que el material fue recibido, seg�n la Nota de Entrada.
    INC_codProveedor VARCHAR(50) NOT NULL, -- C�digo del proveedor del material.
    INC_nomProveedor VARCHAR(100) NOT NULL, -- Nombre completo del proveedor.
    INC_numFactura VARCHAR(50) NULL, -- N�mero de factura del proveedor (puede ser nulo si no est� disponible de inmediato).
    INC_codMaterial VARCHAR(50) NOT NULL, -- C�digo interno del material que se est� inspeccionando.
    INC_nomMaterial VARCHAR(200) NOT NULL, -- Nombre descriptivo del material.
    INC_unidadMedida VARCHAR(20) NOT NULL, -- Unidad de medida del inventario (ej. PZA, KG, M).
    INC_cantRecibida NUMERIC(19,4) NOT NULL, -- Cantidad total de este material recibida en la Nota de Entrada.
    INC_cantAceptada NUMERIC(19,4) NULL, -- Cantidad de este material que fue aceptada despu�s de la inspecci�n (nulo hasta completar la inspecci�n).
    INC_cantRechazada NUMERIC(19,4) NULL, -- Cantidad de este material que fue rechazada despu�s de la inspecci�n (nulo hasta completar la inspecci�n).
    INC_fechaInspeccion DATETIME NULL, -- Fecha y hora en que se realiz� o actualiz� esta inspecci�n espec�fica del material.
    INC_codInspector VARCHAR(50) NULL, -- C�digo del usuario que realiz� la inspecci�n (inspector logeado).
    INC_nomInspector VARCHAR(100) NULL, -- Nombre del usuario que realiz� la inspecci�n.
    INC_notas VARCHAR(250) NULL, -- Observaciones generales o comentarios sobre esta inspecci�n de material.
    INC_esPiel CHAR(1) DEFAULT 'N' NOT NULL, -- Indica si el material es Piel ('S' para S�, 'N' para No), basado en GRUPO_MATERIAL=113.
    INC_borrado CHAR(1) DEFAULT 'N' NOT NULL, -- Indicador de borrado l�gico ('Y' si el registro est� borrado, 'N' si est� activo).
    INC_quienBorro VARCHAR(100) NULL, -- C�digo y nombre del usuario que marc� el registro como borrado.
    INC_creadoEn DATETIME DEFAULT GETDATE(), -- Fecha y hora de creaci�n del registro.
    INC_actualizadoEn DATETIME DEFAULT GETDATE() -- Fecha y hora de la �ltima actualizaci�n del registro.
);
GO

2. Siz_Checklist (Items del Checklist)
Esta es una tabla de cat�logo para almacenar los puntos del checklist de calidad (ej. Dimensiones, Acabado).
SQL
CREATE TABLE Siz_Checklist (
    CHK_id INT IDENTITY(1,1) PRIMARY KEY, -- Identificador �nico del item del checklist (auto-incrementable).
    CHK_descripcion VARCHAR(100) NOT NULL UNIQUE, -- Descripci�n del item del checklist (ej. 'Dimensiones', 'Acabado').
    CHK_activo CHAR(1) DEFAULT 'S' NOT NULL, -- Indica si el item del checklist est� activo ('S') o inactivo ('N').
    CHK_orden INT NULL -- N�mero para definir el orden de visualizaci�n de los items en la lista.
);
GO

-- Inserci�n de los items iniciales del checklist
INSERT INTO Siz_Checklist (CHK_descripcion, CHK_activo, CHK_orden) VALUES
('Dimensiones', 'S', 1),
('Acabado', 'S', 2),
('Espesor', 'S', 3),
('Densidad', 'S', 4),
('Elasticidad', 'S', 5),
('Adherencia', 'S', 6),
('Resistencia', 'S', 7),
('Gramage', 'S', 8),
('Templado', 'S', 9),
('Grietas', 'S', 10),
('Humedad', 'S', 11),
('Detalles visuales', 'S', 12),
('Funcionalidad', 'S', 13);
GO

3. Siz_IncomDetalle (Detalle de Inspecci�n)
Esta tabla almacenar� los resultados de cada punto del checklist para una inspecci�n de material espec�fica.
SQL
CREATE TABLE Siz_IncomDetalle (
    IND_id INT IDENTITY(1,1) PRIMARY KEY, -- Identificador �nico del detalle de la inspecci�n (auto-incrementable).
    IND_incId INT NOT NULL, -- Clave for�nea que referencia el 'INC_id' de la tabla 'Siz_Incoming' (la inspecci�n del material padre).
    IND_chkId INT NOT NULL, -- Clave for�nea que referencia el 'CHK_id' de la tabla 'Siz_Checklist' (el punto del checklist).
    IND_estado CHAR(1) DEFAULT 'A' NOT NULL, -- Estado del punto del checklist: 'C' (Cumple), 'N' (No Cumple), 'A' (No Aplica).
    IND_observacion VARCHAR(250) NULL, -- Observaci�n espec�fica para este punto del checklist.
    IND_borrado CHAR(1) DEFAULT 'N' NOT NULL, -- Indicador de borrado l�gico.
    IND_creadoEn DATETIME DEFAULT GETDATE(), -- Fecha y hora de creaci�n del registro.
    IND_actualizadoEn DATETIME DEFAULT GETDATE(), -- Fecha y hora de la �ltima actualizaci�n del registro.
    CONSTRAINT FK_Incoming_Detail FOREIGN KEY (IND_incId)
        REFERENCES Siz_Incoming(INC_id),
    CONSTRAINT FK_Checklist_Detail FOREIGN KEY (IND_chkId)
        REFERENCES Siz_Checklist(CHK_id)
);
GO
Notas:
* IND_incId es la clave for�nea correcta a Siz_Incoming.INC_id.
* IND_chkId es la clave for�nea correcta a Siz_Checklist.CHK_id.
* Cid_checkAprobado y Cid_checkRechazado se consolidan en IND_estado para mayor claridad y simplificaci�n.

4. Siz_PielClases (Clases de Piel)
Esta tabla registrar� las cantidades por clase de piel para los materiales que sean de este tipo (GRUPO_MATERIAL = 113).
SQL
CREATE TABLE Siz_PielClases (
    PLC_id INT IDENTITY(1,1) PRIMARY KEY, -- Identificador �nico del registro de clases de piel (auto-incrementable).
    PLC_incId INT NOT NULL, -- Clave for�nea que referencia el 'INC_id' de la tabla 'Siz_Incoming' (la inspecci�n del material piel padre).
    PLC_claseA NUMERIC(19,4) NULL, -- Cantidad de material de Clase A.
    PLC_claseB NUMERIC(19,4) NULL, -- Cantidad de material de Clase B.
    PLC_claseC NUMERIC(19,4) NULL, -- Cantidad de material de Clase C.
    PLC_claseD NUMERIC(19,4) NULL, -- Cantidad de material de Clase D (se mantuvo para consistencia, si aplica).
    PLC_borrado CHAR(1) DEFAULT 'N' NOT NULL, -- Indicador de borrado l�gico.
    PLC_creadoEn DATETIME DEFAULT GETDATE(), -- Fecha y hora de creaci�n del registro.
    PLC_actualizadoEn DATETIME DEFAULT GETDATE(), -- Fecha y hora de la �ltima actualizaci�n del registro.
    CONSTRAINT FK_Incoming_PielClasses FOREIGN KEY (PLC_incId)
        REFERENCES Siz_Incoming(INC_id)
);
GO
Cambios y Justificaci�n:
* PLC_incId es la clave for�nea correcta a Siz_Incoming.INC_id.
* Se eliminaron campos redundantes como fecha e inspector.

5. Siz_IncomImagen (Im�genes de Inspecci�n)
Una nueva tabla para almacenar la informaci�n de las im�genes de evidencia.
SQL
CREATE TABLE Siz_IncomImagen (
    IMG_id INT IDENTITY(1,1) PRIMARY KEY, -- Identificador �nico de la imagen (auto-incrementable).
    IMG_incId INT NOT NULL, -- Clave for�nea que referencia el 'INC_id' de la tabla 'Siz_Incoming' (la inspecci�n del material padre).
    IMG_ruta VARCHAR(255) NOT NULL, -- Ruta o URL donde se almacena la imagen en el servidor.
    IMG_descripcion VARCHAR(250) NULL, -- Descripci�n opcional de la imagen.
    IMG_cargadoPor VARCHAR(100) NULL, -- Usuario que carg� la imagen.
    IMG_cargadoEn DATETIME DEFAULT GETDATE(), -- Fecha y hora de la carga de la imagen.
    IMG_borrado CHAR(1) DEFAULT 'N' NOT NULL, -- Indicador de borrado l�gico.
    CONSTRAINT FK_Incoming_Images FOREIGN KEY (IMG_incId)
        REFERENCES Siz_Incoming(INC_id)
);
GO

