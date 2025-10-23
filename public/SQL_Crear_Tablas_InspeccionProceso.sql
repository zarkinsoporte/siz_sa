-- Script para crear las tablas de Inspección en Proceso
-- Basado en la estructura de Siz_Incoming

USE [SIZ_DB] -- Ajustar el nombre de la base de datos según corresponda
GO

-- Tabla principal de Inspección en Proceso
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Siz_InspeccionProceso]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[Siz_InspeccionProceso](
    [IPR_id] [int] IDENTITY(1,1) NOT NULL,
    [IPR_op] [int] NOT NULL,
    [IPR_docEntry] [int] NOT NULL,
    [IPR_codArticulo] [nvarchar](50) NOT NULL,
    [IPR_nomArticulo] [nvarchar](250) NULL,
    [IPR_cantPlaneada] [decimal](19, 6) NULL DEFAULT ((0)),
    [IPR_cantInspeccionada] [decimal](19, 6) NULL DEFAULT ((0)),
    [IPR_cantRechazada] [decimal](19, 6) NULL DEFAULT ((0)),
    [IPR_centroInspeccion] [nvarchar](20) NOT NULL,
    [IPR_nombreCentro] [nvarchar](250) NULL,
    [IPR_fechaInspeccion] [datetime] NULL,
    [IPR_codInspector] [nvarchar](50) NULL,
    [IPR_nomInspector] [nvarchar](250) NULL,
    [IPR_observaciones] [nvarchar](max) NULL,
    [IPR_borrado] [char](1) NULL DEFAULT ('N'),
    [IPR_creadoEn] [datetime] NULL DEFAULT (getdate()),
    [IPR_actualizadoEn] [datetime] NULL DEFAULT (getdate()),
 CONSTRAINT [PK_Siz_InspeccionProceso] PRIMARY KEY CLUSTERED ([IPR_id] ASC)
)
END
GO

-- Tabla de detalle del checklist
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Siz_InspeccionProcesoDetalle]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[Siz_InspeccionProcesoDetalle](
    [IPD_id] [int] IDENTITY(1,1) NOT NULL,
    [IPD_iprId] [int] NOT NULL,
    [IPD_chkId] [int] NOT NULL,
    [IPD_estado] [char](1) NOT NULL,
    [IPD_cantidad] [decimal](19, 6) NULL DEFAULT ((0)),
    [IPD_observacion] [nvarchar](max) NULL,
    [IPD_borrado] [char](1) NULL DEFAULT ('N'),
    [IPD_creadoEn] [datetime] NULL DEFAULT (getdate()),
    [IPD_actualizadoEn] [datetime] NULL DEFAULT (getdate()),
 CONSTRAINT [PK_Siz_InspeccionProcesoDetalle] PRIMARY KEY CLUSTERED ([IPD_id] ASC),
 CONSTRAINT [FK_InspeccionProcesoDetalle_InspeccionProceso] FOREIGN KEY([IPD_iprId])
        REFERENCES [dbo].[Siz_InspeccionProceso] ([IPR_id])
)
END
GO

-- Tabla de imágenes de evidencia
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Siz_InspeccionProcesoImagen]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[Siz_InspeccionProcesoImagen](
    [IPI_id] [int] IDENTITY(1,1) NOT NULL,
    [IPI_iprId] [int] NOT NULL,
    [IPI_ruta] [nvarchar](500) NOT NULL,
    [IPI_descripcion] [nvarchar](250) NULL,
    [IPI_cargadoPor] [nvarchar](250) NULL,
    [IPI_cargadoEn] [datetime] NULL DEFAULT (getdate()),
    [IPI_borrado] [char](1) NULL DEFAULT ('N'),
 CONSTRAINT [PK_Siz_InspeccionProcesoImagen] PRIMARY KEY CLUSTERED ([IPI_id] ASC),
 CONSTRAINT [FK_InspeccionProcesoImagen_InspeccionProceso] FOREIGN KEY([IPI_iprId])
        REFERENCES [dbo].[Siz_InspeccionProceso] ([IPR_id])
)
END
GO

-- Crear índices para mejorar el rendimiento
CREATE NONCLUSTERED INDEX [IX_InspeccionProceso_OP] ON [dbo].[Siz_InspeccionProceso]
(
    [IPR_op] ASC,
    [IPR_centroInspeccion] ASC,
    [IPR_borrado] ASC
)
GO

CREATE NONCLUSTERED INDEX [IX_InspeccionProcesoDetalle_IPR] ON [dbo].[Siz_InspeccionProcesoDetalle]
(
    [IPD_iprId] ASC,
    [IPD_borrado] ASC
)
GO

CREATE NONCLUSTERED INDEX [IX_InspeccionProcesoImagen_IPR] ON [dbo].[Siz_InspeccionProcesoImagen]
(
    [IPI_iprId] ASC,
    [IPI_borrado] ASC
)
GO

PRINT 'Tablas de Inspección en Proceso creadas exitosamente.'
GO

