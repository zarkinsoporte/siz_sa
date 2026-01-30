-- =============================================
-- QUERY PARA VER DETALLE DE MATERIAL POR REVISAR
-- Reporte REP-05 Historial por Proveedor
-- =============================================

-- Este query muestra el material que ha sido RECIBIDO pero que aún no ha sido completamente INSPECCIONADO
-- La "Cantidad por Revisar" es la diferencia entre:
--   INC_cantRecibida - (INC_cantAceptada + INC_cantRechazada)
--
-- Esto puede ocurrir cuando:
-- 1. El material fue recibido pero aún no se ha realizado la inspección
-- 2. La inspección está parcialmente completa (inspecciones parciales)
-- 3. Los valores de INC_cantAceptada o INC_cantRechazada son NULL

-- =============================================
-- QUERY 1: RESUMEN POR PROVEEDOR Y PERÍODO
-- =============================================
SELECT 
    SIC.INC_codProveedor AS COD_PROVEEDOR,
    OCRD.CardName AS PROVEEDOR,
    CAST(SIC.INC_fechaInspeccion AS DATE) AS FECHA_INSPECCION,
    -- Totales
    SUM(SIC.INC_cantRecibida) AS TOTAL_RECIBIDO,
    SUM(ISNULL(SIC.INC_cantAceptada, 0)) AS TOTAL_ACEPTADO,
    SUM(ISNULL(SIC.INC_cantRechazada, 0)) AS TOTAL_RECHAZADO,
    SUM(SIC.INC_cantRecibida - ISNULL(SIC.INC_cantAceptada, 0) - ISNULL(SIC.INC_cantRechazada, 0)) AS TOTAL_POR_REVISAR,
    -- Porcentajes
    CASE 
        WHEN SUM(SIC.INC_cantRecibida) > 0 THEN
            CAST((SUM(ISNULL(SIC.INC_cantAceptada, 0)) * 100.0 / SUM(SIC.INC_cantRecibida)) AS DECIMAL(10,2))
        ELSE 0
    END AS PORC_ACEPTADO,
    CASE 
        WHEN SUM(SIC.INC_cantRecibida) > 0 THEN
            CAST((SUM(ISNULL(SIC.INC_cantRechazada, 0)) * 100.0 / SUM(SIC.INC_cantRecibida)) AS DECIMAL(10,2))
        ELSE 0
    END AS PORC_RECHAZADO,
    CASE 
        WHEN SUM(SIC.INC_cantRecibida) > 0 THEN
            CAST((SUM(SIC.INC_cantRecibida - ISNULL(SIC.INC_cantAceptada, 0) - ISNULL(SIC.INC_cantRechazada, 0)) * 100.0 / SUM(SIC.INC_cantRecibida)) AS DECIMAL(10,2))
        ELSE 0
    END AS PORC_POR_REVISAR
FROM Siz_Incoming SIC
INNER JOIN OCRD ON SIC.INC_codProveedor = OCRD.CardCode
WHERE 
    CAST(SIC.INC_fechaInspeccion AS DATE) BETWEEN '2024-01-01' AND '2024-12-31'  -- CAMBIAR FECHAS SEGÚN NECESITE
    AND SIC.INC_codProveedor = 'P2221'  -- CAMBIAR CÓDIGO DE PROVEEDOR SEGÚN NECESITE
    AND SIC.INC_borrado = 'N'
GROUP BY 
    SIC.INC_codProveedor,
    OCRD.CardName,
    CAST(SIC.INC_fechaInspeccion AS DATE)
ORDER BY 
    FECHA_INSPECCION DESC,
    PROVEEDOR;

-- =============================================
-- QUERY 2: DETALLE POR MATERIAL (Solo los que tienen pendiente por revisar)
-- =============================================
SELECT 
    SIC.INC_docNum AS NOTA_ENTRADA,
    SIC.INC_codProveedor AS COD_PROVEEDOR,
    OCRD.CardName AS PROVEEDOR,
    SIC.INC_codMaterial AS COD_MATERIAL,
    SIC.INC_nomMaterial AS MATERIAL,
    SIC.INC_unidadMedida AS UDM,
    CAST(SIC.INC_fechaInspeccion AS DATE) AS FECHA_INSPECCION,
    SIC.INC_fechaRecepcion AS FECHA_RECEPCION,
    -- Cantidades
    SIC.INC_cantRecibida AS CANT_RECIBIDA,
    ISNULL(SIC.INC_cantAceptada, 0) AS CANT_ACEPTADA,
    ISNULL(SIC.INC_cantRechazada, 0) AS CANT_RECHAZADA,
    (SIC.INC_cantRecibida - ISNULL(SIC.INC_cantAceptada, 0) - ISNULL(SIC.INC_cantRechazada, 0)) AS CANT_POR_REVISAR,
    -- Porcentajes
    CASE 
        WHEN SIC.INC_cantRecibida > 0 THEN
            CAST((ISNULL(SIC.INC_cantAceptada, 0) * 100.0 / SIC.INC_cantRecibida) AS DECIMAL(10,2))
        ELSE 0
    END AS PORC_ACEPTADO,
    CASE 
        WHEN SIC.INC_cantRecibida > 0 THEN
            CAST((ISNULL(SIC.INC_cantRechazada, 0) * 100.0 / SIC.INC_cantRecibida) AS DECIMAL(10,2))
        ELSE 0
    END AS PORC_RECHAZADO,
    CASE 
        WHEN SIC.INC_cantRecibida > 0 THEN
            CAST(((SIC.INC_cantRecibida - ISNULL(SIC.INC_cantAceptada, 0) - ISNULL(SIC.INC_cantRechazada, 0)) * 100.0 / SIC.INC_cantRecibida) AS DECIMAL(10,2))
        ELSE 0
    END AS PORC_POR_REVISAR,
    -- Estado
    CASE 
        WHEN ISNULL(SIC.INC_cantAceptada, 0) = 0 AND ISNULL(SIC.INC_cantRechazada, 0) = 0 THEN 'SIN INSPECCIONAR'
        WHEN (SIC.INC_cantRecibida - ISNULL(SIC.INC_cantAceptada, 0) - ISNULL(SIC.INC_cantRechazada, 0)) > 0.01 THEN 'INSPECCION PARCIAL'
        ELSE 'COMPLETAMENTE INSPECCIONADO'
    END AS ESTADO,
    -- Inspector
    SIC.INC_codInspector AS COD_INSPECTOR,
    SIC.INC_nomInspector AS INSPECTOR,
    SIC.INC_notas AS NOTAS
FROM Siz_Incoming SIC
INNER JOIN OCRD ON SIC.INC_codProveedor = OCRD.CardCode
WHERE 
    CAST(SIC.INC_fechaInspeccion AS DATE) BETWEEN '2024-01-01' AND '2024-12-31'  -- CAMBIAR FECHAS SEGÚN NECESITE
    AND SIC.INC_codProveedor = 'P2221'  -- CAMBIAR CÓDIGO DE PROVEEDOR SEGÚN NECESITE
    AND SIC.INC_borrado = 'N'
    -- Solo mostrar los que tienen material por revisar
    AND (SIC.INC_cantRecibida - ISNULL(SIC.INC_cantAceptada, 0) - ISNULL(SIC.INC_cantRechazada, 0)) > 0.01
ORDER BY 
    FECHA_INSPECCION DESC,
    CANT_POR_REVISAR DESC,
    MATERIAL;

-- =============================================
-- QUERY 3: RESUMEN POR NOTA DE ENTRADA
-- =============================================
SELECT 
    SIC.INC_docNum AS NOTA_ENTRADA,
    SIC.INC_codProveedor AS COD_PROVEEDOR,
    OCRD.CardName AS PROVEEDOR,
    CAST(SIC.INC_fechaInspeccion AS DATE) AS FECHA_INSPECCION,
    COUNT(DISTINCT SIC.INC_codMaterial) AS CANT_MATERIALES,
    -- Totales
    SUM(SIC.INC_cantRecibida) AS TOTAL_RECIBIDO,
    SUM(ISNULL(SIC.INC_cantAceptada, 0)) AS TOTAL_ACEPTADO,
    SUM(ISNULL(SIC.INC_cantRechazada, 0)) AS TOTAL_RECHAZADO,
    SUM(SIC.INC_cantRecibida - ISNULL(SIC.INC_cantAceptada, 0) - ISNULL(SIC.INC_cantRechazada, 0)) AS TOTAL_POR_REVISAR,
    -- Porcentajes
    CASE 
        WHEN SUM(SIC.INC_cantRecibida) > 0 THEN
            CAST((SUM(ISNULL(SIC.INC_cantAceptada, 0)) * 100.0 / SUM(SIC.INC_cantRecibida)) AS DECIMAL(10,2))
        ELSE 0
    END AS PORC_ACEPTADO,
    CASE 
        WHEN SUM(SIC.INC_cantRecibida) > 0 THEN
            CAST((SUM(ISNULL(SIC.INC_cantRechazada, 0)) * 100.0 / SUM(SIC.INC_cantRecibida)) AS DECIMAL(10,2))
        ELSE 0
    END AS PORC_RECHAZADO,
    CASE 
        WHEN SUM(SIC.INC_cantRecibida) > 0 THEN
            CAST((SUM(SIC.INC_cantRecibida - ISNULL(SIC.INC_cantAceptada, 0) - ISNULL(SIC.INC_cantRechazada, 0)) * 100.0 / SUM(SIC.INC_cantRecibida)) AS DECIMAL(10,2))
        ELSE 0
    END AS PORC_POR_REVISAR
FROM Siz_Incoming SIC
INNER JOIN OCRD ON SIC.INC_codProveedor = OCRD.CardCode
WHERE 
    CAST(SIC.INC_fechaInspeccion AS DATE) BETWEEN '2024-01-01' AND '2024-12-31'  -- CAMBIAR FECHAS SEGÚN NECESITE
    AND SIC.INC_codProveedor = 'P2221'  -- CAMBIAR CÓDIGO DE PROVEEDOR SEGÚN NECESITE
    AND SIC.INC_borrado = 'N'
GROUP BY 
    SIC.INC_docNum,
    SIC.INC_codProveedor,
    OCRD.CardName,
    CAST(SIC.INC_fechaInspeccion AS DATE)
HAVING 
    SUM(SIC.INC_cantRecibida - ISNULL(SIC.INC_cantAceptada, 0) - ISNULL(SIC.INC_cantRechazada, 0)) > 0.01
ORDER BY 
    TOTAL_POR_REVISAR DESC,
    FECHA_INSPECCION DESC;

-- =============================================
-- QUERY 4: ANÁLISIS DE VALORES NULL
-- =============================================
-- Este query ayuda a identificar registros con valores NULL que pueden causar discrepancias
SELECT 
    'Registros con cantAceptada NULL' AS TIPO,
    COUNT(*) AS CANTIDAD,
    SUM(SIC.INC_cantRecibida) AS TOTAL_RECIBIDO
FROM Siz_Incoming SIC
WHERE 
    CAST(SIC.INC_fechaInspeccion AS DATE) BETWEEN '2024-01-01' AND '2024-12-31'
    AND SIC.INC_codProveedor = 'P2221'
    AND SIC.INC_borrado = 'N'
    AND SIC.INC_cantAceptada IS NULL

UNION ALL

SELECT 
    'Registros con cantRechazada NULL' AS TIPO,
    COUNT(*) AS CANTIDAD,
    SUM(SIC.INC_cantRecibida) AS TOTAL_RECIBIDO
FROM Siz_Incoming SIC
WHERE 
    CAST(SIC.INC_fechaInspeccion AS DATE) BETWEEN '2024-01-01' AND '2024-12-31'
    AND SIC.INC_codProveedor = 'P2221'
    AND SIC.INC_borrado = 'N'
    AND SIC.INC_cantRechazada IS NULL

UNION ALL

SELECT 
    'Registros con ambas NULL' AS TIPO,
    COUNT(*) AS CANTIDAD,
    SUM(SIC.INC_cantRecibida) AS TOTAL_RECIBIDO
FROM Siz_Incoming SIC
WHERE 
    CAST(SIC.INC_fechaInspeccion AS DATE) BETWEEN '2024-01-01' AND '2024-12-31'
    AND SIC.INC_codProveedor = 'P2221'
    AND SIC.INC_borrado = 'N'
    AND SIC.INC_cantAceptada IS NULL
    AND SIC.INC_cantRechazada IS NULL

UNION ALL

SELECT 
    'Registros con suma mayor a recibido (ERROR)' AS TIPO,
    COUNT(*) AS CANTIDAD,
    SUM(SIC.INC_cantRecibida) AS TOTAL_RECIBIDO
FROM Siz_Incoming SIC
WHERE 
    CAST(SIC.INC_fechaInspeccion AS DATE) BETWEEN '2024-01-01' AND '2024-12-31'
    AND SIC.INC_codProveedor = 'P2221'
    AND SIC.INC_borrado = 'N'
    AND (ISNULL(SIC.INC_cantAceptada, 0) + ISNULL(SIC.INC_cantRechazada, 0)) > SIC.INC_cantRecibida;

-- =============================================
-- EXPLICACIÓN:
-- =============================================
-- 
-- CANTIDAD POR REVISAR = INC_cantRecibida - (INC_cantAceptada + INC_cantRechazada)
--
-- Esta cantidad representa material que:
-- 1. Fue RECIBIDO en el almacén (tiene INC_cantRecibida > 0)
-- 2. Aún NO ha sido completamente INSPECCIONADO
--
-- Escenarios comunes:
-- 
-- A) Material sin inspeccionar:
--    - INC_cantRecibida = 100
--    - INC_cantAceptada = NULL (0)
--    - INC_cantRechazada = NULL (0)
--    - Por Revisar = 100
--
-- B) Inspección parcial:
--    - INC_cantRecibida = 100
--    - INC_cantAceptada = 60
--    - INC_cantRechazada = 20
--    - Por Revisar = 20
--
-- C) Inspección completa:
--    - INC_cantRecibida = 100
--    - INC_cantAceptada = 80
--    - INC_cantRechazada = 20
--    - Por Revisar = 0
--
-- NOTA: Si la suma de aceptado + rechazado es MAYOR que recibido, 
--       hay un error en los datos (no debería ocurrir).
